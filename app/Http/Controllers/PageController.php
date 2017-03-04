<?php

namespace App\Http\Controllers;
use App\Mail\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
class PageController extends Controller
{
    // Send Contact form
    public function contact(Request $request) {
        try {
            if (Mail::to(Config('mail.emails.reservation'))
            ->cc(Config('mail.emails.info'))
            ->send(new Contact($request->input()))) {
                return response()->json('Your message was sent successfully. Thanks.', 200, [], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json('Failed to send your message. Please try later or contact the administrator by another method.', 400, [], JSON_UNESCAPED_UNICODE);
            }
        } catch (\Swift_TransportException  $e) {
            return response()->json('Failed to send your message. Please try later or contact the administrator by another method.', 400, [], JSON_UNESCAPED_UNICODE);
        }
    }
}
