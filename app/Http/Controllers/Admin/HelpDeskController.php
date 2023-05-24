<?php

/**
 * @property App\Client $model
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
use Mail;

class HelpDeskController extends Controller
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
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'group' => 'help_desks.' . $this->id_key])->merge(request()->query())->toArray();

        $login_code = Auth::user()->userclient['login_code'];
        $user_type = Auth::user()->user_type_id;
        $all_data_access = Auth::user()->is_allowed_all_data_access;
        if ($all_data_access != 1) {
            $this->where .= " AND {$this->table}.tenant_name='{$login_code}'";
        }
        //$SQL = "";
        $select = "help_desks.id
        , help_desks.ticket_creator
        , help_desks.ticket_number
        , help_desks.ticket_title
        , DATE_FORMAT(help_desks.resolution_date, '%m-%d-%Y') AS resolution_date
        , DATE_FORMAT(help_desks.created_at, '%m-%d-%Y') AS Created_At";
        $where = $this->where;
        $where .= getWhereClause($select);

        $SQL = $this->model->select(\DB::raw($select));

        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

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
                    'ticket_creator' => Crypto::decryptData($item['ticket_creator'], Crypto::getAwsEncryptionKey()),
                    'ticket_number' => $item['ticket_number'],
                    'ticket_title' => $item['ticket_title'],
                    'resolution_date' => $item['resolution_date'] ?? "No Date Specified",
                    'Created_At' => $item['Created_At'],
                ];
            });

        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.help_desk.grid', compact('paginate_OBJ', 'query'), ['_info' => $this->_info]);
        }
    }

    public function getUserInfoByEmailId(int $email_id)
    {
        $user_info = DB::table('users')
            ->select('users.first_name', 'users.last_name', 'tenants.login_code')
            ->join('tenants', 'users.tenants_id', '=', 'tenants.id')
            ->where('users.id', '=', $email_id)
            ->get();

        foreach ($user_info as $key => $value) {
            $user_info[$key]->first_name =
                Crypto::decryptData($value->first_name, Crypto::getAwsEncryptionKey());
            $user_info[$key]->last_name =
                Crypto::decryptData($value->last_name, Crypto::getAwsEncryptionKey());
        }
        return response()->json($user_info);
    }

    public function form()
    {
        $id = getUri(4);
        if ($id > 0) {
            $row = $this->model->find($id);
            $ticket_owner = DB::table('help_desks')
                ->select('users.email AS ticket_owner')
                ->join('users', 'help_desks.ticket_owner', '=', 'users.id')
                ->where('help_desks.id', '=', $id)
                ->get();

            $ticket_files =
                DB::table('helpdesk_files')
                ->where('helpdesk_files.ticket_id', '=', $id)
                ->where('helpdesk_files.submitted_from', '=', 'ticket_form')
                ->get();
            $ticket_comment_files =
                DB::table('helpdesk_files')
                ->where('helpdesk_files.ticket_id', '=', $id)
                ->where('helpdesk_files.submitted_from', '=', 'comment_form')
                ->get();
            $ticket_source = DB::table('help_desks')
                ->select('helpdesk_sources.source')
                ->join('helpdesk_sources', 'help_desks.ticket_source', '=', 'helpdesk_sources.id')
                ->where('help_desks.id', '=', $id)
                ->get();
            $ticket_status = DB::table('help_desks')
                ->select('helpdesk_ticket_statuses.status', 'helpdesk_ticket_statuses.hierarchy')
                ->join('helpdesk_ticket_statuses', 'help_desks.ticket_status', '=', 'helpdesk_ticket_statuses.id')
                ->where('help_desks.id', '=', $id)
                ->get();
            $ticket_audit_logs = DB::table('help_desks')
                ->select('helpdesk_activity_log.*')
                ->join('helpdesk_activity_log', 'help_desks.id', '=', 'helpdesk_activity_log.ticket_id')
                ->where('help_desks.id', '=', $id)
                ->get();
            $ticket_comments = DB::table('help_desks')
                ->select('helpdesk_comments.*')
                ->join('helpdesk_comments', 'help_desks.id', '=', 'helpdesk_comments.ticket_id')
                ->where('help_desks.id', '=', $id)
                ->get();

            // dd($ticket_files);
            if ($row->id <= 0) {
                \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
            }
        }
        // dd($ticket_status[0]->hierarchy);
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);
        Breadcrumb::add_item(($id > 0) ? "Edit -> id:[$id]" : 'Add New');


        // dd($ticket_files);
        return view('admin.help_desk.form', compact('row', 'ticket_owner', 'ticket_files', 'ticket_source', 'ticket_status', 'ticket_audit_logs', 'ticket_comments', 'ticket_comment_files'));
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
            'ticket_source' => "required",
            'ticket_owner' => "required",
            'ticket_title' => "required",
            'ticket_description' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = DB_FormFields($this->model);
        $data['data']['ticket_source'] = request()->input('ticket_source');
        $data['data']['ticket_owner'] = request()->input('ticket_owner');
        $data['data']['tenant_name'] = request()->input('tenant_name');
        $data['data']['ticket_number'] = request()->input('ticket_number');
        $data['data']['ticket_title'] = request()->input('ticket_title');
        $data['data']['ticket_description'] = request()->input('ticket_description');
        // $data['data']['ticket_number'] = request()->input('');
        $data['data']['created_by'] = Auth::user()->email;
        $data['data']['ticket_creator'] = Auth::user()->email;
        $data['data']['resolution_date'] = request()->input('resolution_date');
        // dd($data);
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
            $ticket_owner_user = \App\User::find($row->ticket_owner);
            $ticket_owner_email = Crypto::decryptData($ticket_owner_user->email, Crypto::getAwsEncryptionKey());

            if ($id == 0) {
                $id = $row->{$this->id_key};
                if (request()->hasfile('ticket_upload')) {
                    foreach (request()->file('ticket_upload') as $col => $file) {
                        $name = time() . '.' . $file->extension();
                        $name = $file->getClientOriginalName();
                        $file->move('assets/admin/media/helpdesk', $name);
                        $uploaded_file[$col] = $name;
                        DB::table('helpdesk_files')->insert([
                            [
                                'ticket_id' => $id,
                                'ticket_number' => request()->input('ticket_number'),
                                'submitted_from' => 'ticket_form',
                                'filename' => $name,
                                'created_by' => Auth::user()->email,
                            ]
                        ]);
                    }
                }

                set_notification('Record has been inserted!', 'primary');
                activity_log('Add', $this->table, $id, 0, null, $data['data']);
                helpdesk_activity_log('Create', 'Created Ticket', $id, request()->input('ticket_number'), Auth::user()->email);

                // shot email
                sendTicketEmail($ticket_owner_email, 'Created', $row->toArray());

                $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
                return redirect($__redirect)->with('primary', 'Record has been Inserted!');
            } else {
                if (request()->hasfile('ticket_upload')) {
                    DB::table('helpdesk_files')->where('ticket_id', '=', $id)->delete();
                    foreach (request()->file('ticket_upload') as $col => $file) {
                        $name = time() . '.' . $file->extension();
                        $name = $file->getClientOriginalName();
                        $file->move('assets/admin/media/helpdesk', $name);
                        $uploaded_file[$col] = $name;
                        DB::table('helpdesk_files')->insert([
                            [
                                'ticket_id' => $id,
                                'ticket_number' => request()->input('ticket_number'),
                                'submitted_from' => 'ticket_form',
                                'filename' => $name,
                                'created_by' => Auth::user()->email,
                            ]
                        ]);
                    }
                }
                set_notification('Record has been updated!', 'primary');
                activity_log('Update', $this->table, $id, 0, null, $data['data']);
                helpdesk_activity_log('Update', 'Updated Ticket', $id, request()->input('ticket_number'), Auth::user()->email);

                // shot email
                sendTicketEmail($ticket_owner_email, 'Updated', $row->toArray());

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

    public function assign()
    {
        // dd(request()->all());
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'ticket_priority' => "required",
            'ticket_category' => "required",
            'ticket_status' => "required",
            'ticket_assignee' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($id > 0) {
            $reqData = [
                'ticket_priority' => request()->input('ticket_priority'),
                'ticket_category' => request()->input('ticket_category'),
                'ticket_application' => request()->input('ticket_application'),
                'ticket_status' => request()->input('ticket_status'),
                'ticket_assignee' => request()->input('ticket_assignee'),
            ];
            $affected = DB::table('help_desks')
                ->where('id', $id)
                ->update($reqData);
            $row = DB::table('help_desks')
                ->find($id);

            $row = collect($row)->toArray();
            // dd($row);

            $ticket_owner_user = \App\User::find($row['ticket_owner']);
            $ticket_owner_email = Crypto::decryptData($ticket_owner_user->email, Crypto::getAwsEncryptionKey());

            // dd($ticket_owner_email);

            // shot email
            sendTicketEmail($ticket_owner_email, 'Assigned', $row);

            $ticket_assignee_mail = DB::table('helpdesk_ticket_assignees')
                ->select('helpdesk_ticket_assignees.assignee')
                ->where('helpdesk_ticket_assignees.id', '=', request()->input('ticket_assignee'))
                ->get();
            $ticket_status = DB::table('helpdesk_ticket_statuses')
                ->select('helpdesk_ticket_statuses.status')
                ->where('helpdesk_ticket_statuses.id', '=', request()->input('ticket_status'))
                ->get();

            set_notification('Ticket has been Assigned!', 'primary');
            activity_log('Update', $this->table, $id, 0, null, $reqData);
            helpdesk_activity_log('Assign and Change Status', 'Assigned Ticket to ' . $ticket_assignee_mail[0]->assignee . ' and Changed Status of Ticket to ' . $ticket_status[0]->status, $id, request()->input('ticket_number'), Auth::user()->email);
            $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
            return redirect($__redirect)->with('success', 'Ticket has been Assigned!');
        } else {
            \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
        }
    }

    public function comment()
    {
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'ticket_comment' => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($id > 0) {
            $commentData = [
                'ticket_id' => $id,
                'ticket_number' => request()->input('ticket_number'),
                'ticket_comment' => request()->input('ticket_comment'),
            ];
            $affected = DB::table('helpdesk_comments')->insertGetId($commentData);

            if (request()->hasfile('ticket_comments_upload')) {
                foreach (request()->file('ticket_comments_upload') as $col => $file) {
                    $name = time() . '.' . $file->extension();
                    $name = $file->getClientOriginalName();
                    $file->move('assets/admin/media/helpdesk', $name);
                    $uploaded_file[$col] = $name;
                    DB::table('helpdesk_files')->insert([
                        [
                            'ticket_id' => $id,
                            'comment_activity_id' => $affected,
                            'ticket_number' => request()->input('ticket_number'),
                            'submitted_from' => 'comment_form',
                            'filename' => $name,
                            'created_by' => Auth::user()->email,
                        ]
                    ]);
                }
            }


            $row = DB::table('help_desks')->find($id);

            $row = collect($row)->toArray();
            // dd($row);

            $ticket_owner_user = \App\User::find($row['ticket_owner']);
            $ticket_owner_email = Crypto::decryptData($ticket_owner_user->email, Crypto::getAwsEncryptionKey());

            // dd($ticket_owner_email);

            // shot email
            sendTicketEmail($ticket_owner_email, 'Commented', $row);

            set_notification('Ticket has been Assigned!', 'primary');
            activity_log('Add', 'helpdesk_comments', $affected, 0, null, $commentData);
            helpdesk_activity_log('Comment', Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey()) . ' Commented ' . request()->input('ticket_comment'), $id, request()->input('ticket_number'), Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey()));
            $__redirect = (!empty(getVar('__redirect')) ? getVar('__redirect') : admin_url("form/{$id}", true));
            return redirect($__redirect)->with('success', 'Commented Successfully!');
        } else {
            \Redirect::to(admin_url('', true))->with('error', 'Access forbidden!');
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

        activity_log('Status', $this->table, $ids, 0, null, $data);

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
