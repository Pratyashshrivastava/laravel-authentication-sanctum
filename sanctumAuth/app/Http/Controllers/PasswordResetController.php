<?php

namespace App\Http\Controllers;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordReset; 
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Mail\Message;


class PasswordResetController extends Controller
{
    public function send_reset_password_email(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();
        if(!$user) {
            return response([
                'message' => 'Email not found',
                'status' => 'failed'
            ]);
        }

        $token = Str::random(60);

        PasswordReset::create(
            [
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // dump("http://127.0.0.1:3000/api/user/reset/". $token);

        Mail::send('reset', ['token' => $token], function (Message $message) use ($request) {
            $message->subject('Reset Password Notification');
            $message->to($request->email);
        });

        return response([
            'message' => 'Email sent successfully',
            'status' => 'success'
        ]);
        
    }

    public function reset(Request $request, $token)
    {
        $formattedToken = Carbon::now()->subMinutes(1)
        ->toDateTimeString();
        PasswordReset::where('created_at', '<=', $formattedToken)->delete();
        $request->validate([
            'password' => 'required|confirmed'
        ]);

        $passwordReset = PasswordReset::where('token', $token)->first();
        if(!$passwordReset) {
            return response([
                'message' => 'Invalid token',
                'status' => 'failed'
            ]);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if(!$user) {
            return response([
                'message' => 'User not found',
                'status' => 'failed'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $passwordReset->delete();

        return response([
            'message' => 'Password reset successfully',
            'status' => 'success'
        ]);
    }
}
