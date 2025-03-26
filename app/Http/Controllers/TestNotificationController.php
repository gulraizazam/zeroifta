<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FcmService;
use Illuminate\Support\Facades\DB;

class TestNotificationController extends Controller
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function sendTestNotification(Request $request)
    {
        $deviceToken = 'cB2U-Fq5Q16SSb_3ruYUfJ:APA91bHCv0ZWxtEtZDBun-Ly7n6qEweSMrmsNnFlPEwqbw3BIZoMwyRYnvDz1OpcQChhXnQJiuuwI-Wvx3zzS1QwX_JF1Me6Ittz7tolwk9ZVxbJGMnNyD8'; // Replace with actual FCM token.
        $title = 'Test Notification';
        $body = 'This is a test push notification.';

        $response = $this->fcmService->sendNotification($deviceToken, $title, $body);

        return response()->json(['response' => $response]);
    }
    public function getCompanyByDriver(Request $request) {
        $driver_id = $request->driver_id;
        $company = DB::table('company_drivers')->where('driver_id', $driver_id)->first();
        return response()->json(['company_id' => $company ? $company->company_id : null]);
    }
    public function getCompanyFCMTokens(Request $request) {
        $company_id = $request->company_id;
        $tokens = DB::table('fcm_tokens')->where('user_id', $company_id)->pluck('token')->toArray();
        return response()->json(['tokens' => $tokens]);
    }
}
