<?php

namespace App\Traits;

// namespace Illuminate\Foundation\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;
use App\Notifications\AccountActivation as AccountActivation;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Mail;
use Notification;
use App\Traits\ApiResponser;

trait AccountActivations
{
    // use RedirectsUsers;
    // use SendMailService;
    use ApiResponser;

    /**
     * @lrd:start
     *  To account activation request
     * @lrd:end
     *
     *@QAparam email string required email|unique:users
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function accountActivation(Request $request)
    {
        $email = $request->input('email') ?? $request->email;
        $reqtype = $request->input('type');
        $token = Str::random(60);
        if($reqtype && ($reqtype == "forget" || $reqtype == "changepassword")){
            if($email!=""){
                $user = User::where('email', $email)->first();
                if ($user) {
                    if($user->status ==1){
                        $username = $user->name;

                        $password_reset = DB::table('password_resets')->where('email', $user->email)->first();

                        if($password_reset){
                            DB::table('password_resets')->update(['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()]);
                        }else{
                                DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()]);
                        }

                        $user->reset_status = 0;
                        $user->email_generated_at = now();
                        $user->save();
                        $subject = 'Reset Passwor';
                        $description = 'We have received a request to reset your password for the Employee Portal. Click the link below to reset your password.';
                        $actionText = 'Click here to reset your password';
                        $expiryText = 'Please note that this link will expire in '.config('constant.EMAIL_EXPIRY_IN_HOURS').' hours';
                        //send mail template to the user for resetting the password
                        Notification::send($user, new AccountActivation(['email' => $email, 'username' => $username,'token' => $token,'subject' => $subject,'description' => $description,'actionText' => $actionText,'actionType' => $reqtype,'expiryText'=>$expiryText]));

                        return $this->response('Mail sent',Response::HTTP_OK,'Mail send');
                    }else{
                        return $this->response('This account is no longer active in the system',Response::HTTP_BAD_REQUEST,'Your account is inactive');
                    }
                }else{
                    return $this->response('User does not exists',Response::HTTP_BAD_REQUEST,'User does not exists');
                }
            }else{
                return $this->response('Required error',Response::HTTP_BAD_REQUEST,'Required error');
            }
        }else{
            if($email!=""){
                $user = User::where('email', $email)->first();
                if ($user) {

                    $username = $user->name;

                    $password_reset = DB::table('password_resets')->where('email', $user->email)->first();

                    if($password_reset){
                        DB::table('password_resets')->update(['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()]);
                    }else{
                        DB::table('password_resets')->insert(['email' => $user->email, 'token' => $token, 'created_at' => Carbon::now()]);
                    }

                    $user->reset_status = 0;
                    $user->save();

                    $subject = 'Account Activation';
                    $actionText = 'Click here to create your password';
                    $description = 'Welcome to the Employee Portal. Kindly activate your account by clicking the link below to create your password. Please note that your account will be activated once you login to the portal with your password.';
                    $expiryText = '';
                    //send mail template to the user for resetting the password
                    Notification::send($user, new AccountActivation(['email' => $email, 'username' => $username,'token' => $token,'subject' => $subject,'description' => $description,'actionText' => $actionText,'actionType' => 'activation','expiryText'=>$expiryText]));

                    return $this->response('Mail send',Response::HTTP_OK,'Mail send');

                }else{
                    return $this->response('User does not exists',Response::HTTP_BAD_REQUEST,'User does not exists');
                }
            }else{
                return $this->response('Required error',Response::HTTP_BAD_REQUEST,'Required error');
            }
        }


    }

    public function activationUrl($email, $token)
    {

        return config('app.mailurl')."/account/activation/".$token."?email=".$email;

    }


    /**
     * Reset the given user's password.
     *
     * @lrd:start
     *  To set password activation request
     * @lrd:end
     *
     *@QAparam email string required email|unique:users
     *@QAparam password string required password
     *@QAparam token string required 218ef1iJenTC7b5SdTzOGUrFWi7n2DCx3ogaOAB5
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function setPassword(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $password_confirmation = $request->input('passwordConfirmation');
        $token = $request->input('token');
        if($email!="" &&  $token!="" &&  $password!="" && $password_confirmation !=""){
            if($password==$password_confirmation){
        // Here we will attempt to reset the user's password. If it is successful we
            // will update the password on an actual user model and persist it to the
            // database. Otherwise we will parse the error and return the response.
            $user = User::where('email',$email)->first();

            $this->resetPassword($user, $password);

            // If the password was successfully reset, we will redirect the user back to
            // the application's home authenticated view. If there is an error we can
            // redirect them back to where they came from with their error message.
            return $this->response('Successfully updated',Response::HTTP_OK,'Successfully updated');
            }else{
                return $this->response('Password does not match',Response::HTTP_BAD_REQUEST,'Password does not match');
            }

        }else{
            return $this->response('Required error',Response::HTTP_BAD_REQUEST,'Required error');
        }

    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);
        #Activate account
        $this->setAccountActive($user);
        $this->setEmailVerifiedAt($user);
        $this->setRestStatus($user);
        $user->reset_status = 1;
        $user->email_generated_at = null;
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
        $this->guard()->login($user);
    }

    /**
     * Set the user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $user->password = Hash::make($password);
    }

     /**
     * Set the user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function setAccountActive($user)
    {
        $user->status = 1;
    }

     /**
     * Set the user's email verified at time.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function setEmailVerifiedAt($user)
    {
        $user->email_verified_at = Carbon::now();
    }

     /**
     * Set the user's email verified at time.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function setRestStatus($user)
    {
        $user->reset_status = 1;
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['message' => trans($response)], 200);
        }

        return redirect($this->redirectPath())
                            ->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                'email' => [trans($response)],
            ]);
        }

        return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }


}
