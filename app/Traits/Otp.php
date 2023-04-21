<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Events\SendingEmailEvent;

trait Otp
{
    /**
    * @lrd:start
    *  Resending Login OTP
    * @lrd:end
    *
    * @QAparam username string required email|unique:users
    **/
    public function resendOtp(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
            ]);

            if ($validator->fails())
            {
                // Return failed validation message
                return $this->response($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            }
            $user = User::where('email', '=', $request->email)->first();
            if($user) {
                // Sending Otp
                event(new SendingEmailEvent($user));
                return $this->response('Mail sent',Response::HTTP_OK);
            } else {
                return $this->response('Email not found', Response::HTTP_BAD_REQUEST);
            }

        }catch (Exception $e) {
            ////return error message
            return $this->response("Internal Server Error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
    * @lrd:start
    *  Verify Login OTP
    * @lrd:end
    *
    * @QAparam top string required Example 7878
    * @QAparam username string required email|unique:users
    *
    * @return status object return with messages
    **/
    public function verifyOtp(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                    'otp' => 'required|int',
                    'email' => 'required|string|email|max:255',
            ]);

            if ($validator->fails())
            {
                // Return failed validation message
                return $this->response($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('email', $request->email)->first();

            $message = 'Please Enter Valid OTP';
            $code = Response::HTTP_BAD_REQUEST;

            //OTP verfication Success
            $dbtimestamp = strtotime($user->otp_generated_at);
            $otpExpiryInSec = config('constant.OTP_EXPIRY_IN_SEC');
            $data = [];

            if (in_array($user->email, explode(",", config('constant.automation_user')))) {
                //Skip the OTP validation for automation users
                $message = 'Access granted';
                $data = json_decode($user->token_response, true);
                $code = Response::HTTP_OK;
            } elseif (time() - $dbtimestamp > $otpExpiryInSec) {
                // 3 mins has passed
                $message = 'OTP expired';
                $code = Response::HTTP_UNAUTHORIZED;
            } elseif ($user && $request->otp == $user->otp) {
                $message = 'OTP created at fetch successfully.';
                $code = Response::HTTP_OK;
                $data = json_decode($user->token_response, true);
                # Update OTP
                User::where('email', $user->email)->update(['otp' => null, 'otp_generated_at' => null]);
            }

            return $this->response($message, $code, $data);
        } catch (Exception $e) {
            //return error message
            return $this->response("Internal Server Error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Reset Set OTP null
     *
     * @lrd:start
     *  Reset OTP When its expired
     * @lrd:end
     *
     * @QAparam username string required email|unique:users
     */
    public function resetOtp(Request $request) {
        User::where('email', $request->email)->update(['otp' => null, 'otp_generated_at' => null]);
        return $this->response('OTP Cleared Succuessfully', Response::HTTP_OK);
    }

     /**
    * @lrd:start
    *  Get OTP expire time
    * @lrd:end
    *
    * @QAparam username string required email|unique:users
    *
    * @return status object return with messages
    **/
    public function getOtpGeneratedAt(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                    'email' => 'required|string|email|max:255',
            ]);
            if ($validator->fails())
            {
                # Return failed validation message
                return $this->response($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('email', $request->email)->first();
            $data = [];
            if (in_array($user->email, explode(",", config('constant.automation_user')))) {
                //Skip the OTP validation for automation users
                $message = 'Access granted';
                $code = Response::HTTP_OK;
                $data = [
                    'otp_generated_at' => date('Y-m-d h:m:s'),
                ];
            } elseif (!empty($user) && !empty($user->otp_generated_at)) {
                $message = 'Access granted';
                $code = Response::HTTP_OK;
                $data = [
                    'otp_generated_at' => $user->otp_generated_at,
                ];
            } elseif (!empty($user) && empty($user->otp_generated_at)) {
                $message = 'Session expired. Please authendicate your credential for otp send.';
                $code = Response::HTTP_UNAUTHORIZED;
                $data = [
                    'otp_session_expired' => true
                ];
            } else {
                $message = 'OTP expired. Please resent & enter the new otp to verify';
                $code = Response::HTTP_UNAUTHORIZED;
                $data = [
                    'otp_expired' => true
                ];
            }
            return $this->response($message, $code, $data);
        } catch (Exception $e) {
            # return error message
            return $this->response("Internal Server Error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
