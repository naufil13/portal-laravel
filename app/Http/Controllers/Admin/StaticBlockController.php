<?php
/**
 * Class Static_blocks * @property App\Static_block $module
 */

namespace App\Http\Controllers\Admin;

use Breadcrumb;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class StaticBlockController extends Controller
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
            $this->where .= " AND {$this->table}.created_by = '{$user_id}'";
        }
    }


    /**
     * *****************************************************************************************************************
     * @method static_blocks index | Grid | listing
     * *****************************************************************************************************************
     */
    public function index()
    {
        /** -------- Breadcrumb */
        Breadcrumb::add_item($this->_info->title, $this->_route);

        /** -------- Pagination Config */
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'limit' => 25, 'group' => 'static_blocks.' . $this->id_key])->merge(request()->query())->toArray();

        /** -------- Query */
        $select = "static_blocks.id
, static_blocks.title
, static_blocks.identifier
, static_blocks.status
";
        $SQL = $this->model->select(\DB::raw($select));

        /** -------- WHERE */
        $where = $this->where;
        $where .= getWhereClause($select);

        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

        $SQL = $SQL->orderBy($config['sort'], $config['dir']);//->groupBy($config['group']);

        $paginate_OBJ = $SQL->paginate($config['limit']);

        /** -------- RESPONSE */
        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.static_blocks.grid', compact('paginate_OBJ'), ['_info' => $this->_info]);
        }
    }


    /**
     * *****************************************************************************************************************
     * @method static_blocks form
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

        /** -------- Breadcrumb */
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item(($id > 0) ? "Edit -> id:[$id]" : 'Add New');

        /** -------- Response */
        return view('admin.static_blocks.form', compact('row'));
    }


    /**
     * *****************************************************************************************************************
     * @method static_blocks store | Insert & Update
     * *****************************************************************************************************************
     */
    public function store()
    {
        $id = request()->input($this->id_key);

        /** -------- Validation */
        $validator = Validator::make(request()->all(), [
            'title' => "required",
            'identifier' => "required|unique:{$this->module},identifier,{$id},{$this->id_key}|max:255",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $data = DB_FormFields($this->model);

        /** -------- Upload Files */
        $files = upload_files([], "assets/front/{$this->table}/");
        if (count($files) > 0) {
            foreach ($files as $name => $file) {
                if ($file) {
                    $data['data'][$name] = $file->getFilename();
                }
            }
        }

        if ($id > 0) {
            $row = $this->model->find($id);
            $row = $row->fill($data['data']);
        } else {
            $row = $this->model->fill($data['data']);
        }

        if ($row = $row->save()) {
            if ($id == 0) {
                set_notification('Record has been inserted!', 'success');
                activity_log('Add', $this->table, $row->{$this->id_key});
            } else {
                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $row->{$this->id_key});
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
            return redirect($__redirect);
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

        activity_log(getUri(3), $this->table, $ids);

        return \Redirect::to(admin_url($this->_route));
    }


    /**
     * *****************************************************************************************************************
     * @method static_blocks delete
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

        $unlink = [];
        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ")", true, $unlink);
        //$this->model->whereIn($this->id_key, $ids)->delete();

        activity_log(getUri(3), $this->table, $ids);

        return \Redirect::to(admin_url('index', true))->with('success', 'Record has been deleted!');
    }


    /**
     * *****************************************************************************************************************
     * @method static_blocks view | Record
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
        ];
        $config['custom_func'] = ['status' => 'status_field'];
        $config['attributes'] = [
            'id' => ['title' => 'ID'],
        ];

        activity_log(getUri(3), $this->table, $id);

        if (request()->ajax()) {
            return $row;
        } else if (view()->exists('admin.modules.view')) {
            return view('admin.static_blocks.view', compact('row', 'config'));
        } else {
            return view('admin.layouts.view_record', compact('row', 'config'));
        }
    }

    /**
     * *****************************************************************************************************************
     * @method static_blocks AJAX actions
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
     * @method static_blocks import
     * *****************************************************************************************************************
     */

    public function duplicate()
    {
        $id = getUri(4);
        $OBJ = $this->model->find($id);
        $unique = ['identifier'];
        $newOBJ = $OBJ->replicate($unique);
        foreach ($unique as $item) {
            $newOBJ->{$item} = '';
        }
        $newOBJ->save();
        $newID = $newOBJ->id;

        return \Redirect::to(admin_url("form/{$newID}", true))->with('success', 'Record has been duplicated!');
    }

    /**
     * *****************************************************************************************************************
     * @method static_blocks import
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

            return view('admin.layouts.import');
        }
    }

    /**
     * *****************************************************************************************************************
     * @method static_blocks export
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

        if (!empty($_FILES)) {
            /**
             * Directory
             */
            $dir = "assets/front/{$this->table}/";
            if (!is_dir($dir)) mkdir($dir);
            $id = intval(getVar('id'));
            /*if ($id > 0) { $dir .= $id . '/'; mkdir($dir); }*/

            $config['upload_path'] = './' . $dir;
            $config['allowed_types'] = '';

            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $fileinfo = $this->upload->data();
                $output['result']['filename'] = $fileinfo['file_name'];

                $thumb_file = _img(base_url(file_icon($dir . $fileinfo['file_name'], true)), 200, 200);

                $output['result']['thumb_url'] = $thumb_file;
                $output['result']['image_url'] = site_url($dir . $fileinfo['file_name']);
                $output['result']['title'] = substr(str_replace(array('-', '_'), array(' ', ' '), $fileinfo['file_name']), 0, -(strlen($fileinfo['file_ext'])));
                $output['result']['size'] = $fileinfo['file_size'];
                $output['result']['file_ext'] = $fileinfo['file_ext'];
            } else {
                $output['error']['filename'] = $_FILES['file']['name'];
                $output['error']['message'] = $this->upload->display_errors();
            }

            echo json_encode($output);
        } else {
            \Redirect::to(admin_url($this->_route));
        }
    }

}
