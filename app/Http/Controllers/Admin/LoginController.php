<?php

namespace App\Http\Controllers\Admin;

use App\EmailTemplate;
use App\ParticipantEmailChange;
use Auth;
use App\User;
use Crypto;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Mail;
use Session;
use Token;
use Aws\Laravel\AwsServiceProvider;

class LoginController extends Controller
{

    public function index(Request $request)
    {

        if (Auth::check()) {
            return redirect()->intended(admin_url('dashboard'));
        }
        $data = [];
        // if($token) {
        //     $participant_email_changes = DB::table('participant_email_changes')->where('token', '=', request()->token)->where('is_confirmed', 0)->first();
        // }
        if ($token = $request->query('token')) {

            $participant_email_changes = DB::table('participant_email_changes')
                ->where('token', '=', $token)
                ->where('is_confirmed', 0)
                ->first();

            if ($participant_email_changes) {
                $user = DB::table('users')
                    ->where('id', '=', $participant_email_changes->user_id)->first();
                // if (!Hash::check($request->input('password'), $user->password)) {
                //     return redirect()->back()->withErrors('Incorrect Credentials. Please try again!');
                // }
                $body = array(
                    "listingParameter" => array(
                        'prev_email' => $participant_email_changes->old_email,
                        "new_email" => $participant_email_changes->new_email
                    )
                );
                // update email in epro DB
                $response = curl_request(
                    env('EPRO_URL') . 'updateEmail',
                    'POST',
                    $body
                );
                $response = json_decode($response, true);

                // check response
                if ($response['status'] !== 'success' || $response['status_code'] != 200) {
                    // return redirect()->back()->withErrors($response['message']);
                    $participant_email_changes = new ParticipantEmailChange;
                    return view('admin.auth.login', compact('data', 'token', 'participant_email_changes'))->withErrors($response['message']);
                }


                $body = array(
                    'old_email' => $participant_email_changes->old_email,
                    'new_email' => $participant_email_changes->new_email
                );

                // update email in fpse DB
                $response = curl_request(
                    env('FPSE_URL') . 'updateEmail',
                    'POST',
                    $body
                );
                $response = json_decode($response, true);

                // check response
                if ($response['status'] !== 'success' || $response['status_code'] != 200) {
                    // return redirect()->back()->withErrors($response['message']);
                    $participant_email_changes = new ParticipantEmailChange;
                    return view('admin.auth.login', compact('data', 'token', 'participant_email_changes'))->withErrors($response['message']);
                }

                // update in users table
                DB::table('users')
                    ->where('id', '=', $participant_email_changes->user_id)
                    ->update([
                        'email' => Crypto::encryptData($participant_email_changes->new_email, Crypto::getAwsEncryptionKey()),
                        'username' => $participant_email_changes->new_email,
                        'email_hash' => md5($participant_email_changes->new_email)
                    ]);



                // update in participants table
                DB::table('participants')
                    ->where('email_hash', md5($participant_email_changes->old_email))
                    ->update([
                        'email' => Crypto::encryptData($participant_email_changes->new_email, Crypto::getAwsEncryptionKey()),
                        'email_hash' => md5($participant_email_changes->new_email)
                    ]);

                DB::table('participants')
                    ->join('participant_profiles', 'participant_profiles.participant_id', '=', 'participants.id')
                    ->join('payment_details', 'participant_profiles.subject_id', '=', 'payment_details.subject_id')
                    ->join('payment_generator_bulks', 'payment_details.subject_id', '=', 'payment_generator_bulks.subject_id')
                    ->where('participants.email_hash', md5($participant_email_changes->old_email))
                    ->whereNotIn('payment_details.status', ['paid', 'processing'])
                    ->whereNotIn('payment_generator_bulks.status', ['paid', 'processing'])
                    ->update([
                        'payment_details.email' =>  Crypto::encryptData($participant_email_changes->new_email, Crypto::getAwsEncryptionKey()),
                        'payment_generator_bulks.email' => Crypto::encryptData($participant_email_changes->new_email, Crypto::getAwsEncryptionKey())
                    ]);

                ParticipantEmailChange::find($participant_email_changes->id)->update(['is_confirmed' => true]);

                $user_data = collect($participant_email_changes)->toArray();;

                $emailTemplate = EmailTemplate::where('name','Change Email Details')->where('status','Active')->first();
                if(isset($emailTemplate)){
                $html = $emailTemplate->message;
                $output = preg_replace_callback(
                    '~\{(.*?)\}~',
                    function ($key) use($user_data) {
                        $variable["opt('site_title')"] = opt('site_title');
                        $variable["opt('site_url')"] = opt('site_url');
                        $variable["user_data['subject_id']"] = $user_data['subject_id'];
                        $variable["user_data['old_email']"] = $user_data['old_email'];
                        $variable["user_data['new_email']"] = $user_data['new_email'];
                        if ($variable[$key[1]] != null) {
                            return $variable[$key[1]];
                        }else{
                            return "";
                            return $key[0];
                        }
                    },
                    $html,
                );
                $blade_from_db = view('admin.mails.dynamic_mail',['user_data',$user_data,'html' => $output])->render();
                Mail::send([], compact('user_data'), function ($message) use ($user_data,$blade_from_db) {
                            $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
                    $message->to($user_data['new_email'], 'Evo-User')->subject(opt('site_title')." - Email Changed");
                    $message->bcc(getSupportEmails());
                    $message->setBody($blade_from_db,'text/html');
                });
            }
            }
        }

        return view('admin.auth.login', compact('data', 'token', 'participant_email_changes'))->with(['error' => "token expired"]);
    }

