<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class EmailVerificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    private $otp;
    public function __construct(){
        $this->otp = new Otp;
    }
    public function store(Request $request)
    {
        $otp2 = $this->otp->validate($request->email,$request->code );
        if(!$otp2->status)
        {
            return response()->json([
                'error' => $otp2,

            ],401);

        }
        $user = User::where('email', $request->email)->first();
        $user->update(['email_verified_at' => now()]);
        $user->save();

}
}
