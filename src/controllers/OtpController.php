<?php

namespace tpaksu\LaravelOTPLogin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use tpaksu\LaravelOTPLogin\Controllers\Controller;
use tpaksu\LaravelOTPLogin\Models\OneTimePassword;

/**
 * Class for handling OTP view display and processing
 */
class OtpController extends Controller
{
    /**
     * Shows the OTP login screen
     *
     * @return View/Redirector
     */
    public function view()
    {
        // this route is protected by WEB and AUTH middlewares, but still, this check can be useful.
        if (Auth::check()) {
            // Check if user has already made a OTP request with a "waiting" status
            $otp = $this->getLastPendingOTP(Auth::user());
            if ($otp instanceof OneTimePassword) {
                // show the OTP validation form
                return view('laravel-otp-login::otpvalidate');
            } else {
                // the user hasn't done a request, why is he/she here? redirect back to login screen.
                Auth::logout();
                return redirect('/')->withErrors(["username" => __("laravel-otp-login::messages.otp_expired")]);
            }
        } else {
            // the user hasn't tried to log in, why is he/she here? redirect back to login screen.
            return redirect('/')->withErrors(["username" => __("laravel-otp-login::messages.unauthorized")]);
        }
    }

    private function getLastPendingOTP($user)
    {
        return OneTimePassword::where([
            "user_id" => $user->id,
            "status" => "waiting",
        ])->orderByDesc("created_at")->first();
    }

    /**
     * Checks the given OTP
     *
     * @param Request $request
     * @return Redirector
     */
    public function check(Request $request)
    {
        // if user has been logged in
        if (Auth::check()) {

            // get the user for querying the verification code
            $user = Auth::user();

            // check if current request has a verification code
            if ($request->has("code")) {

                // get the code entered by the user to check
                $code = $request->input("code");

                // get the waiting verification code from database
                $otp = $this->getlastPendingOTP($user);

                // if the code exists
                if ($otp instanceof OneTimePassword) {

                    // compare it with the received code
                    if ($otp->checkPassword($code)) {

                        // the codes match, set a cookie to expire in one year
                        setcookie("otp_login_verified", "user_id_" . $user->id, time() + (365 * 24 * 60 * 60), "/", "", false, true);

                        // set the code status to "verified" in the database
                        $otp->acceptEntrance();

                        return redirect()->intented();
                    } else {
                        // the codes don't match, return an error.
                        return redirect(route("otp.view"))->withErrors(["code" => __("laravel-otp-login::messages.code_mismatch")]);
                    }
                } else {
                    // the code doesn't exist in the database, return an error.
                    return redirect(route("login"))->withErrors(["phone" => __("laravel-otp-login::messages.otp_expired")]);
                }
            } else {
                // the code is missing, what should we compare to?
                return redirect(route("otp.view"))->withErrors(["code" => __("laravel-otp-login::messages.code_missing")]);
            }
        } else {
            // why are you here? we don't have anything to serve to you.
            return redirect(route("login"));
        }
    }
}
