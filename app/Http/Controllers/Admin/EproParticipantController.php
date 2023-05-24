<?php

namespace App\Http\Controllers\Admin;

use App\EmailTemplate;
use App\Http\Controllers\Controller;
use App\ParticipantEmailChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Breadcrumb;
use Crypto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use Token;

class EproParticipantController extends Controller
{
    public $module = ''; //Project module name
    public $_info = null; // Project module info
    public $_route = '';

    public $model = null; // Object
    public $table = ''; // Object
    public $id_key = ''; // Object

    var $where = '1';

    public function __construct()
    {
        $this->module = getUri(2);
        $this->_route = admin_url($this->module);
        $model = 'App\\' . \Str::studly(\Str::singular($this->module));
        $this->model = new $model;
        $this->table = $this->model->getTable();
        $this->id_key = $this->model->getKeyName();

        $this->_info = getModuleDetail();

        if (user_do_action('self_records')) {
            $user_id = Auth::user()->id;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'group' => 'users.' . $this->id_key])->merge(request()->query())->toArray();

        /** -------- Query */
        $select = "users.id
            , users.user_type_id
            , user_types.user_type
            , user_types.hierarchy
            , users.first_name
            , users.email
            , users.status
            ";
        $user_hierarchy = Auth::user()->usertype['hierarchy'];
        $this->where .= " AND user_types.hierarchy <= {$user_hierarchy}";
        $SQL = $this->model->select(\DB::raw($select));

        $user_type = Auth::user()->user_type_id;
        $login_code = Auth::user()->userclient['login_code'];
        $all_data_access = Auth::user()->is_allowed_all_data_access;

        $participant_type_id = DB::table('user_types')->where('user_type', '=', "Participant")->first();

        $participants = DB::table('users')
            ->select('users.id', 'users.login_code', 'users.first_name', 'users.last_name', 'users.username', 'users.email', 'users.status', 'user_portal_trial_site.user_id as user_id', 'user_portal_trial_site.trial_id as trial_id', 'participants.erx_id')
            ->join('user_portal_trial_site', 'users.id', '=', 'user_portal_trial_site.user_id')
            ->join('participants', 'users.email_hash', '=', 'participants.email_hash')
            ->where('users.user_type_id', '=', $participant_type_id->id);

        if ($all_data_access != 1) {
            $participants->where('users.login_code', '=', $login_code);
        }

        if ($trial_id = request()->query('trial_id')) {
            $participants = $participants->where('user_portal_trial_site.trial_id', $trial_id);
        }

        $participants = $participants->get();
        $participant_id = $participant_id[0]->id;
        $where = $this->where . " And users.user_type_id = '{$participant_id}'";
        $where .= getWhereClause($select);
        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

