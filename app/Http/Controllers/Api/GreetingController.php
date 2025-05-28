<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallRecording;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GreetingController extends Controller
{
    public function transcript()
    {
        $user_id =Auth::user()->id;
        $voicemail_details = CallRecording::where('user_id', $user_id)->get()->map(function ($item) {
            return [
                'date'       => $item->created_at->toDateString(),
                'start_time' => $item->created_at->format('h:i A'),
                'Asr'        => gmdate("i:s", $item->recording_duration),
                'Phone'      => $item->from_number,
                'Audio'      => $item->recording_url,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Voicemail details fetched successfully.',
            'data'    => $voicemail_details
        ]);
    }

    public function post_greetings(){


        
    }
}

