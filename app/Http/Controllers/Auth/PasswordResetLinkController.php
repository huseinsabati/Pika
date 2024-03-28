<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetpasswordRequest;



class PasswordResetLinkController extends Controller
{
    /**
     * Send a new email verification notification.
     */

    private $otp;
    public function __construct(){
        $this->otp = new Otp;
    }

    public function reset(ResetpasswordRequest $request)
    {
        $otp2 = $this->otp->validate($request->email,$request->code );
        if(!$otp2->status)
        {
            return response()->json([
                'error' => $otp2,

            ],401);

        }
        $user = User::where('email', $request->email)->first();
        $user->update(['password' => $request->password]);

}
}