    function do_login(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'username' => "required",
            'password' => "required|min:8|max:16",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $JSON['status'] = false;
        $credentials = $request->only('username', 'password');

        $credentials['status'] = 'Active';

        // $credentials['login_code'] = request()->input('login_code');
        if (Auth::attempt($credentials, intval(getVar('remember')))) {
            activity_log('login', 'users', Auth::id(), Auth::id(), 'success', auth()->user());
            if ($request->ajax()) {
                $JSON['status'] = true;
                $JSON['message'] = 'Successfully login!';
                $JSON['redirect'] = redirect()->intended(admin_url('dashboard'))->getTargetUrl();
            } else {
                $login_count = DB::table('users')->select('login_count')->where('id', Auth::id())->first();
                // dd($login_count->login_count);
                if ($login_count->login_count == '0') {
                    return redirect()->intended(admin_url('user_info/change_pass_first_attempt'));
                } else {
                    $user = auth()->user();
                    if (!$user->two_factor_auth) {
                        return redirect()->intended(admin_url('dashboard'));
                    }

                    // generate 6 digit pin
                    $pin = random_int(100000, 999999);
                    $expiresTime = date('Y-m-d H:i:s', strtotime('4 hour'));
                    // get authenticated user

                    // update current user row i.e. login_pin, pin_created_at, pin_verified
                    $user->update([
                        'pin_verified' => 0,
                        'login_pin' => $pin,
                        'pin_expire_at' => $expiresTime
                    ]);

                    $email = Crypto::decryptData($user->email, Crypto::getAwsEncryptionKey());
                    $data = ['pin' => $pin];

                    $emailTemplate = EmailTemplate::where('name','Login Pin')->where('status','Active')->first();
                    if(isset($emailTemplate)){
                    $html = $emailTemplate->message;
                    $output = preg_replace_callback(
                        '~\{(.*?)\}~',
                        function ($key) use($data) {
                            $variable["opt('site_title')"] = opt('site_title');
                            $variable["opt('site_url')"] = opt('site_url');
                            $variable["pin"] = $data['pin'];
                            if ($variable[$key[1]] != null) {
                                return $variable[$key[1]];
                            }else{
                                return "";
                                return $key[0];
                            }
                        },
                        $html,
                    );
                    $blade_from_db = view('admin.mails.dynamic_mail',['data',$data,'html' => $output])->render();
                    Mail::send([], $data, function ($message) use ($email,$blade_from_db) {
                                $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
                        $message->to($email, 'Evo-User')->subject(opt('site_title').' - Login Pin');
                        $message->bcc(getSupportEmails());
                        $message->setBody($blade_from_db,'text/html');
                    });
                }

                    // send number code here
                    if ($user->country_code && $user->phone) {
                        $country_code = $user->country_code;
                        $phone = Crypto::decryptData($user->phone, Crypto::getAwsEncryptionKey());
                        $message = 'Hello, This is your OTP, Please enter this code to login.' . $pin;
                        send_aws_sns_sms($country_code, $phone, $message);
                    }

                    // redirect to pin_confirmation file
                    return redirect(admin_url('user_info/pin_confirmation'));


                    return redirect()->intended(admin_url('dashboard'));
                }
                return redirect()->intended(admin_url('dashboard'));
            }
        } else {
            activity_log('login', 'users', Auth::id(), Auth::id(), 'failed', $request->username);
            return redirect()->back()->withErrors('Incorrect Credentials. Please try again!');
        }

        return $JSON;
    }

