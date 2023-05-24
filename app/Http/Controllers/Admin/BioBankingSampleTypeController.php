<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Auth;
use Breadcrumb;
use Crypto;
use DB;
use Illuminate\Http\Request;
use Validator;

class BioBankingSampleTypeController extends Controller
{


    public $module = ''; //Project module name
    public $_info = null; // Project module info
    public $_route = '';

    public $model = null; // Object
    public $table = ''; // Object
    public $id_key = ''; // Object
    // public $user_actions = '';
    // public $actions = '';

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

    public function index()
    {

        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'group' => 'bio_banking_sample_types.' . $this->id_key])->merge(request()->query())->toArray();

        $login_code = Auth::user()->userclient['login_code'];
        $user_client_id = Auth::user()->userclient['id'];
        $user_type = Auth::user()->user_type_id;
        $all_data_access = Auth::user()->is_allowed_all_data_access;


        $select = "bio_banking_sample_types.id
        , bio_banking_sample_types.name
        , bio_banking_sample_types.color";

        $where = $this->where;
        $where .= getWhereClause($select);

        $SQL = $this->model->select(\DB::raw($select));

        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

        $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);
        $paginate_OBJ = $SQL->paginate(10000);


        $query = $SQL->toSql();
        // dd($this->module);
        $user_type = Auth::user()->user_type_id;

        // // dd($user_type);
        // if ($user_type == 1) {
        //     $tokens = $this->model::orderBy('id', 'desc');
        // } else {
        //     $tokens = $this->model::where('tenant_code', Auth::user()->userclient['login_code'])
        //         ->where('tenant_id', Auth::user()->userclient['id'])
        //         ->orderBy('id', 'desc');
        // }

        // dd($tokens);
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        $paginate_OBJ
            ->getCollection()
            ->transform(function ($item, $key) {
                return [
                    'id' => $item['id'],
                    'name' => Crypto::decryptData($item['name'], Crypto::getAwsEncryptionKey()),
                    'color' => Crypto::decryptData($item['color'], Crypto::getAwsEncryptionKey()),
                ];
            });

        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.bio_banking_smple_types.grid', compact('paginate_OBJ', 'query', 'tokens'), ['_info' => $this->_info]);
        }
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

        $sample_types_rel = DB::table('bio_banking_test_result_children')
            ->select('*')
            ->where('parent_id', '=', $id)
            ->get();


        return view('admin.bio_banking_smple_types.form', compact('row', 'sample_types_rel'));
    }

    public function store()
    {
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'name' => "required|unique:{$this->module},name,{$id},{$this->id_key}",
            'color' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $data = DB_FormFields($this->model);
        $data['data']['name'] = request()->input('name');
        $data['data']['color'] = request()->input('color');



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
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("index/", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {

                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id, 0, null, $data['data']);
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
            $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
            return redirect($__redirect)->with('success', 'Record has been Inserted!');;
        }
    }

    public function delete()
    {
        $id = getUri(4);
        if ($id > 0) {
            $ids = [$id];
        }
        if ($ids == null || count($ids) == 0) {
            return redirect()->back()->with('danger', 'Select items');
        }
        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ")", true);

        set_notification('Record has been deleted!', 'success');
        $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("index/", true));
        return redirect($__redirect)->with('success', 'Record has been Updated!');
    }
}
