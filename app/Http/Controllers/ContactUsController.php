<?php

namespace App\Http\Controllers;

use App\Models\CompanyContactUs;
use App\Models\FcmToken;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class ContactUsController extends Controller
{
    public function store(Request $request)
    {
      
        $request->validate([
            'contact_id' => 'required|exists:company_contact_us,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'contact_id' => $request->contact_id,
            'message' => $request->message,
            'sender' => $request->sender,
            'user_id' => Auth::id(),
        ]);
        $findCompany = CompanyContactUs::where('id', $request->contact_id)->first();
        $company = $findCompany->company_id;
        $findDriver = User::find($company);
        $companyFcmTokens = FcmToken::where('user_id', $company)->first();
        $factory = (new Factory)->withServiceAccount(storage_path('app/zeroifta.json'));
        $messaging = $factory->createMessaging();
        if (!empty($companyFcmTokens)) {
            $message = CloudMessage::new()
                ->withNotification(Notification::create('New Message','A new message has been received.'))
                ->withData([
                   
                    'driver_name' => $findDriver->name,
                    'sound' => 'default',
                ]);

            $messaging->sendMulticast($message, $companyFcmTokens->token);
        }
        
        return response()->json(['success' => 'Message sent']);
    }

    public function fetchMessages($contact_id)
    {
        $messages = Message::where('contact_id', $contact_id)->orderBy('created_at', 'asc')->get();
        return response()->json($messages);
    }
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        $message->is_read = true;
        $message->save();

        return response()->json(['success' => 'Message marked as read']);
    }
}
