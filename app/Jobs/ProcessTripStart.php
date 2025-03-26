<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\FuelStation;
use App\Models\FcmToken;
use App\Models\CompanyDriver;
use App\Models\ModelsNotification;
use App\Models\Notification as AppModelsNotification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessTripStart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fuelStations;
    protected $trip;
    protected $validatedData;

    /**
     * Create a new job instance.
     */
    public function __construct($fuelStations, $trip, $validatedData)
    {
        $this->fuelStations = $fuelStations;
        $this->trip = $trip;
        $this->validatedData = $validatedData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Insert Fuel Stations in Bulk
        FuelStation::insert($this->fuelStations);

        // Fetch driver
        $findDriver = User::find($this->trip->user_id);
        if (!$findDriver) {
            return;
        }

        // Find company associated with driver
        $findCompany = CompanyDriver::where('driver_id', $findDriver->id)->first();
        if (!$findCompany) {
            return;
        }

        // Fetch FCM tokens
        $driverFcm = FcmToken::where('user_id', $findDriver->id)->first();
        $companyFcmTokens = FcmToken::where('user_id', $findCompany->company_id)->first();

        // Firebase Notification Factory
        $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
        $messaging = $factory->createMessaging();

        //Send Notification to Company
        if (!empty($companyFcmTokens)) {
            $message = CloudMessage::new()
                ->withNotification(Notification::create('Trip Started', $findDriver->name . ' has started a trip.'))
                ->withData([
                    'trip_id' => (string) $this->trip->id,
                    'driver_name' => $findDriver->name,
                    'sound' => 'default',
                ]);

            $messaging->sendMulticast($message, $companyFcmTokens->token);
        }

        // Send Notification to Driver
        if (!empty($driverFcm)) {
            $message = CloudMessage::new()
                ->withNotification(Notification::create('Trip Started', 'Trip started successfully'))
                ->withData([
                    'sound' => 'default',
                ]);

            $messaging->sendMulticast($message, $driverFcm->token);

            // Store Notification in Database
            AppModelsNotification::create([
                'user_id' => $findCompany->company_id,
                'title' => 'Trip Started',
                'body' => $findDriver->name . ' has started a trip.',
            ]);
        }
    }
}
