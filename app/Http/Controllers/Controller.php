<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Mail\ResetPassowrdMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function sendResetPasswordMail($user){
        $encryptedEmailAddress = Crypt::encryptString($user['email']);
        $user['encryptedEmail'] = $encryptedEmailAddress;
        Mail::to((object) $user)->cc($user['sender']->email)->queue(new ResetPassowrdMail($user));
    }
}
