<?php

namespace App\Http\Controllers\Oauth;

use App\Events\SendingEmailEvent;
use App\Models\User;
use App\Services\RoleService;
use App\Traits\AccountActivations;
use App\Traits\ApiResponser;
use App\Traits\Otp;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Exceptions\OAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class AccessTokenController extends PassportAccessTokenController
{
    use ApiResponser;
    use AccountActivations;
    use Otp;

    /**
     * @lrd:start
     *  To Generate Authentication token and redirect to Dashboard if success
     * @lrd:end
     *
     * @QAparam grant_type string required password
     * @QAparam client_id integer required Example 2
     * @QAparam client_secret string required 218ef1iJenTC7b5SdTzOGUrFWi7n2DCx3ogaOAB5
     * @QAparam username string required email|unique:users
     * @QAparam password string required password
     *
     */
    //
    public function issueToken(ServerRequestInterface $request)
    {
        try {
            //validate the request
            $validator = Validator::make($request->getParsedBody(), [
                'grant_type' => "required",
                'client_id' => "required",
                'client_secret' => "required",
                'username' => "required",
                'password' => "required",
            ]);
            if ($validator->fails()) {
                # Return failed validation message
                return $this->response($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            }
            # get username (default is :email)
            $username = $request->getParsedBody()['username'];

            # get user
            $user = User::where('email', '=', $username)->first();

            # If user does not exist throw an exception
            if (!$user) {
                throw new ModelNotFoundException("User with this email does not exists");
            }

            //Check if user is activated or not
            if ($user->status != 1) {
                if ($user->status == 0) {
                    //return error message
                    return $this->response('User Is Inactive', Response::HTTP_BAD_REQUEST);
                } else {
                    //return error message
                    return $this->response('This account is no longer active in the system', Response::HTTP_BAD_REQUEST);
                }

            }

            # generate token
            $tokenResponse = parent::issueToken($request);

            # convert response to json string
            $content = $tokenResponse->getContent();

            # convert json to array
            $data = json_decode($content, true);
            $data['userId'] = $user->id;
            $data['userType'] = $user->user_type;
            $data['otp_sent'] = true;

            if (isset($data["error"])) {
                throw new OAuthServerException('The user credentials were incorrect.', 6, 'invalid_credentials', 401);
            }

            # save token response
            if (!empty($user)) {
                $user->token_response = json_encode($data);
                $user->save();
            }

            $authResponse = $this->response('Access granted', Response::HTTP_OK, $data);
            # Skip the OTP check for automation users
            if (!in_array($user->email, explode(",", config('constant.automation_user')))) {
                //Sending OTPEmail
                event(new SendingEmailEvent($user));
                $authResponse = $this->response(
                    'Verified and otp sent to email successfully',
                    Response::HTTP_OK,
                    ['otp_sent' => true]
                );
            }
            return $authResponse;
        } catch (ModelNotFoundException $e) { // email not found
            # return error message
            return $this->response('Incorrect email or password.', Response::HTTP_NOT_FOUND);
        } catch (OAuthServerException $e) { //password not correct..token not granted
            # return error message
            return $this->response('Incorrect email or password.', Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            # return error message
            return $this->response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    /**
     * Refreshes an access token using a refresh_token provided during access token
     * @lrd:start
     *  To efreshes an access token using a refresh_token provided during access token
     * @lrd:end
     *
     * @QAparam grant_type string required password
     * @QAparam client_id integer required Example 2
     * @QAparam client_secret string required 218ef1iJenTC7b5SdTzOGUrFWi7n2DCx3ogaOAB5
     * @QAparam refresh_token string required 218ef1iJenTC7b5SdTzOGUrFWi7n2DCx3ogaOAB5
     * @param \Psr\Http\Message\ServerRequestInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(ServerRequestInterface $request)
    {

        try {
            //validate the request
            $validator = Validator::make($request->getParsedBody(), [
                'grant_type' => "required",
                'client_id' => "required",
                'client_secret' => "required",
                'refresh_token' => "required",
            ]);
            if ($validator->fails()) {
                //Return failed validation message
                return $this->response($validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            //generate token
            $tokenResponse = parent::issueToken($request);

            //convert response to json string
            $content = $tokenResponse->getContent();

            //convert json to array
            $data = json_decode($content, true);

            if (isset($data["error"])) {
                throw new OAuthServerException($data["error"], $tokenResponse);
            }

            return $this->response('Token refreshed.', Response::HTTP_OK, $data);
        } catch (OAuthServerException $e) {
            //password not correct..token not granted
            //return error message
            return $this->response('BAD Request', Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            //return error message
            return $this->response('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @lrd:start
     *  Logout the application
     * @lrd:end
     *
     */
    public function removeToken(Request $request)
    {
        // Revoke access token
        $request->user()->token()->revoke();
        // Revoke all of the token's refresh tokens
        // $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        // $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($request->user()->token()->id);
        return $this->response('Successfully logged out.', Response::HTTP_OK);

    }

    /**
     * Revoke the token
     * @param {obj} $request - request data
     */
    public function revokeTokenById(Request $request)
    {
        # Revoke access token
        $request->user()->token()->revoke();
        return true;

    }

    /**
     * @lrd:start
     *  Check reset passwork link status
     * @lrd:end
     *
     *@QAparam email string required email|unique:users
     *@QAparam token string required 218ef1iJenTC7b5SdTzOGUrFWi7n2DCx3ogaOAB5
     */
    public function checkReset(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');
        $actionType = $request->input('actionType');
        $reset_status = 0;
        if ($email != "") {
            $user = User::where('email', $email)->whereNotIn('status', [2, 3])->first();
            if ($user) {
                if($actionType === 'forget' || $actionType === 'changepassword'){
                    //Get email generate
                    $emailGeneratedAt = strtotime($user->email_generated_at);
                    $expiryHours = config('constant.EMAIL_EXPIRY_IN_HOURS');
                    $emailGeneratedAtExpiry = date("Y-m-d H:i:s", strtotime('+'.$expiryHours.' hours', $emailGeneratedAt));
                    $now = date("Y-m-d H:i:s");
                    if($now < $emailGeneratedAtExpiry){
                        if ($user->reset_status == 1) {

                            $reset_status = $user->reset_status;
        
                        } else {
                            $password_resets = DB::table('password_resets')->where('email', $email)->orderBy('created_at', 'DESC')->first();
                            if ($password_resets->token != $token) {
                                $reset_status = 1;
                            } else {
                                $reset_status = 0;
                            }
                        }
                    }else{
                        $reset_status = 1;
                    }
                }else{
                    if ($user->reset_status == 1) {

                        $reset_status = $user->reset_status;
    
                    } else {
                        $password_resets = DB::table('password_resets')->where('email', $email)->orderBy('created_at', 'DESC')->first();
                        if ($password_resets->token != $token) {
                            $reset_status = 1;
                        } else {
                            $reset_status = 0;
                        }
                    }
                }

            } else {
                $reset_status = 1;
            }
        }
        return $this->response('sucess', Response::HTTP_OK, $reset_status);
    }

    /**
     * @lrd:start
     *  To Generate One time password Token
     * @lrd:end
     *
     * @QAparam email string required email|unique:users
     *
     */
    public function generateOtp()
    {
        try {

            //validate the request
            $validator = Validator::make($request->getParsedBody(), [
                'email' => "required",
            ]);
            if ($validator->fails()) {
                //Return failed validation message
                return $this->response($validator->messages()->first(), Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $e) {
            ////return error message
            return $this->response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @lrd:start
     *  To Generate change password link status
     * @lrd:end
     *
     * @QAparam email string required email
     *
     */
    public function changePassword(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $user = User::where('id', $userId)->first();
            $userEmail =  $request->email ?? "";
            if (!empty($user)) {
                $request->request->add([ "email" => $user->email]);
                $userEmail = $user->email;
            }
            $this->accountActivation($request);
            $response =  $this->revokeTokenById($request);
            return $this->response('Successfully logged out.', Response::HTTP_OK, ['user_email' => $userEmail]);
        } catch (Exception $e) {
            return $this->response("Internal Server Error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @lrd:start
     *  To Generate change password link status
     * @lrd:end
     *
     * @QAparam user_id int required 1
     *
     */
    public function userPermission(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            if ($user_id != "") {
                $user = User::where('id', $user_id)->first();
                if ($user) {

                    $roleService = new RoleService;
                    return $roleService->getUserPermissions($request);
                    return $this->response('sucess', Response::HTTP_OK, $user);
                } else {
                    return $this->response('User not found', Response::HTTP_BAD_REQUEST, []);
                }
            } else {
                return $this->response('Required Error', Response::HTTP_BAD_REQUEST, []);
            }

        } catch (Exception $e) {
            return $this->response("Internal Server Error", Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @lrd:start
     *  Get User data by its id
     * @lrd:end
     *
     * @QAparam userId int required 2
     * @QAparam secret int required base64:xxxx
     *
     * @return $user{array} user details
     */
    public function getUserByType($userId, $secret)
    {
        $user = [];
        if ($userId && $secret == config('constant.ACCEPTED_SECRETS')) {
            $user = User::where('id', $userId)->first()->user_type;
        }
        else {
            return $this->response('Unauthorized Access', Response::HTTP_UNAUTHORIZED);
        }
        return $this->response('sucess', Response::HTTP_OK, $user);
    }
}
