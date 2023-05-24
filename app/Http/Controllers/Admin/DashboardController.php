<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Dashboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Input\Input;
use Token;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user_type = Auth::user()->user_type_id;
        $type_name = DB::table('users')
            ->select('user_types.user_type')
            ->join('user_types', 'users.user_type_id', '=', 'user_types.id')
            ->where('users.id', Auth::user()->id)
            ->first();

        if ($type_name->user_type == 'Participant') {
            $trial_site_count = DB::table('user_portal_trial_site')
                ->select('id')
                ->where('user_id', '=', Auth::user()->id)
                ->count();
        }

        if ($trial_site_count > 1) {
            $participant_trial_name = DB::table('user_portal_trial_site')
                ->select('user_portal_trial_site.id', 'user_portal_trial_site.trial_id', 'user_portal_trial_site.site_id', 'trials.study_name')
                ->join('trials', 'user_portal_trial_site.trial_id', '=', 'trials.id')
                ->where('user_id', '=', Auth::user()->id)
                ->get();
            //            dd($participant_trial_name);
        }

        $data = [];
        if ($user_type == 1) {
            $applications = DB::table('applications')
                ->select('*')
                ->where('application_status', '=', 1)
                ->get();
        } else {
            $applications = DB::table('user_portal_role')
                ->select('applications.*')
                ->join('applications', 'user_portal_role.application_id', '=', 'applications.id')
                ->where('user_portal_role.user_id', '=', Auth::id())
                ->where('applications.application_status', '=', 1)
                ->get();
        }
        foreach($applications as $app){
            if($app->id == 25){
                // dd(json_decode($app->technology_stack)[0]->value);
            }
        }
        // return $applications;

        return view('admin.dashboard.index', compact('applications', 'type_name', 'trial_site_count', 'participant_trial_name'));
    }

    public function openApp()
    {
        $applicationId = request("applicationId");
        $trial_id = request("trial_id");

        // $client = new Client([
        //     // Base URI is used with relative requests
        //     'base_uri' => 'http://httpbin.org',
        //     // You can set any number of default request options.
        //     'timeout'  => 2.0,
        // ]);

        #guzzle hits application url with data array
        #200 with unique id
        #
        // return redirect()->away('https://www.google.com');

        // return Redirect::away('http://127.0.0.1:8001/login/sso/')->with(['user_id' => '1']);

        // dd($application_code);
        $type_name = DB::table('users')
            ->select('user_types.user_type')
            ->join('user_types', 'users.user_type_id', '=', 'user_types.id')
            ->where('users.id', Auth::user()->id)
            ->first();

        $ip = \Request::ip();
        $token = Token::generateAppToken();
        // dd(Auth::user());
        $application_code = DB::table('applications')
            ->select('application_code', 'application_url')
            ->where('id', '=', $applicationId)
            ->get();

        $application_role = DB::table('user_portal_role')
            ->select('application_role')
            ->where('application_id', '=', $applicationId)
            ->where('user_id', '=', Auth::id())
            ->get();

        $tenant_login_code = DB::table('tenants')
            ->select('login_code')
            ->where('id', '=', Auth::user()->tenants_id)
            ->get();

        $app_user_authentication = array(
            'token' => $token,
            'user_id' => Auth::user()->id,
            'ip_address' => $ip,
            'token_duration' => 30,
            'application_id' => $applicationId,
            'status' => 0,
        );

        $user_trial_sites = DB::table('user_portal_trial_site')
            ->select('trial_id AS clinical_trial_id', 'site_id')
            ->where('user_id', Auth::user()->id)
            ->where('tenants_id', Auth::user()->tenants_id)
            ->where('application_id', $applicationId)
            ->where('is_active', 'yes')
            ->when($trial_id != 0, function ($query) use ($trial_id) {
                return $query->where('trial_id', $trial_id);
            })
            ->get()->toArray();

        if ($user_trial_sites) {
            foreach ($user_trial_sites as $user_trial_site) {
                $site_ids[] = $user_trial_site->site_id;
            }
        }

        DB::table('app_user_authentication')->insert($app_user_authentication);

        $data = array(
            'date_time' => date("Y-m-d H:i:s"),
            'token' => $token,
            'user_id' => Auth::id(),
            'ip_address' => $ip,
            'token_time_stamp' => date("Y-m-d H:i:s"),
            'token_duration' => "30",
            'application_id' => $applicationId,
            'application_role' => $application_role[0]->application_role,
            'application_code' => $application_code[0]->application_code,
            'application_url' => $application_code[0]->application_url,
            'first_name' => Auth::user()->first_name,
            'last_name' => Auth::user()->last_name,
            'username' => Auth::user()->username,
            'email' => Auth::user()->email,
            'client_login_code' => $tenant_login_code[0]->login_code,
            'status' => "1",
            'clinical_trial_id' => $user_trial_sites[0]->clinical_trial_id,
            'site_id' => $site_ids,

        );
        // dd($data);
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function show(Dashboard $dashboard)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function edit(Dashboard $dashboard)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dashboard $dashboard)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dashboard  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dashboard $dashboard)
    {
        //
    }
}