        $SQL = $SQL->leftJoin('user_types', 'user_types.id', '=', 'users.user_type_id');
        $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);

        $paginate_OBJ = $SQL->paginate(10000);
        $query = $SQL->toSql();

        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        $paginate_OBJ
            ->getCollection()
            ->transform(function ($item, $key) {

                return [
                    'id' => $item['id'],
                    'user_type_id' => $item['user_type_id'],
                    'user_type' => $item['user_type'],
                    'hierarchy' => $item['hierarchy'],
                    'first_name' => Crypto::decryptData($item['first_name'], Crypto::getAwsEncryptionKey()),
                    'email' => Crypto::decryptData($item['email'], Crypto::getAwsEncryptionKey()),
                    'status' => $item['status']
                ];
            });
        $trials = DB::table('trials');

        if (!$all_data_access) {
            $trials = $trials->where('tenant_code', Auth::user()->login_code);
        }
        $trials = $trials->get();
        // return($trials);
        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.epro_participants.grid', compact('paginate_OBJ', 'query', 'participants', 'trials'), ['_info' => $this->_info]);
        }
    }

    public function form()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = $this->model->find($id);
            if ($row->id <= 0) {
                return \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        }

        /** -------- Breadcrumb */
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item(($id > 0) ? "Edit -> id:[$id]" : 'Add New');
        $tenants = DB::table('tenants')
            ->select('id', 'tenant_name')
            ->get();

        $applications = DB::table('applications')
            ->select('id', 'application_name')
            ->get();

        $roles = DB::table('user_types')
            ->select('id', 'user_type AS role')
            ->where('user_types.user_type', '=', 'Participant')
            ->get();

        $user_application_roles = DB::table('user_portal_role')
            ->select('*')
            ->where('user_id', '=', $id)
            ->get();

        $user_application_trials_sites_roles = DB::table('user_portal_trial_site')
            ->select('*')
            ->where('user_id', '=', $id)
            ->get();
        // dd($row);

        /** -------- Response */
        return view('admin.epro_participants.form', compact('row', 'tenants', 'applications', 'roles', 'user_application_roles', 'user_application_trials_sites_roles'), ['_info' => $this->_info]);
    }

    public function updateParticipantEmail()
    {

        if (request()->input('updated_email') !== request()->input('confirm_email')) {
            return redirect()->back()->with('error', 'email and confirm email not match');
        }

        $user_id = request()->input('user_id');
        $updated_email = request()->input('updated_email');

        $body = array("email" => $updated_email);
        $response = curl_request(
            env('EPRO_URL') . 'checkEmailInvitation',
            "POST",
            $body
        );

        $response = json_decode($response, true);

        if ($response['status'] !== 'success' || $response['status_code'] != 200) {
            return redirect()->back()->with('error', $response['message']);
        }




        // fetch all emails from users table
        $emails = DB::table('users')
            ->select('email')
            ->get();
        // dd($emails);

        // check if the given email is already exists
        foreach ($emails as $email) {
            if (Crypto::decryptData($email->email, Crypto::getAwsEncryptionKey()) === $updated_email) {
                return redirect(admin_url("index/", true))->with('error', 'Email-Id already exists!');
            }
        }


        $user_data = DB::select("SELECT p.erx_id, u.id as user_id, u.email as old_email, p.tenant_code, pp.subject_id
            FROM participants p JOIN
            users u on MD5(u.username) = p.email_hash
            JOIN participant_profiles pp ON p.id = pp.participant_id
            WHERE u.id = {$user_id}
        ");
        $user_data = $user_data[0];

        if (!$user_data->subject_id) {
            return redirect(admin_url("index/", true))->with('error', 'This participant ICF is still in pending!');
        }
        $user_data = (array)($user_data);

        $user_data['old_email'] = Crypto::decryptData($user_data['old_email'], Crypto::getAwsEncryptionKey());
        $user_data['new_email'] = $updated_email;
        $user_data['updated_by'] = auth()->id();

        $unconfirn_request = DB::table('participant_email_changes')->where([
            'user_id' => $user_id,
            'old_email' => $user_data['old_email'],
            'new_email' => $updated_email,
            'tenant_code' => $user_data['tenant_code'],
            'is_confirmed' => 0
        ]);

        if ($unconfirn_request->exists()) {
            $user_data = collect($unconfirn_request->first())->toArray();
        } else {
            $user_data['token'] = Token::generateRandomString();
            ParticipantEmailChange::create($user_data);
        }

        activity_log('Email Trigger', 'participant_email_changes', 0, 0, null, $user_data);
        $emailTemplate = EmailTemplate::where('name','New Email Confirmation')->where('status','Active')->first();
        if(isset($emailTemplate)){
        $html = $emailTemplate->message;
        $output = preg_replace_callback(
            '~\{(.*?)\}~',
            function ($key) use($user_data) {
                $variable["opt('site_title')"] = opt('site_title');
                $variable["opt('site_url')"] = opt('site_url');
                $variable["url"] = admin_url('login') . '?token=' . $user_data['token'];
                if ($variable[$key[1]] != null) {
                    return $variable[$key[1]];
                }else{
                    return "";
                    return $key[0];
                }
            },
            $html,
        );
        $blade_from_db = view('admin.mails.dynamic_mail',['user_data',$user_data,'email' => $email,'html' => $output])->render();
            // return $blade_from_db;
        Mail::send([], compact('user_data'), function ($message) use ($user_data,$blade_from_db) {
            $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
            $message->to($user_data['new_email'], 'Evo-User')->subject("Confirm your new email address");
            $message->bcc(getSupportEmails());
            $message->setBody($blade_from_db,'text/html');
        });
    }
        // dd($user_data['token']);

        return redirect(admin_url("index/", true))->with('success', 'Confirmation email sent to participant!');
    }

    // public function check_email_invitation()
    // {

    //     $email = Crypto::decryptData('vhFuRSgPamtuWuoimkY+OKt4+m09KY+SuRmZCpS927y/KEcyEfU3eWgXXYhnp0TzXh8gnLlSLNLk++h1efoTVB8rcCi6EpPSDbs/2Qe65dM=', Crypto::getAwsEncryptionKey());
    //     // $email = 'muhammad.shafique@lathran.com';

    //     $body = array("email" => $email);

    //     $response = curl_request(
    //         env('EPRO_URL') . 'checkEmailInvitation',
    //         "POST",
    //         $body
    //     );
    //     dd(json_decode($response, true));
    // }

    public function store()
    {

        $id = request()->input($this->id_key);

        /** -------- Validation */
        $validator_rules = [
            'user_type_id' => "required",
            'first_name' => "required",
            'tenants_id' => "required",
            'email' => "required",
            'username' => "required|unique:users,username,{$id},{$this->id_key}",
            'password' => "required|min:8|max:30",
        ];

        if ($id > 0) {
            unset($validator_rules['password']);
        }

        $validator = Validator::make(request()->all(), $validator_rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = DB_FormFields($this->model);

        $role_ids = array_values(array_filter(request()->input('role_id')));
        $application_ids = request()->input('application_name');
        $clinical_studies_ids = array_values(array_filter(request()->input('clinic_studies')));
        $sites_ids = request()->input('sites');
        $tenant_id = request()->input('tenants_id');
        $login_code = DB::table('tenants')
            ->select('login_code')
            ->where('id', '=', $tenant_id)
            ->get();
        $application_and_role = array_combine($application_ids, $role_ids);
        $trial_and_site = array_combine($clinical_studies_ids, $sites_ids);
        $data['data']['login_code'] = $login_code[0]->login_code;


        /** -------- Upload Files */
        if (!empty($data['data']['password'])) {
            $data['data']['password'] = Hash::make($data['data']['password']);
        } else {
            unset($data['data']['password']);
        }


        if ($id > 0) {
            $row = $this->model->find($id);
            $row = $row->fill($data['data']);
        } else {
            $row = $this->model->fill($data['data']);
        }

        if ($status = $row->save()) {
            if ($id == 0) {
                $id = $row->{$this->id_key};
                // Insert Relation Table logic Here!


                for ($i = 0; $i < count($role_ids); $i++) {
                    DB::table('user_portal_role')->insert([
                        ['user_id' => $id, 'tenants_id' => $tenant_id, 'application_id' => $application_ids[$i], 'application_role' => $role_ids[$i]]
                    ]);
                }

                if ($clinical_studies_ids) {
                    for ($a = 0; $a < count($sites_ids); $a++) {

                        $data_user_portal_trial_site = ['user_id' => $id, 'tenants_id' => $tenant_id, 'application_id' => explode("|", request()->input('sites')[$a])[0], 'trial_id' => explode("|", request()->input('sites')[$a])[2], 'site_id' => explode("|", request()->input('sites')[$a])[1]];

                        $insertedId = DB::table('user_portal_trial_site')->insertGetId($data_user_portal_trial_site);
                        activity_log('Add', $this->table, $insertedId, 0, null, $data_user_portal_trial_site);
                    }
                }

                set_notification('Record has been inserted!', 'success');
                activity_log('Add', $this->table, $id, 0, null, $data['data']);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {

                DB::table('user_portal_role')->where('user_id', '=', $id)->delete();
                DB::table('user_portal_trial_site')->where('user_id', '=', $id)->delete();
                // Insert Relation Table logic Here!

                for ($i = 0; $i < count($role_ids); $i++) {
                    $data_user_portal_role = ['user_id' => $id, 'tenants_id' => $tenant_id, 'application_id' => $application_ids[$i], 'application_role' => $role_ids[$i]];
                    $insertedId = DB::table('user_portal_role')->insertGetId($data_user_portal_role);
                    activity_log('Add', $this->table, $insertedId, 0, null, $data_user_portal_role);

                    if ($clinical_studies_ids) {
                        for ($a = 0; $a < count($sites_ids); $a++) {
                            $data_user_portal_trial_site = ['user_id' => $id, 'tenants_id' => $tenant_id, 'application_id' => explode("|", request()->input('sites')[$a])[0], 'trial_id' => explode("|", request()->input('sites')[$a])[2], 'site_id' => explode("|", request()->input('sites')[$a])[1]];
                            $insertedId = DB::table('user_portal_trial_site')->insertGetId($data_user_portal_trial_site);
                            activity_log('Add', $this->table, $insertedId, 0, null, $data_user_portal_trial_site);
                        }
                    }
                }

                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id, 0, null, $data['data']);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('success', 'Record has been Updated!');
            }
        } else {
            set_notification('Some error occurred!', 'error');
        }

        if (request()->ajax()) {
            $alert_types = ['success', 'error' => 'danger', 'warning', 'primary', 'info', 'brand'];
            $alerts = collect(session('errors')->all())->append(collect($alert_types)->map(function ($val, $key) {
                return session($val);
            }));
            return $alerts;
        } else {
            $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
            return redirect($__redirect)->with('success', 'Record has been Inserted!');
        }
    }

    function status()
    {
        $id = getUri(4);
        $ids = request()->input('ids');
        if ($id > 0) {
            $ids = [$id];
        }

        $data = ['status' => request('status')];
        $this->model->whereIn($this->id_key, $ids)->update($data);

        set_notification('Status has been updated', 'success');
        activity_log(getUri(3), $this->table, $ids, 0, null, $data);

        $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("index/", true));
        return redirect($__redirect)->with('success', 'Status has been Updated!');

        //        return \Redirect::to($this->_route);
    }

    public function delete()
    {
        $id = getUri(4);
        $ids = request()->input('ids');
        if ($id > 0) {
            $ids = [$id];
        }
        if ($ids == null || count($ids) == 0) {
            return redirect()->back()->with('danger', 'Select minimum one row!');
        }

        $unlink = [
            'photo' => 'assets/front/{$this->table}',
        ];
        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ")", true, $unlink);
        //$this->model->whereIn($this->id_key, $ids)->delete();

        activity_log(getUri(3), $this->table, $ids);

        return \Redirect::to(admin_url('index', true))->with('success', 'Record has been deleted!');
    }
}
