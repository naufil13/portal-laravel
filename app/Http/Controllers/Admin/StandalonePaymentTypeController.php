<?php

/**
 * @property App\Client $model
 */

namespace App\Http\Controllers\Admin;

use Breadcrumb;
use Crypt;
use Crypto;
use File;
use Grid_btn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Str;
use ZanySoft\Zip\Zip;
use Uri_func;

class StandalonePaymentTypeController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_types = $this->model
            ->where('status', '!=', 'deleted')
            ->orWhere('status', null)
            ->get();

        return view('admin.standalonePaymentTypes.grid', compact('payment_types'), ['_info' => $this->_info]);
    }

    public function form()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = $this->model->find($id);
            // dd(Crypto::decryptData($row->name, Crypto::getAwsEncryptionKey()));
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
        return view('admin.standalonePaymentTypes.form', compact('row'));
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
            'amount' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = DB_FormFields($this->model);
        // dd($data);

        $data['data']['status'] = request()->input('status');
        $data['data']['payment_type'] = request()->input('payment_type');;
        $data['data']['amount'] = request()->input('amount');;

        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | File Upload
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        if ($id > 0) {
            $data['data']['status'] = 'edited';
            $data['data']['updated_by'] = Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey());
            $row = $this->model->find($id);
            $row = $row->fill($data['data']);
        } else {
            $data['data']['created_by'] = Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey());
            $row = $this->model->fill($data['data']);
        }

        if ($row->save()) {
            if ($id == 0) {
                $id = $row->{$this->id_key};
                set_notification('Record has been inserted!', 'primary');
                activity_log('Add', $this->table, $id, 0, null, $data['data']);
                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {
                set_notification('Record has been updated!', 'primary');
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
     * Remove the specified resource from storage.
     *
     * @param \App\Module $module
     * @return \Illuminate\Http\Response
     */
    public function delete()
    {
        $id = getUri(4);


        $row = $this->model->find($id);
        $row->update(['status' => 'deleted']);

        $alert = ['class' => 'success', 'message' => "Record has been deleted!"];
        //$this->model->whereIn($this->id_key, $ids)->delete();

        return \Redirect::to(admin_url('index', true))->with($alert['class'], $alert['message']);
    }
}
