<?php

/**
 * Class User
 * @property App\User $module
 */

namespace App\Http\Controllers\Admin;

use App\Tenant;
use Auth;
use App\User;
use Breadcrumb;
use Crypto;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public $module = ''; //Project module name
    public $_info = null; // Project module info
    public $_route = '';

    public $model = null; // Model Object
    public $table = '';
    public $id_key = '';

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

        $agent_type_id = opt('agent_type_id');
        $this->where .= " AND {$this->table}.user_type_id != '{$agent_type_id}'";
        if (user_do_action('self_records')) {
            $user_id = Auth::user()->id;
            $this->where .= " AND {$this->table}.created_by = '{$user_id}'";
        }
    }


    /**
     * *****************************************************************************************************************
     * @method users index | Grid | listing
     * *****************************************************************************************************************
     */
    public function index()
    {
        // dd(Auth::user()->usertype['hierarchy']);
        /** -------- Breadcrumb */
        Breadcrumb::add_item($this->_info->title, $this->_route);

        /** -------- Pagination Config */
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'group' => 'users.' . $this->id_key])->merge(request()->query())->toArray();

        /** -------- Query */
        $select = "users.id
-- , users.user_type_id
, user_types.user_type
, user_types.hierarchy
, users.first_name
, users.email
, users.username
, users.status
";
        $user_hierarchy = Auth::user()->usertype['hierarchy'];
        $this->where .= " AND user_types.hierarchy <= {$user_hierarchy} AND users.user_type_id != 18";
        $user_client_id = Auth::user()->userclient['id'];
        $user_type = Auth::user()->user_type_id;
        $login_code = Auth::user()->userclient['login_code'];
        $all_data_access = Auth::user()->is_allowed_all_data_access;

        if ($all_data_access != 1) {
            $this->where .= " AND {$this->table}.login_code='{$login_code}' AND {$this->table}.tenants_id={$user_client_id} AND {$this->table}.is_migrated=0";
        }
        $SQL = $this->model->select(\DB::raw($select));

        /** -------- WHERE */
        $where = $this->where;
        $where .= getWhereClause($select);
        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

        $SQL = $SQL->leftJoin('user_types', 'user_types.id', '=', 'users.user_type_id');
        $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);

        $paginate_OBJ = $SQL->paginate(10000);
        // dd($config['limit']);
        $paginate_OBJ
            ->getCollection()
            ->transform(function ($item, $key) {
                return [
                    'id' => $item['id'],
                    'user_type' => $item['user_type'],
                    'hierarchy' => $item['hierarchy'],
                    'first_name' => Crypto::decryptData($item['first_name'], Crypto::getAwsEncryptionKey()),
                    'email' => Crypto::decryptData($item['email'], Crypto::getAwsEncryptionKey()),
                    'username' => Crypto::decryptData($item['username'], Crypto::getAwsEncryptionKey()),
                    'status' => $item['status']
                ];
            });
        // dd($SQL);
        /** -------- RESPONSE */
        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.users.grid', compact('paginate_OBJ'), ['_info' => $this->_info]);
        }
    }


    /**
     * *****************************************************************************************************************
     * @method users form
     * *****************************************************************************************************************
     */
    public function form()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = $this->model->find($id);
            if ($row->id <= 0) {
                return \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        }
        // return $row;

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
            ->get();

        $user_application_roles = DB::table('user_portal_role')
            ->select('*')
            ->where('user_id', '=', $id)
            ->get();

        $user_application_trials_sites_roles = DB::table('user_portal_trial_site')
            ->select('*')
            ->where('user_id', '=', $id)
            ->get();
        // dd($user_application_trials_sites_roles);

        /** -------- Response */
        return view('admin.users.form', compact('row', 'tenants', 'applications', 'roles', 'user_application_roles', 'user_application_trials_sites_roles'), ['_info' => $this->_info]);
    }

    // get application based on client
    public function getApplicationsByClientId(int $tenants_id)
    {

        // $client_application = DB::table('application_tenant')
        //     ->select('application_tenant.*', 'applications.id AS application_id', 'applications.application_name')
        //     ->join('applications', 'application_tenant.application_id', '=', 'applications.id')
        //     ->where('application_tenant.tenant_id', '=', $tenants_id)
        //     ->get();
        $client_application = Tenant::with('applications', 'applications.roles')->find(1);
        return response()->json($client_application);
    }

    public function getAllowedApplicationsForUser(int $user_id)
    {
        $user_allowed_apps = DB::table('user_portal_role')
            ->select('user_portal_role.*', 'applications.id AS application_id', 'applications.application_name')
            ->join('applications', 'user_portal_role.application_id', '=', 'applications.id')
            ->where('user_id', '=', $user_id)
            ->get();
        $tenants_id = $user_allowed_apps[0]->tenants_id;
        // $client_application = DB::table('application_tenant')
        //     ->select('application_tenant.*', 'applications.id AS application_id', 'applications.application_name')
        //     ->join('applications', 'application_tenant.application_id', '=', 'applications.id')
        //     ->where('application_tenant.tenant_id', '=', $tenants_id)
        //     ->get();
        $client_application = Tenant::select('id')->with('applications:id,application_name', 'applications.roles:user_type,id as user_type_id')->find($tenants_id);
        return response()->json(['user_allowed_apps' => $user_allowed_apps, 'client_application' => $client_application]);
    }

    public function getClinicalTrialsByClient(int $tenant_id)
    {
        $clinical_trials = DB::table('trials')
            ->select('id', 'study_name')
            ->where('tenants_id', '=', $tenant_id)
            ->get();

        foreach ($clinical_trials as $key => $value) {
            $clinical_trials[$key]->study_name =
                Crypto::decryptData($value->study_name, Crypto::getAwsEncryptionKey());
        }
        return response()->json($clinical_trials);
    }

    public function getSitesByClinicalStudy(int $study_id)
    {
        $sites = DB::table('sites')
            ->select('sites.id', 'sites.site_name') //, 'user_portal_trial_site.*')
            ->join('sites_clinical_studies', 'sites.id', '=', 'sites_clinical_studies.site_id')
            ->where('sites_clinical_studies.clinical_trial_id', '=', $study_id)
            // ->leftJoin('user_portal_trial_site', 'user_portal_trial_site.site_id', '=', 'sites.id')
            // ->where('user_id', '=', request()->query('user_id'))
            ->get();

        foreach ($sites as $key => $value) {
            $sites[$key]->site_name =
                Crypto::decryptData($value->site_name, Crypto::getAwsEncryptionKey());
        }
        return response()->json($sites);
    }


    public function getPortalTrailSitesByUserId(int $user_id)
    {
        $data = DB::table('user_portal_trial_site')->where('user_id', $user_id)->get();
        return response($data, 200);
    }


    /**
     * *****************************************************************************************************************
     * @method users store | Insert & Update
     * *****************************************************************************************************************
     */
    public function store()
    {
        // dd(request()->all());
        $id = request()->input($this->id_key);
        // dd(explode("|", request()->input('sites')[0]));

        /** -------- Validation */
        $validator_rules = [
            'user_type_id' => "required",
            'first_name' => "required",
            'tenants_id' => "required",
            // 'user_application' => "required",
            'email' => "required",
            'username' => "required|unique:{$this->module},username,{$id},{$this->id_key}",
            'password' => "required|min:8|max:30",
            'country_code' => "required",
            'phone' => "required",
        ];

        if ($id > 0) {
            unset($validator_rules['password']);
        }
        $validator = Validator::make(request()->all(), $validator_rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = DB_FormFields($this->model);

        $data['data']['user_type_id'] = request()->input('user_type_id');
        $data['data']['password'] = request()->input('password');
        $data['data']['tenants_id'] = request()->input('tenants_id');
        $data['data']['is_standalone'] = request()->input('is_standalone');
        $data['data']['username'] = request()->input('username');
        $data['data']['country_code'] = request()->input('country_code');
        $data['data']['email_hash'] = md5(Crypto::decryptData($data['data']['email'], Crypto::getAwsEncryptionKey()));

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

        // dd($role_ids);
        // dd($application_ids);
        // dd($application_and_role);
        // dd($clinical_studies_ids);
        // dd($sites_ids);
        // dd($trial_and_site);

        /** -------- Upload Files */
        if (!empty($data['data']['password'])) {
            $reg = "/(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/";

            if(!preg_match($reg, request()->password)) {
                return redirect()->back()->withErrors("Password must have at least one character one numric and one special character")->withInput();
            }

            if(request()->password !== request()->password_confirmation){
                return redirect()->back()->withErrors("Password and confirmation password do not match")->withInput();
            }

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
                    $data_user_portal_role = ['user_id' => $id, 'tenants_id' => $tenant_id, 'application_id' => $application_ids[$i], 'application_role' => $role_ids[$i]];
                    $insertedId = DB::table('user_portal_role')->insertGetId($data_user_portal_role);
                    activity_log('Add', 'user_portal_role', $insertedId, 0, null, $data_user_portal_role);
                }

                if ($clinical_studies_ids) {
                    for ($a = 0; $a < count($sites_ids); $a++) {
                        $data_user_portal_trial_site = ['user_id' => $id, 'tenants_id' => $tenant_id, 'tenant_code' => $login_code[0]->login_code, 'application_id' => explode("|", request()->input('sites')[$a])[0], 'trial_id' => explode("|", request()->input('sites')[$a])[2], 'site_id' => explode("|", request()->input('sites')[$a])[1]];
                        $insertedId = DB::table('user_portal_trial_site')->insertGetId($data_user_portal_trial_site);
                        activity_log('Add', 'user_portal_trial_site', $insertedId, 0, null, $data_user_portal_trial_site);
                    }
                }

                // foreach ($application_and_role as $application_id => $role_id) {
                //     DB::table('user_portal_role')->insert([
                //         ['user_id' => $id, 'client_id' => $client_id, 'application_id' => $application_id, 'application_role' => $role_id]
                //     ]);
                // }
                // // For Trials and Sites
                // if ($clinical_studies_ids) {
                //     foreach ($trial_and_site as $trial_id => $site_id) {
                //         DB::table('user_portal_trial_site')->insert([
                //             ['user_id' => $id, 'client_id' => $client_id, 'trial_id' => $trial_id, 'site_id' => $site_id]
                //         ]);
                //     }
                // }

                set_notification('Record has been inserted!', 'success');
                activity_log('Add', $this->table, $id, 0, null, $data['data']);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {

                DB::table('user_portal_role')->where('user_id', '=', $id)->delete();
                // dd('delete');
                DB::table('user_portal_trial_site')->where('user_id', '=', $id)->delete();
                // dd('del');
                // Insert Relation Table logic Here!

                for ($i = 0; $i < count($role_ids); $i++) {
                    $data_user_portal_role = ['user_id' => $id, 'tenants_id' => $tenant_id, 'application_id' => $application_ids[$i], 'application_role' => $role_ids[$i]];
                    $rowId = DB::table('user_portal_role')->insertGetId($data_user_portal_role);
                    activity_log('Update', 'user_portal_role', $rowId, 0, null, $data_user_portal_role);
                }
                if ($clinical_studies_ids) {
                    for ($a = 0; $a < count($sites_ids); $a++) {
                        $data_user_portal_trial_site = ['user_id' => $id, 'tenants_id' => $tenant_id, 'tenant_code' => $login_code[0]->login_code, 'application_id' => explode("|", request()->input('sites')[$a])[0], 'trial_id' => explode("|", request()->input('sites')[$a])[2], 'site_id' => explode("|", request()->input('sites')[$a])[1]];
                        $rowId = DB::table('user_portal_trial_site')->insertGetId($data_user_portal_trial_site);
                        activity_log('Update', 'user_portal_trial_site', $rowId, 0, null, $data_user_portal_trial_site);
                    }
                }
                // foreach ($application_and_role as $application_id => $role_id) {
                //     DB::table('user_portal_role')->insert([
                //         ['user_id' => $id, 'client_id' => $client_id, 'application_id' => $application_id, 'application_role' => $role_id]
                //     ]);
                // }

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


    /**
     * *****************************************************************************************************************
     * @method Status
     * @unlink Delete Files (unlink)
     * *****************************************************************************************************************
     */
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

        return \Redirect::to($this->_route);
    }


    /**
     * *****************************************************************************************************************
     * @method users delete
     * *****************************************************************************************************************
     */
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


    /**
     * *****************************************************************************************************************
     * @method users view | Record
     * *****************************************************************************************************************
     */
    public function view()
    {
        $id = getUri(1);
        if ($id > 0) {
            $row = $this->model->find($id);
            if ($row->id <= 0) {
                return \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        } else {
            return \Redirect::to(admin_url('', true))->with('error', 'Invalid URL!');
        }

        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item("View -> id:[$id]");

        $data['title'] = $this->_info->title;
        $config['buttons'] = ['new', 'edit', 'delete', 'refresh', 'print', 'back'];
        $config['hidden_fields'] = ['created_by'];
        $config['image_fields'] = [
            'photo' => ['path' => asset_url("front/{$this->table}/"), 'size' => '128x128'],
        ];
        $config['custom_func'] = ['status' => 'status_field'];
        $config['attributes'] = [
            'id' => ['title' => 'ID'],
        ];

        activity_log(getUri(3), $this->table, $id, $data);

        if (request()->ajax()) {
            return $row;
        } else if (view()->exists('admin.modules.view')) {
            return view('admin.users.view', compact('row', 'config'), ['_info' => $this->_info]);
        } else {
            return view('admin.layouts.view_record', compact('row', 'config'), ['_info' => $this->_info]);
        }
    }

    /**
     * *****************************************************************************************************************
     * @method users AJAX actions
     * *****************************************************************************************************************
     */
    function ajax()
    {
        $action = request('action') ?? getUri(4);
        $id = request('id') ?? getUri(5);
        switch ($action) {
            case 'delete_img':
                $field = getUri(6);
                $del_img = [$field => asset_dir("front/{$this->table}/")];
                $JSON['status'] = delete_rows($this->table, [$this->id_key => $id], false, $del_img);
                $JSON['message'] = ucwords($field) . ' has been deleted!';
                break;
            case 'ordering':
                $field = array_keys($_GET)[0];
                $value = getVar($field)[$id];
                $JSON['status'] = $this->model->where($this->id_key, $id)->update([$field => $value]);
                $JSON['message'] = 'updated!';
                break;
            case 'validate':
                $field = array_keys($_GET)[0];
                $value = getVar($field);

                $row = \DB::table($this->table)->where($field, $value);
                if ($id > 0) {
                    $row = $row->where($this->id_key, $id);
                }
                $row = $row->first();
                if ($row->id > 0) {
                    exit('false');
                }
                exit('true');
                break;
        }

        echo json_encode($JSON);
    }


    /**
     * *****************************************************************************************************************
     * @method users import
     * *****************************************************************************************************************
     */

    public function duplicate()
    {
        $id = getUri(4);
        $OBJ = $this->model->find($id);
        $unique = [];
        $newOBJ = $OBJ->replicate($unique);

        $newOBJ->save();
        $newID = $newOBJ->id;

        return \Redirect::to(admin_url("form/{$newID}", true))->with('success', 'Record has been duplicated!');
    }

    /**
     * *****************************************************************************************************************
     * @method users import
     * *****************************************************************************************************************
     */
    public function import()
    {
        if (\request()->isMethod('POST')) {
            $import_CLS = "{$this->module}Import";
            Excel::import(new $import_CLS(), request()->file('file'));
            return \Redirect::to(admin_url('', true))->with('success', 'All records has been import!');
        } else {

            /** -------- Breadcrumb */
            Breadcrumb::add_item($this->_info->title, $this->_route);
            Breadcrumb::add_item("Import");

            return view('admin.layouts.import', ['_info' => $this->_info]);
        }
    }

    /**
     * *****************************************************************************************************************
     * @method users export
     * @type csv & xml
     * *****************************************************************************************************************
     */
    public function export()
    {
        $ext = 'csv';
        $OBJ = $this->model->all();
        return $OBJ->downloadExcel("{$this->module}.{$ext}", ucfirst($ext), true);
        //return Excel::download($OBJ, "{$this->module}.{$ext}");
    }


    public function file_upload()
    {

        $data = [];
        $dir = "assets/front/{$this->table}/";
        $files = upload_files(['photo'], $dir, ['ext' => gif, jpg, jpeg, png]);
        if (count($files) > 0) {
            foreach ($files as $name => $file) {
                if ($file) {
                    $data[$name]->name = $file->getFilename();
                    $data[$name]->image_url = $dir . $data[$name]->name;
                    $data[$name]->thumb_url = _img($dir . $data[$name]->name, 100, 100);
                    $data[$name]->title = $file->getFilename();
                    $data[$name]->size = $file->getSize();
                    $data[$name]->ext = $file->getClientOriginalExtension();
                } else {
                    $data[$name]->name = $file->getFilename();
                    $data[$name]->error = $file->error;
                }
            }
        }

        return $data;
    }
}
