<?php

namespace App\Http\Controllers\Admin;

use Breadcrumb;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Crypto;


class PortalLogController extends Controller
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

        if (user_do_action('self_records')) {
            $user_id = Auth::user()->id;
            $this->where .= " AND {$this->table}.user_id = '{$user_id}'";
        }
    }


    /**
     * *****************************************************************************************************************
     * @method portal_log index | Grid | listing
     * *****************************************************************************************************************
     */
    public function index()
    {
        /** -------- Breadcrumb */
        Breadcrumb::add_item($this->_info->title, $this->_route);

        /** -------- Pagination Config */
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'limit' => 10000, 'group' => 'portal_logs.' . $this->id_key])->merge(request()->query())->toArray();
        
        $paginate_OBJ = collect(DB::table('activity_logs')
                        ->select('activity_logs.id',
                        'activity_logs.activity',
                        'users.email',
                        'activity_logs.table',
                        'activity_logs.response',
                        )
                        ->leftjoin('users','activity_logs.user_id','=','users.id')
                        ->limit(10000)->orderBy('activity_logs.id', 'DESC')->get());
        

        $Response = $paginate_OBJ->transform(function($transform){
            $transform->email =  Crypto::decryptData($transform->email, Crypto::getAwsEncryptionKey());
            return $transform;
        });  

        
        /** -------- RESPONSE */
        if (!$Response){
            return \Redirect::to(admin_url('', true))->with('error', 'Failed to Fetch Data!');
        }else{
        return view('admin.portal_logs.grid  ', compact('Response'), ['_info' => $this->_info]);            
        }   
        
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

        return view('admin.portal_logs.form', compact('row'));
    }

      /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'name' => "required|unique:{$this->module},name,{$id},{$this->id_key}",
            'type' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = DB_FormFields($this->model);

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
                activity_log('Add', $this->table, $id);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("index/", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {
                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("index/", true));
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
            $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("index/", true));
            return redirect($__redirect)->with('success', 'Record has been Inserted!');;
        }
    }
    /**
     * *****************************************************************************************************************
     * @method portal_log view | Record
     * *****************************************************************************************************************
     */
    public function view()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = DB::table('activity_logs')->find($id);  
            
            // $row->response = json_encode($row->response,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            // dd($row);
            if ($row->id <= 0) {
                return \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        }

        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item("View -> id:[$id]");

        $data['title'] = $this->_info->title;
        $config['buttons'] = ['new', 'edit', 'delete', 'refresh', 'print', 'back'];
        $config['hidden_fields'] = ['created_by'];
        $config['image_fields'] = [
        ];
        $config['custom_func'] = ['status' => 'status_field'];
        $config['attributes'] = [
            'id' => ['title' => 'ID'],
            'user_id' => ['title' => 'User', 'wrap' => function($value, $field, $row){
                return '<a href="'.admin_url("users/view/{$value}").'">View -> '.$value.'</a>';
            }],
        ];

        if (request()->ajax()) {
            return $row;
        } else if (view()->exists('admin.modules.view')) {

            return view('admin.portal_logs.view', compact('row', 'config'), ['_info' => $this->_info]);
        } else {
            return view('admin.layouts.view_record', compact('row', 'config'), ['_info' => $this->_info]);
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
    /**
     * *****************************************************************************************************************
     * @method portal_log AJAX actions
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
}