<?php

/**
 * @property App\Client $model
 */

namespace App\Http\Controllers\Admin;

use App\EmailTemplate;
use Breadcrumb;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Str;
use Crypto;
use ZanySoft\Zip\Zip;
use Mail;
use App\User;
use Token;

class UserInfoController extends Controller
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
            //$this->where .= " AND {$this->table}.created_by = '{$user_id}'";
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;

        $tenant = DB::table('users')
            ->select('tenants.tenant_name')
            ->join('tenants', 'users.tenants_id', '=', 'tenants.id')
            ->where('users.id', $id)
            ->first();
        $tenant = Crypto::decryptData($tenant->tenant_name, Crypto::getAwsEncryptionKey());

        return view('admin.user_info.form', compact('tenant'), ['_info' => $this->_info]);
    }

    public function form()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = $this->model->find($id);
            // dd($row);
            if ($row->id <= 0) {
                \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        }

        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item(($id > 0) ? "Edit -> id:[$id]" : 'Add New');


        // dd($clients->);
        return view('admin.tokens.form', compact('row'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {

        $id = Auth::user()->id;

        $old_pass = request()->old_password;
        $new_pass = request()->new_password;
        $conf_pass = request()->conf_password;

        $two_factor = request()->two_factor;


        $validator = Validator::make(request()->all(), []);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $get_pass = DB::table('users')
            ->where('id', $id)
            ->select('password')
            ->first();


        if (isset($new_pass) && isset($conf_pass)) {
            // when password is given
            if (Hash::check($old_pass, $get_pass->password)) {

                $reg = "/(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/";

                if(!preg_match($reg, request()->password)) {
                    return redirect()->back()->withErrors("Password must have at least one character one numric and one special character")->withInput();
                }

                $upd_record =  DB::table('users')
                    ->where('id', $id)
                    ->update([
                        'first_name' => Crypto::encryptData(request()->first_name, Crypto::getAwsEncryptionKey()),
                        'last_name' => Crypto::encryptData(request()->last_name, Crypto::getAwsEncryptionKey()),
                        'pin_verified' => '1',
                        'two_factor_auth' =>  isset($two_factor) ? '1' : '0',
                        'password' => Hash::make($new_pass)
                    ]);
                activity_log('password update', 'users', Auth::id(), Auth::id(), 'success', $upd_record);
                return redirect()->back()->with('success', 'Record has been Updated!');
            } else {
                return redirect()->back()->with('error', 'Your Old Password is Wrong ');
            }
        } else {

            $upd_record =  DB::table('users')
                ->where('id', $id)
                ->update([
                    'first_name' => Crypto::encryptData(request()->first_name, Crypto::getAwsEncryptionKey()),
                    'last_name' => Crypto::encryptData(request()->last_name, Crypto::getAwsEncryptionKey()),
                    'pin_verified' => '1',
                    'two_factor_auth' =>  isset($two_factor) ? '1' : '0',
                ]);
            activity_log('profile update', 'users', Auth::id(), Auth::id(), 'success', $upd_record);

            return redirect()->back()->with('success', 'Record has been Updated!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Module $module
     * @return \Illuminate\Http\Response
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

        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item("View -> id:[$id]");

        if (view()->exists('admin.modules.view')) {
            return view('admin.modules.view', compact('row'));
        } else {
            return view('admin.layouts.view_record', compact('row'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Module $module
     * @return \Illuminate\Http\Response
     */
    public function delete()
    {
        $id = getUri(4);
        $ids = request()->input('ids');
        if ($id > 0) {
            $ids = [$id];
        }
        if ($ids == null || count($ids) == 0) {
            return redirect()->back()->with('danger', 'Select items');
        }

        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ")", true);

        $alert = ['class' => 'success', 'message' => "Record has been deleted!"];
        //$this->model->whereIn($this->id_key, $ids)->delete();

        return \Redirect::to(admin_url('index', true))->with($alert['class'], $alert['message']);
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

        $alert = ['class' => 'success', 'message' => "Record status has been updated!"];

        return \Redirect::to(admin_url('index', true))->with($alert['class'], $alert['message']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Module $module
     * @return \Illuminate\Http\Response
     */
    public function duplicate()
    {
        $id = getUri(4);
        $OBJ = $this->model->find($id);
        $newOBJ = $OBJ->replicate();
        $newOBJ->save();
        $newID = $newOBJ->id;

        return \Redirect::to(admin_url("form/{$newID}", true))->with('success', 'Record has been duplicated!');
    }

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
    }

    /**
     * export
     */
    public function export()
    {
        $ext = 'csv';
        $OBJ = $this->model->all();
        return $OBJ->downloadExcel("{$this->module}.{$ext}", ucfirst($ext), true);
        //return Excel::download($OBJ, "{$this->module}.{$ext}");
    }

    /**
     * export
     */
    public function import()
    {
        if (\request()->isMethod('POST')) {
            $import_CLS = "{$this->module}Import";
            Excel::import(new $import_CLS(), request()->file('file'));
            return \Redirect::to(admin_url('', true))->with('success', 'All records has been import!');
        } else {

            /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
             * | Breadcrumb
             *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
            Breadcrumb::add_item($this->_info->title, $this->_route);
            Breadcrumb::add_item("Import");

            return view('admin.layouts.import');
        }
    }


    function export_module($id)
    {
        $row = $this->model->find($id);
        $export_dir = "export_module";
        $path = base_path("{$export_dir}/{$row->module}/");

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $controller = Str::studly(Str::singular($row->module)) . "Controller";
        $model = Str::studly(Str::singular($row->module));

        $zip_data['file']['app/'] = base_path("app/{$model}.php");
        $zip_data['file']['app/Http/Controllers/Admin/'] = base_path("app/Http/Controllers/Admin/{$controller}.php");
        $zip_data['dir']['resources/views/' . config('app.admin_dir') . "/{$row->module}"] = base_path("resources/views/" . config('app.admin_dir') . "/{$row->module}");

        $M_file = File::glob(base_path("database/migrations/*create_{$row->module}_table.php"));
        $zip_data['file']['database/migrations/'] = $M_file[0];
        $zip_data['file']['database/factories/'] = base_path("database/factories/{$model}Factory.php");


        foreach ($zip_data as $type => $zip_files) {
            foreach ($zip_files as $file_path => $zip_file) {

                if ($type == 'file') {
                    $file_name = (basename($zip_file));
                    if (!is_dir($path . $file_path)) {
                        mkdir($path . $file_path, 0777, true);
                    }
                    File::copy($zip_file, "{$path}{$file_path}/{$file_name}");
                } else {
                    foreach (glob("{$zip_file}/*.*") as $item) {
                        $file_name = basename($item);
                        if (!is_dir($path . $file_path)) {
                            mkdir($path . $file_path, 0777, true);
                        }
                        File::copy($item, "{$path}{$file_path}/{$file_name}");
                    }
                }
            }
        }


        $backup = '';
        $module = \DB::selectOne("SELECT * FROM modules WHERE module='{$row->module}'");
        $module_row = collect($module)->toArray();
        unset($module_row['id']);
        $module_insert = insert_string('modules', $module_row);

        $backup .= $module_insert . ';' . "\n\n";

        $icon_path = asset_dir("media/icons/", true);
        if (File::exists($icon_path . $module->image)) {
            $img_path = $path . str_replace(base_path() . "\\", '', $icon_path);
            if (!is_dir($img_path)) {
                mkdir($img_path, 0777, true);
            }
            File::copy("{$icon_path}{$module->image}", "{$img_path}/{$module->image}");
        }

        if (\Schema::hasTable($row->module)) {
            $table = $row->module;
        } else {
            $model = 'App\\' . \Str::studly(\Str::singular($row->module));
            $model = new $model;
            $table = $model->getTable();
        }

        $newline = "\n";
        $backup .= '#' . $newline . '# TABLE STRUCTURE FOR: ' . $table . $newline . '#' . $newline . $newline;
        if ($prefs['add_drop'] == TRUE) {
            $backup .= 'DROP TABLE IF EXISTS ' . $table . ';' . $newline . $newline;
        }
        $backup .= \DB::selectOne("SHOW CREATE TABLE `{$table}`")->{'Create Table'} . ";" . $newline;

        $db_backup_file = "{$path}/{$row->module}.sql";
        file_put_contents($db_backup_file, $backup);

        $zip_file = base_path("{$export_dir}/{$row->module}.zip");
        if (File::exists($zip_file)) {
            File::delete($zip_file);
        }

        $zip = Zip::create($zip_file);
        $zip->add($path, true);
        $zip->close();

        File::deleteDirectory($path);
        return Response::download($zip_file);
    }

    function import_module()
    {
        $file = upload_files(['file'], 'export_module/import/')['file'];

        if ($file) {
            $filename = $file->getFilename();
            $full_path = $file->getRealPath();
            $extractPath = './';

            $zip = Zip::open($full_path);


            $zip_files = $zip->listFiles();
            if (count($zip_files) > 0) {
                foreach ($zip_files as $zip_file) {
                    if (Str::contains($zip_file, '.sql')) {
                        $sql_file = $zip_file;
                    }
                }
            }
            // Extract file
            $zip->extract($extractPath);
            $zip->close();

            if (!empty($sql_file)) {
                $module_name = ucwords(str_replace(['.sql', '_'], ['', ' '], basename($sql_file)));
                $sql_query = file_get_contents($sql_file);
                $queries = explode(';', $sql_query);
                array_pop($queries);

                foreach ($queries as $key => $statement) {
                    $statement = $statement . ";";
                    if ($key == 0 && \request('insert_module') === 1) {
                        \DB::statement($statement);
                        $module_id = inserted_id();
                        $row = $this->model->find($module_id);
                        \DB::table('user_type_module_rel')->insert(['user_type_id' => 1, 'module_id' => $module_id, 'actions' => $row->actions]);
                    } else if (\request('create_table') === 1) {
                        \DB::statement($statement);
                    }
                }
                File::delete($sql_file);
            }
            File::delete($full_path);
            set_notification("<span class='badge badge-warning' style='font-size: 100%;'>{$module_name}</span> module has been imported!", 'success');
        } else {
            set_notification('Upload zip file!');
        }
        return redirect()->back();
    }


    public function check_password($old_pass)
    {

        $id = request()->input('id');

        $get_pass = DB::table('users')
            ->where('id', $id)
            ->select('password')
            ->first();

        if (Hash::check($old_pass, $get_pass->password)) {
            return "True";
        } else {
            return "False";
        }
    }

    private function sendConfirmationMail($user, $sub)
    {
        $email = Crypto::decryptData($user[0]->email, Crypto::getAwsEncryptionKey());

        // dd($sub);
        $data = array(
            'url' => admin_url() . "/login/resetLink/" . Token::generateRandomString()
        );
        $emailTemplate = EmailTemplate::where('name','Password Changed Successfully')->where('status','Active')->first();
        if(isset($emailTemplate)){
        $html = $emailTemplate->message;
        $output = preg_replace_callback(
            '~\{(.*?)\}~',
            function ($key) {
                $variable["opt('site_title')"] = opt('site_title');
                $variable["opt('site_url')"] = opt('site_url');
                $variable["url"] = admin_url() . '/login/';
                if ($variable[$key[1]] != null) {
                    return $variable[$key[1]];
                }else{
                    return "";
                    return $key[0];
                }
            },
            $html,
        );
        $blade_from_db = view('admin.mails.dynamic_mail',['data',$data,'email' => $email,'html' => $output])->render();
            // return $blade_from_db;
        Mail::send([], $data, function ($message) use ($email, $sub,$blade_from_db) {
            $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
            $message->to($email, 'Evo-User')->subject($sub);
            $message->bcc(getSupportEmails());
            $message->setBody($blade_from_db,'text/html');
        });
    }
    }

    public function change_pass_first_attempt()
    {
        return view('admin.auth.change_password_first_attempt');
    }

    public function pass_change_first_attempt()
    {
        $id = Auth::user()->id;
        $username = Auth::user()->username;
        $old_pass = request()->old_password;
        $new_pass = request()->new_password;
        // dd($old_pass);
        // dd($new_pass);
        $validator = Validator::make(request()->all(), [
            'old_password' => "required",
            'new_password' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $reg = "/(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/";

        $get_pass = DB::table('users')
            ->where('id', $id)
            ->select('password')
            ->first();

        $email_hash = md5($username);

        if (Hash::check($old_pass, $get_pass->password)) {
            if(!preg_match($reg, $new_pass)) {
                return redirect()->back()->withErrors("Password must have at least one character one numric and one special character")->withInput();
            }
            $upd_record =  DB::table('users')
                ->where('id', $id)
                ->update([
                    'password' => Hash::make($new_pass),
                    'login_count' => '1',
                    'default_password_updated' => 1
                ]);

            // Epro pass change curl call
            $participant_erx_id = DB::table('participants')
                ->select('erx_id')
                ->where('email_hash', '=', $email_hash)
                ->pluck('erx_id')
                ->first();

            $postRequest['listingParameter'] = array(
                'erx_id' => $participant_erx_id,
                'password' => $new_pass
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


            $this->sendConfirmationMail(DB::table('users')->where('id', $id)->get(), opt('site_title').' - Change-Password');
            return redirect()->intended(admin_url('dashboard'))->with('success', 'Password has been changed successfully!');
        } else {
            // dd('!matched');
            // Auth::logout();
            return redirect()->back()->withErrors('Old Password Did Not Matched')->withInput();
        }
    }

    public function pin_confirmation()
    {
        return view('admin.auth.pin_confirmation');
    }

    public function pin_confirmation_post()
    {
        $currentTime = date('Y-m-d H:i:s');
        // dd($currentTime >= auth()->user()->pin_expire_at);
        if (auth()->user()->login_pin !== request()->input('pin')) {
            return redirect(admin_url('user_info/pin_confirmation'))->withErrors("Invalid Pin");
        }

        if ($currentTime >= auth()->user()->pin_expire_at) {
            return redirect(admin_url('user_info/pin_confirmation'))->withErrors("Looks like something is wrong with your pin");
        }

        $user = auth()->user();

        $user->update([
            'pin_verified' => 1,
            'login_pin' => '',
            'pin_expire_at' => ''
        ]);

        return redirect(admin_url('dashboard'));
        // 2021-12-27 21:16:43
    }
}
