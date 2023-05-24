<?php

/**
 * @property App\Module $model
 */

namespace App\Http\Controllers\Admin;

use App\EmailTemplate;
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

class EmailTemplateController extends Controller
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
        $this->model = new $model();
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
        /** -------- Breadcrumb */
        Breadcrumb::add_item($this->_info->title, $this->_route);

        /** -------- Pagination Config */
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'group' => 'email_templates.' . $this->id_key])
            ->merge(request()->query())
            ->toArray();

        /** -------- Query */
        $select = "email_templates.id
, email_templates.name
, email_templates.subject
, email_templates.status
";
        $SQL = $this->model->select(\DB::raw($select));

        /** -------- WHERE */
        $where = $this->where;
        $where .= getWhereClause($select);
        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

        $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);

        $paginate_OBJ = $SQL->paginate(10000);

        /** -------- RESPONSE */
        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.email_templates.grid', compact('paginate_OBJ'), ['_info' => $this->_info]);
        }
    }
    public function setVariableOLD($data)
    {
        $string = $data;
        $matches = array();
        preg_match_all('~\{(.*?)\}~', $string, $matches);
        dd($matches[1]);
        $output = preg_replace_callback(
            '~\{(.*?)\}~',
            function ($key) use($matches) {
                // dd($key[1]);
                foreach($matches[1] as $match){
                    dd($match);
                }
                $variable['test'] = 'Banana';
                $variable['sample'] = 'hi';
                return $variable[$key[1]];
            },
            $string,
        );
        echo $output;
    }
    public function setVariable($data)
    {
        $string = $data;
        $output = preg_replace_callback(
            '~\{(.*?)\}~',
            function ($key) {
                // return $key[0];
                $variable['test'] = 'Banana';
                $variable['sample'] = 'hi';
                $variable['opt("fie")'] = opt('site_title');
                $variable['opt("fiwwe")'] = 'h2i';
                // dd($variable);
                if ($variable[$key[1]] != null) {
                    return $variable[$key[1]];
                }else{
                    return $key[0];
                }
            },
            $string,
        );
        echo $output;
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
        $applications = DB::table('applications')->get();
        // dd($row);
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item($id > 0 ? "Edit -> id:[$id]" : 'Add New');

        return view('admin.email_templates.form', compact('row','applications'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'name' => 'required|max:255',
            'subject' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = DB_FormFields($this->model);
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | File Upload
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        $files = upload_files(['image', 'icon'], 'assets/admin/media/icons');

        foreach ($files as $name => $file) {
            if ($file) {
                $data['data'][$name] = $file->getFilename();
            }
        }

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
            } else {
                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id, 0, null, $data['data']);
            }
        } else {
            session()->flash('error', 'Some error occurred!');
        }

        if (request()->ajax()) {
            $alert_types = ['success', 'error' => 'danger', 'warning', 'primary', 'info', 'brand'];
            $alerts = collect(session('errors')->all())->append(
                collect($alert_types)->map(function ($val, $key) {
                    return session($val);
                }),
            );
            return $alerts;
        } else {
            $__redirect = !empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true);
            return redirect($__redirect);
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

        if (view()->exists('admin.email_templates.view')) {
            return view('admin.email_templates.view', compact('row'));
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
            return redirect()
                ->back()
                ->with('danger', 'Select items');
        }

        $unlink = ['icon' => asset_dir('media/icons/', true), 'image' => asset_dir('media/icons/', true)];
        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ')', true, $unlink);

        $alert = ['class' => 'success', 'message' => 'Record has been deleted!'];
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
        foreach ($ids as $id) {
            activity_log('update', 'module', $id, 0, null, $data);
        }
        $alert = ['class' => 'success', 'message' => 'Record status has been updated!'];

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
}
