<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Application;
use Illuminate\Support\Facades\Hash;
use App\UserPortalRole;
use App\AppUserAuthentication;

class SssoController extends Controller
{
    public function index(Request $request)
    {
        $user_id = User::where('email', $request->email)->pluck('id')->first();
        $application_id = Application::where('application_code', $request->application_code)->pluck('id')->first();
        $token = Application::where('application_code', $request->application_code)->pluck('id')->first();
        if (!isset($user_id)) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "User does not exists"
            );
            return json_encode($arrres);
        }
        if (!isset($application_id)) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "Application does not exists"
            );
            return json_encode($arrres);
        }
        if (UserPortalRole::select('id')->where('user_id', $user_id)->where('application_id', $application_id)->doesntExist()) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "User is not allowed to access the application"
            );
            return json_encode($arrres);
        }
        if (AppUserAuthentication::select('id')->where('token', $request->token)->where('count', '<=', 1)->doesntExist()) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "Invalid Token"
            );
            return json_encode($arrres);
        }
        $count = AppUserAuthentication::where('token', $request->token)->pluck('count')->first();
        $count = $count + 1;
        AppUserAuthentication::where('token', $request->token)->update(['count' => $count]);
        $arrres = array(
            "Success" => true,
            "ResponseCode" => "200",
            "Message" => "Valid Token"
        );
        return json_encode($arrres);
    }
    public function validateToken($username,$applicationCode,$ssoToken)
    {
        $user_id = User::where('username', $username)->pluck('id')->first();
        $application_id = Application::where('application_code', $applicationCode)->pluck('id')->first();
        $token = Application::where('application_code', $applicationCode)->pluck('id')->first();
        if (!isset($user_id)) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "User does not exists"
            );
            return json_encode($arrres);
        }
        if (!isset($application_id)) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "Application does not exists"
            );
            return json_encode($arrres);
        }
        if (UserPortalRole::select('id')->where('user_id', $user_id)->where('application_id', $application_id)->doesntExist()) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "User is not allowed to access the application"
            );
            return json_encode($arrres);
        }
        // return 'here';
        if (AppUserAuthentication::select('id')->where('token', $ssoToken)->where('count', '<=', 1)->doesntExist()) {
            $arrres = array(
                "Success" => false,
                "ResponseCode" => "300",
                "Message" => "Invalid Token"
            );
            return json_encode($arrres);
        }
        $count = AppUserAuthentication::where('token', $ssoToken)->pluck('count')->first();
        // $count = $count + 1;
        AppUserAuthentication::where('token', $ssoToken)->update(['count' => $count]);
        $arrres = array(
            "Success" => true,
            "ResponseCode" => "200",
            "Message" => "Valid Token"
        );
        return json_encode($arrres);
    }

    public function userAuthForMobileApp(Request $request)
    {
        return 'hi';
        if (User::where('username', '=', $request->username)->exists()) {

            $user = User::where([
                'username' => $request->username,
                'password' => Hash::make($request->password)
            ])->first();

            if (!isset($user)) {
                $arrres = array(
                    "Success" => false,
                    "Payload" => NULL,
                    "ResponseCode" => "300",
                    "Message" => "User Password is incorrect"
                );
                return json_encode($arrres);
            }

            $arrres = array(
                "Success" => true,
                "Payload" => $user,
                "ResponseCode" => "200",
                "Message" => "User Exists!"
            );
            return json_encode($arrres);
        } else {
            $arrres = array(
                "Success" => false,
                "Payload" => NULL,
                "ResponseCode" => "300",
                "Message" => "User does not exists"
            );
            return json_encode($arrres);
        }
    }
}
