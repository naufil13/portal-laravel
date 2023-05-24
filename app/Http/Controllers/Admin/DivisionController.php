<?php

/**
 * @property App\Division $model
 */

namespace App\Http\Controllers\Admin;

use Breadcrumb;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Str;
use ZanySoft\Zip\Zip;
use Crypto;

class DivisionController extends Controller
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
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'group' => 'divisions.' . $this->id_key])->merge(request()->query())->toArray();

        $login_code = Auth::user()->userclient['login_code'];
        $user_client_id = Auth::user()->userclient['id'];
        $user_type = Auth::user()->user_type_id;
        $all_data_access = Auth::user()->is_allowed_all_data_access;

        if ($all_data_access != 1) {
            $this->where .= " AND {$this->table}.tenant_code='{$login_code}' AND {$this->table}.tenant_id={$user_client_id}";
        }

        //$SQL = "";
        $select = "divisions.id
        , divisions.division_name
        , cities.name AS city
        , states.name AS state
        , divisions.zip_code
        , countries.name AS country";
        $where = $this->where;
        $where .= getWhereClause($select);

        $SQL = $this->model->select(\DB::raw($select));

        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }
        $SQL = $SQL->join('cities', 'divisions.city_id', '=', 'cities.id');
        $SQL = $SQL->join('states', 'divisions.state_id', '=', 'states.id');
        $SQL = $SQL->join('countries', 'divisions.country_id', '=', 'countries.id');
        // dd($SQL->get());

        $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);
        $paginate_OBJ = $SQL->paginate(10000);


        $query = $SQL->toSql();
        // dd($query);
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);

        $paginate_OBJ
            ->getCollection()
            ->transform(function ($item, $key) {
                return [
                    'id' => $item['id'],
                    'division_name' => Crypto::decryptData($item['division_name'], Crypto::getAwsEncryptionKey()),
                    'city' => Crypto::decryptData($item['city'], Crypto::getAwsEncryptionKey()),
                    'state' => Crypto::decryptData($item['state'], Crypto::getAwsEncryptionKey()),
                    'zip_code' => Crypto::decryptData($item['zip_code'], Crypto::getAwsEncryptionKey()),
                    'country' => Crypto::decryptData($item['country'], Crypto::getAwsEncryptionKey())
                ];
            });

        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.divisions.grid', compact('paginate_OBJ', 'query'), ['_info' => $this->_info]);
        }
    }

    public function getStateByCountry(int $country_id)
    {
        $states = DB::table('states')
            ->select('id', 'name')
            ->where('country_id', '=', $country_id)
            ->get();
        return response()->json($states);
    }

    public function getCityByState(int $state_id)
    {
        $states = DB::table('cities')
            ->select('id', 'name')
            ->where('state_id', '=', $state_id)
            ->get();
        return response()->json($states);
    }

    public function getTenantCodeById(int $id)
    {
        $login_code = DB::table('tenants')
            ->select('login_code')
            ->where('id', '=', $id)
            ->get();
        return $login_code;
    }

    public function form()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = $this->model->find($id);
            if ($row->id <= 0) {
                \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        }
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item(($id > 0) ? "Edit -> id:[$id]" : 'Add New');
        $tenants = DB::table('tenants')
            ->select('id', 'tenant_name')
            ->get();

        return view('admin.divisions.form', compact('row', 'tenants'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        // dd(request()->all());
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'division_name' => "required|max:255",
            'address' => 'required|max:255',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'phone' => 'required',
            'contact_email' => 'required|email',
            'contact_phone' => 'required',
            'zip_code' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = DB_FormFields($this->model);
        $data['data']['tenant_code'] = Auth::user()->userclient['login_code'];
        $data['data']['tenant_id'] = Auth::user()->userclient['id'];
        $data['data']['created_by'] = Auth::user()->email;
        $data['data']['country_id'] = request()->input('country_id');
        $data['data']['state_id'] = request()->input('state_id');
        $data['data']['city_id'] = request()->input('city_id');

        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | File Upload
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        if ($id > 0) {
            $row = $this->model->find($id);
            $row = $row->fill($data['data']);
        } else {
            $row = $this->model->fill($data['data']);
        }

        if ($row->save()) {

            if ($id == 0) {
                $id = $row->{$this->id_key};
                set_notification('Record has been inserted!', 'success');
                activity_log('Add', $this->table, $id, 0, null, $data['data']);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {
                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id, 0, null, $data['data']);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('success', 'Record has been Updated!');
            }
        } else {
            session()->flash('error', 'Some error occurred!');
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

            activity_log('status', $this->table, $ids, 0, null, $data);
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
}
