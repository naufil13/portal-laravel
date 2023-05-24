<?php
/**
 * Class Member
 * @property App\Member $module
 */

namespace App\Http\Controllers\Admin;

use Breadcrumb;
use Grid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
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
     * @method members index | Grid | listing
     * *****************************************************************************************************************
     */
    public function index()
    {
        try {

            $association_id = intval(getVar('association_id'));

            if ($association_id > 0) {
                $this->where .= " AND members.association_id='{$association_id}'";
            }
            /** -------- Breadcrumb */
            Breadcrumb::add_item($this->_info->title, $this->_route);

            /** -------- Pagination Config */
            $config = collect(['sort' => 'members.' . $this->id_key, 'dir' => 'desc', 'limit' => 25, 'group' => 'members.' . $this->id_key])->merge(request()->query())->toArray();

            /** -------- Query */
            $select = "members.id";
            if ($association_id == 0) {
                //$select .= ", associations.name AS association";
            }

            $select .= ", associations.name AS association
            , members.company
            -- , members.name
            , members.logo
            , members.joining_date
            -- , members.website
            , members.city
            , members.emails->>'$[0]' AS email
            , members.phones->>'$[0]' AS phone
            -- , JSON_EXTRACT(members.phones, '$[0]') AS phone
            ";
            $SQL = $this->model->select(\DB::raw($select));

            /** -------- WHERE */
            $where = $this->where;
            $where .= getWhereClause($select);
            if (!empty($where)) {
                $SQL = $SQL->whereRaw($where);
            }

            $SQL = $SQL->leftJoin('associations', 'associations.id', '=', 'members.association_id');
            $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);
            $paginate_OBJ = $SQL->paginate($config['limit']);
            $query = $SQL->toSql();



            /** -------- RESPONSE */
            if (request()->ajax()) {
                return $paginate_OBJ;
            } else {
                return view('admin.members.grid', compact('paginate_OBJ', 'query'), ['_info' => $this->_info]);
            }
        } catch (\Exception $e) {

        }
    }


    /**
     * *****************************************************************************************************************
     * @method members form
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
        return view('admin.members.form', compact('row'), ['_info' => $this->_info]);
    }


    /**
     * *****************************************************************************************************************
     * @method members store | Insert & Update
     * *****************************************************************************************************************
     */
    public function store()
    {
        $id = request()->input($this->id_key);

        /** -------- Validation */
        $validator_rules = [
            'name' => "required",
            'logo' => "image|mimes:gif,jpg,jpeg,png|max:2048",

            'joining_date' => "required",
        ];
        $validator = Validator::make(request()->all(), $validator_rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $data = DB_FormFields($this->model);

        /** -------- Upload Files */
        $files = upload_files(['logo'], "assets/front/{$this->table}/");
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

        if ($status = $row->save()) {
            if ($id == 0) {
                $id = $row->{$this->id_key};
                set_notification('Record has been inserted!', 'success');
                activity_log('Add', $this->table, $id);
            } else {
                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id);
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
     * @method members delete
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

        $unlink = ['logo' => 'assets/front/{$this->table}',
        ];
        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ")", true, $unlink);
        //$this->model->whereIn($this->id_key, $ids)->delete();

        activity_log(getUri(3), $this->table, $ids);

        return \Redirect::to(admin_url('index', true))->with('success', 'Record has been deleted!');
    }


    /**
     * *****************************************************************************************************************
     * @method members view | Record
     * *****************************************************************************************************************
     */
    public function view()
    {
        $id = getUri(4);
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

        $config['buttons'] = ['new', 'edit', 'delete', 'refresh', 'print', 'back'];
        $config['hidden_fields'] = ['created_by'];
        $config['image_fields'] = [
            'logo' => ['path' => asset_url("front/{$this->table}/"), 'size' => '128x128'],
        ];
        $config['attributes'] = [
            'id' => ['title' => 'ID'],
            'phones' => ['wrap' => function ($value, $field, $row) {
                return collect(json_decode($value, true))->map(function ($val) {
                    return '<span class=""><i class="la la-phone la-2x"></i> <a href="tel:' . $val . '">' . $val . '</a></span>';
                })->join(', ');
            }],
            'emails' => ['wrap' => function ($value, $field, $row) {
                return collect(json_decode($value, true))->map(function ($val) {
                    return '<span class=""><i class="la la-at la-2x"></i> <a href="mailto:' . $val . '">' . $val . '</a></span>';
                })->join(', ');
            }],
            'association_id' => ['title' => 'Association', 'wrap' => function ($value, $field, $row) {
                return \App\Association::find($value)->name;
            }],
            'data' => ['title' => 'Stats', 'wrap' => function ($value, $field, $row) {
                $data = json_decode($value);
                if (count($data) > 0) {
                    $HTML = '<table class="table"><tr>';
                    foreach ($data as $f => $val) {
                        $HTML .= '<th>' . ucwords(str_replace(['_'], ' ', $f)) . '</th>';
                    }
                    $HTML .= '</tr><tr>';
                    foreach ($data as $f => $val) {
                        $HTML .= '<td>' . $val . '</td>';
                    }
                    $HTML .= '</tr></table>';
                }
                return $HTML;
            }],
        ];

        activity_log(getUri(3), $this->table, $id);

        if (request()->ajax()) {
            return $row;
        } else if (view()->exists('admin.members.view')) {
            return view('admin.members.view', compact('row', 'config'), ['_info' => $this->_info]);
        } else {
            return view('admin.layouts.view_record', compact('row', 'config'), ['_info' => $this->_info]);
        }
    }

    /**
     * *****************************************************************************************************************
     * @method members AJAX actions
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
            case 'sync_directory':
                $member = \App\Member::find($id);

                \App\Directory::fetchDirectory($member->association_id, $member->id);
                $JSON['msg'] = $member->company . ' sync successfully!';
                break;
            case 'directories':
                $member = \App\Member::find($id);

                $JSON['member'] = $member;
                $rows = \App\Directory::where('member_id', $id)->select(\DB::Raw("directories.id
, directories.name
, directories.designation
, directories.department
, JSON_UNQUOTE(JSON_EXTRACT(directories.emails, '$[0]')) as email
, JSON_UNQUOTE(JSON_EXTRACT(directories.phones, '$[0]')) as phone
, directories.data->>'$.from' as `from`
"))->get();

                $grid = new Grid();
                $grid->is_front = true;


                $grid->init(['data' => $rows]);
                $grid->dt_column(['id' => ['hide' => true]]);

                $JSON['html'] = "<table class='table table-bordered table-info'>
<tr>
<th>Phone</th>
<th>Email</th>
</tr>
<tr>
<td>".join(', ', json_decode($member->phones))."</td>
<td>".join(', ', json_decode($member->emails))."</td>
</tr>
</table>";
                $JSON['html'] .= '<h4>Directory</h4>';
                $JSON['html'] .= $grid->showGrid();

                break;
        }

        return $JSON;
    }


    /**
     * *****************************************************************************************************************
     * @method members import
     * *****************************************************************************************************************
     */

    public function duplicate()
    {
        $id = getUri(4);
        $OBJ = $this->model->find($id);
        $unique = [
        ];
        $newOBJ = $OBJ->replicate($unique);
        $newOBJ->logo = '';
        $newOBJ->save();
        $newID = $newOBJ->id;

        return \Redirect::to(admin_url("form/{$newID}", true))->with('success', 'Record has been duplicated!');
    }

    /**
     * *****************************************************************************************************************
     * @method members import
     * *****************************************************************************************************************
     */
    public function import()
    {
        if (\request()->isMethod('POST')) {
            $import_CLS = \Str::studly(\Str::singular($this->module)) . "Import";

            Excel::import(new \App\Imports\MemberImport(), request()->file('file'));
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
     * @method members export
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
        $files = upload_files(['logo'], $dir, ['ext' => gif, jpg, jpeg, png]);
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