    public function forgotPassword()
    {
        return view('admin.auth.forget_password');
    }

    public function forgetPasswordSubmissions()
    {
        $validator = Validator::make(request()->all(), [
            'email' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $userQuery = User::where('username', '=', request()->input('email'));
        if ($userQuery->exists()) {
            $token = $userQuery->pluck('forget_pass_token')->first();
            if (!$token) {
                $token = Token::generateRandomString();
            }
            $user = User::where('username', request()->input('email'))->first();
            $user->forget_pass_token = $token;
            $user->save();
            $data = array(
                'email' => Crypto::decryptData($user->email, Crypto::getAwsEncryptionKey()),
                'url' => admin_url() . "/login/resetLink/" . $token,
            );
            $email = Crypto::decryptData($user->email, Crypto::getAwsEncryptionKey());
            $emailTemplate = EmailTemplate::where('name','Forget Password')->where('status','Active')->first();
            if(isset($emailTemplate)){
            $html = $emailTemplate->message;
            $output = preg_replace_callback(
                '~\{(.*?)\}~',
                function ($key) use($email,$data) {
                    $variable["opt('site_title')"] = opt('site_title');
                    $variable["opt('site_url')"] = opt('site_url');
                    $variable["email"] = $email;
                    $variable["url"] = $data['url'];
                    $variable["date('Y')"] = date('Y');
                    if ($variable[$key[1]] != null) {
                        return $variable[$key[1]];
                    }else{
                        return $key[0];
                    }
                },
                $html,
            );
            // return($output);
            $blade_from_db = view('admin.mails.dynamic_mail',['data',$data,'email' => $email,'html' => $output])->render();
            // return $blade_from_db;
            Mail::send([], $data, function ($message) use ($email,$blade_from_db) {
                $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
                $message->to($email, 'Evo-User')->subject(opt('site_title').' - Forget-Password');
                $message->bcc(getSupportEmails());
                $message->setBody($blade_from_db,'text/html');
            });
        }
            return redirect()->back()->with('message', 'Reset link has been emailed.');
        } else {
            return redirect()->back()->withErrors('Provided Email-Id/Username ' . request()->input('email') . ' not found');
        }
    }

    function resetPasswordLink()
    {
        // dd();
        $token = request()->segment(4);
        $email = User::select('email')->where('forget_pass_token', $token)->first();
        $email = Crypto::decryptData($email->email, Crypto::getAwsEncryptionKey());
        //        dd($email);
        return view('admin.auth.password_reset_link', compact('token', 'email'));
    }
    function resetPassword()
    {
        //         dd(request()->all());
        $validator = Validator::make(request()->all(), [
            'email' => "required",
            'password' => "required|min:8|max:16",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $reg = "/(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/";

        if(!preg_match($reg, request()->password)) {
            return redirect()->back()->withErrors("Password must have at least one character one numric and one special character")->withInput();
        }

        if (User::where('forget_pass_token', request()->input('reset_token'))->exists()) {
            // dd('milgia');
            $user = User::where('forget_pass_token', request()->input('reset_token'))->first();
            //            $user = User::where('username', request()->input('email'))->first();
            // dd(Hash::make(request()->input('password')));
            //            dd($user);
            $user->password = Hash::make(request()->input('password'));
            $user->forget_pass_token = null;
            $user->save();
            $email_hash = md5(request()->input('email'));

            $participant_erx_id = DB::table('participants')
                ->select('erx_id')
                ->where('email_hash', '=', $email_hash)
                ->pluck('erx_id')
                ->first();

            $postRequest['listingParameter'] = array(
                'erx_id' => $participant_erx_id,
                'password' => request()->input('password')
            );

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('EPRO_URL') . "changePasswordPortal",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($postRequest),
                CURLOPT_HTTPHEADER => array(
                    "Accept: */*",
                    "Cache-Control: no-cache",
                    "Connection: keep-alive",
                    "Content-Type: application/json",
                    "cache-control: no-cache"
                ),
            ));
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            activity_log('reset password', 'users', Auth::id(), Auth::id(), 'success', $user);

            return redirect(admin_url('login'))->with('message', 'Password Successfully Changed');
            return redirect()->back()->with('message', 'Password Successfully Changed');
        } else {
            return redirect(admin_url('login'))->withErrors('Link Expired.');
            return redirect()->back()->withErrors('Provided Email-Id not found');
        }
    }

    function api_forget_pass()
    {

        $validator = Validator::make(request()->all(), [
            'email' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (User::where('username', '=', request()->input('email'))->exists()) {
            $str = Token::generateRandomString();
            $user = User::where('username', request()->input('email'))->first();
            $user->forget_pass_token = $str;
            $user->save();
            $data = array(
                'email' => Crypto::decryptData($user->email, Crypto::getAwsEncryptionKey()),
                'url' => admin_url() . "/login/resetLink/" . $str,
            );
            $email = Crypto::decryptData($user->email, Crypto::getAwsEncryptionKey());
            $emailTemplate = EmailTemplate::where('name','Forget Password')->where('status','Active')->first();
            if(isset($emailTemplate)){
            // $html = html_entity_decode($emailTemplate->message);
            $html = $emailTemplate->message;
            $output = preg_replace_callback(
                '~\{(.*?)\}~',
                function ($key) use($email,$data) {
                    $variable["opt('site_title')"] = opt('site_title');
                    $variable["opt('site_url')"] = opt('site_url');
                    $variable["email"] = $email;
                    $variable["url"] = $data['url'];
                    $variable["date('Y')"] = date('Y');
                    if ($variable[$key[1]] != null) {
                        return $variable[$key[1]];
                    }else{
                        return $key[0];
                    }
                },
                $html,
            );
            // $body = str_replace('{{'.$opt('company_url').'}}', $parameter, $body);
            $blade_from_db = view('admin.mails.dynamic_mail',['data',$data,'email' => $email,'html' => $output])->render();
            Mail::send([], $data, function ($message) use ($email,$blade_from_db) {
                        $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
                $message->to('mohammadmuneeb02@gmail.com', 'Evo-User')->subject(opt('site_title').' - Forget-Password');
                $message->bcc(getSupportEmails());
                $message->setBody($blade_from_db,'text/html');
            });
        }
            $data = [
                'success' => true,
                'message' => 'Reset link has been emailed'
            ];

            return json_encode($data);
        } else {
            $data = [
                'success' => false,
                'message' => 'Provided user not found'
            ];

            return json_encode($data);
        }
    }

    function logout()
    {
        activity_log('logout', 'users', Auth::id(), Auth::id());
        Auth::logout();
    }
}
