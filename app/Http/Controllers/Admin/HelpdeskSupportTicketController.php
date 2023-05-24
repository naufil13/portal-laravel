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
use Token;
use Str;
use ZanySoft\Zip\Zip;
use Crypto;

class HelpdeskSupportTicketController extends Controller
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
        $config = collect(['sort' => $this->id_key, 'dir' => 'desc', 'limit' => 25, 'group' => 'helpdesk_support_tickets.' . $this->id_key])->merge(request()->query())->toArray();

        //$SQL = "";
        $select = "helpdesk_support_tickets.id
        , helpdesk_support_tickets.ticket_no
        , users.email as owner
        , clients.client_name as company
        , helpdesk_support_tickets.created
        , helpdesk_support_tickets.updated
        , helpdesk_support_status.status
        , helpdesk_support_assignees.assignee as assignto";

        $where = $this->where;
        $where .= getWhereClause($select);

        $SQL = $this->model->select(\DB::raw($select));

        if (!empty($where)) {
            $SQL = $SQL->whereRaw($where);
        }

        // SQL Joins
        $SQL = $SQL->join('users', 'helpdesk_support_tickets.owner_username', '=', 'users.id');
        $SQL = $SQL->join('clients', 'helpdesk_support_tickets.company_name', '=', 'clients.id');
        $SQL = $SQL->join('helpdesk_support_status', 'helpdesk_support_tickets.status', '=', 'helpdesk_support_status.id');
        $SQL = $SQL->leftjoin('helpdesk_support_assignees', 'helpdesk_support_tickets.assign_to', '=', 'helpdesk_support_assignees.id');

        $SQL = $SQL->orderBy($config['sort'], $config['dir'])->groupBy($config['group']);
        $paginate_OBJ = $SQL->paginate($config['limit']);

        $query = $SQL->toSql();
        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | Breadcrumb
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        Breadcrumb::add_item($this->_info->title, $this->_route);

         // Encryption
         $paginate_OBJ
         ->getCollection()
         ->transform(function($item, $key){
             return [
                 'id' => $item['id'],
                 'ticket_no' => $item['ticket_no'],
                 'owner' => $item['owner'],
                 'company' => Crypto::decryptData($item['company'], Crypto::getAwsEncryptionKey()),
                 'assignto' => $item['assignto'] == null ? "Unassigned": $item['assignto'],
                 'created' => $item['created'],
                 'updated' => $item['updated'],
                 'status' => $item['status'],
             ];

         });

        if (request()->ajax()) {
            return $paginate_OBJ;
        } else {
            return view('admin.supports.grid', compact('paginate_OBJ', 'query'), ['_info' => $this->_info]);
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


        //creator Data
        $login_user = Auth::user()->email;
        //creator Data

        //Ticket Sources Data Starts Here
        $source_sql = DB::table('helpdesk_support_tickets')
        ->select('helpdesk_support_source.src_name')    
        ->join('helpdesk_support_source','helpdesk_support_tickets.ticket_source_id','=','helpdesk_support_source.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $sources_edit=$source_sql[0]->src_name;
        //Ticket Sources Data Ends Here

        //Owners Data Starts Here
        $owner_sql = DB::table('helpdesk_support_tickets')
        ->select('users.email')
        ->join('users','helpdesk_support_tickets.owner_username','=','users.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $owners_edit=$owner_sql[0]->email;
        //Owners Data Ends Here

        //Companies Data Starts Here
        $companies = DB::table('clients')
        ->select('id','client_name')
        ->get();
        $company_sql = DB::table('helpdesk_support_tickets')
        ->select('clients.client_name')
        ->join('clients','helpdesk_support_tickets.company_name','=','clients.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $companies_edit=$company_sql[0]->client_name;
        //Companies Data Ends Here

        //Division Data Starts Here
        $division_sql = DB::table('helpdesk_support_tickets')
        ->select('divisions.division_name')
        ->join('divisions','helpdesk_support_tickets.division_name','=','divisions.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $divisions_edit=$division_sql[0]->division_name;
        //Division Data Ends Here

        //Department Data Starts Here
        $department_sql = DB::table('helpdesk_support_tickets')
        ->select('departments.department_name')
        ->join('departments','helpdesk_support_tickets.department_name','=','departments.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $departments_edit=$department_sql[0]->department_name;
        //Department Data Ends Here

        //Clinical Study Data Starts Here
        $studies_sql = DB::table('helpdesk_support_tickets')
        ->select('trials.study_name')
        ->join('trials','helpdesk_support_tickets.clinical_study','=','trials.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $studies_edit=$studies_sql[0]->study_name;
        //Clinical Study Data Ends Here

        //Status Data Starts Here
        // $statuses = DB::table('helpdesk_support_status')
        // ->select('id','status')
        // ->where('id','>',$row->status)
        // ->limit(1)
        // ->get();

        $statuses_sql = DB::table('helpdesk_support_tickets')
        ->select('helpdesk_support_status.status')
        ->join('helpdesk_support_status','helpdesk_support_tickets.status','=','helpdesk_support_status.id')
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $statuses_edit=$statuses_sql[0]->status;
        //Status Data Ends Here

        //Assign To Starts Here
        $assignto_sql = DB::table('helpdesk_support_tickets')
        ->select('helpdesk_support_assignees.assignee')
        ->join("helpdesk_support_assignees","helpdesk_support_tickets.assign_to","=","helpdesk_support_assignees.id")
        ->where('helpdesk_support_tickets.id',$id)
        ->get();
        $assignto=$assignto_sql[0]->assignee;

        if($assignto == null){
        $assignto = 'Unassigned';
        }
        //Assign To Ends Here

        //Files Data Starts Here
        $files = DB::table('helpdesk_files')
        ->select('filename','created_by','created','submitted_from') 
        ->where('ticket_id',$id)
        ->get();
        //Files Data Ends Here

        //Activity Logs Data Starts Here
        $activity_logs =  DB::table('helpdesk_portal_log')
        ->select('*') 
        ->where('ticket_id',$id)
        ->get();
        $complete_logs = $this->createlog($activity_logs);

        //Activity Logs Data Ends Here      

        //Assign Comments and there Filename Data Starts Here
        $assign_records = DB::table('helpdesk_support_assign_activity')
            ->select('helpdesk_support_assign_activity.assign_comments','helpdesk_files.filename')
            ->leftjoin('helpdesk_files','helpdesk_support_assign_activity.id','helpdesk_files.assign_activity_id')
            ->where('helpdesk_support_assign_activity.created_by', $login_user)
            ->get();

        $assign_comments = [];
        foreach($assign_records as $assign_record)
        {
        $assign_comments[$assign_record->assign_comments][] = $assign_record;

        }     
        //Assign Comments and there Filename Data Ends Here

        //Assign Comments and there Filename Data Starts Here
        $comment_records = DB::table('helpdesk_support_comment_activity')
            ->select('helpdesk_support_comment_activity.comments_detail','helpdesk_files.filename')
            ->leftjoin('helpdesk_files','helpdesk_support_comment_activity.id','helpdesk_files.comment_activity_id')
            ->where('helpdesk_support_comment_activity.created_by',$login_user)
            ->get();

        $comment_comments = [];
        foreach($comment_records as $comment_record)
        {
        $comment_comments[$comment_record->comments_detail][] = $comment_record;
        }
        //Assign Comments and there Filename Data Ends Here

        // //Generate New Token Starts Here
        $ticket = Token::generateTicketNo();
        //Generate New Token Ends Here


        return view('admin.supports.form', compact(['row','sources_edit','owners_edit','companies','companies_edit','divisions_edit','departments_edit','studies_edit','description','assignto','statuses','statuses_edit','files','complete_logs','comment_comments','assign_record','studies','ticket','assignees','statuses'
        ,'updated','created','status','comment_detail','assign_comments','login_user']));    
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */

    // Ticket Tab Form Data Store Starts Here
    public function store()
    {
        $id = request()->input($this->id_key);

        $validator = Validator::make(request()->all(), [
            'ticket_source_id' => "required",
            'owner_username' => "required",
            'creator_username' => "required",
            'first_name' => "required",
            'last_name' => "required",
            'company_name' => "required",
            'division_name' => "required",
            'department_name' => "required",
            'clinical_study' => "required",
            'priorities_id' => "required",
            'general_issues_id' => "required",
            'subject' => "required",
            'description' => "required",
    
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
 
        $data = DB_FormFields($this->model);

        $login_user = Auth::user()->email;
        $login_code = Auth::user()->userclient['login_code'];

        $data['data']['login_code'] = $login_code;
        $data['data']['created_by'] = $login_user;
        $data['data']['updated_by'] = $login_user;


        /**‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒
         * | File Upload
         *‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒‒*/
        if ($id > 0) {
            $row = $this->model->find($id);
            $row = $row->fill($data['data']);
        } else {
            $row = $this->model->fill($data['data'],$login_code);
        }

        if ($row->save()) {

            if ($id == 0) {
                $id = $row->{$this->id_key};

                if(request()->hasfile('support_file'))
                {
                    foreach(request()->support_file as $file)
                    {
                        $filename = $file->getClientOriginalName();
                        // $files = upload_files(['support_file'], "assets/admin/media/helpdesk_support");

                        $insert = DB::table('helpdesk_files')
                                    ->insert(['ticket_id' => $id,'ticket_no'=> request()->ticket_no,'submitted_from'=>'ticket_form','filename'=>"dummy_file",'created_by' => $login_user]);
                    }
                }
                
                $insert = DB::table('helpdesk_portal_log')
                            ->insert(['user_login_code' => $login_code,'user_email'=> $login_user,'activity_description'=>'Created Ticket'.','.request()->ticket_no,'ticket_id'=>$id]);
                
                set_notification('Record has been inserted!', 'success');
                activity_log('Add', $this->table, $id);
            } else {

                if(request()->hasfile('support_file'))
                {
                    foreach(request()->support_file as $file)
                    {
                        $filename = $file->getClientOriginalName();
                        // $files = upload_files(['support_file'], "assets/admin/media/helpdesk_support");

                        $insert = DB::table('helpdesk_files')
                                    ->insert(['ticket_id' => $id,'ticket_no'=> request()->ticket_no,'submitted_from'=>'ticket_form','filename'=>"dummy_file",'created_by' => $login_user]);
                    }
                }

                $insert = DB::table('helpdesk_portal_log')
                            ->insert(['user_login_code' => $login_code,'user_email'=> $login_user,'activity_description'=>'Updated Ticket'.','.request()->ticket_no,'ticket_id'=>$id]);
                
                set_notification('Record has been updated!', 'success');
                activity_log('Update', $this->table, $id);
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
    // Ticket Tab Form Data Store Ends Here


    // Assign Tab Form Data Store Starts Here
    public function store_assignee(){
        
        $id = request()->input($this->id_key);        

        $validator = Validator::make(request()->all(), [
            'ticket_id' => $id,
            'ticket_no' => "required",
            'assign_comments' => "required",
            'assignee' => "required",
            'status_id' => "required",
            "assign_filename" => "",
        ]);

        $ticketno = DB::table('helpdesk_support_tickets')->where('id', $id)->value('ticket_no');
        $comments = request()->assign_comments;
        $assignee = request()->assignee;
        $status = request()->status_id;
        $login_user = Auth::user()->email;
        $login_code = Auth::user()->userclient['login_code'];

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $currId = DB::table('helpdesk_support_assign_activity')
        ->insertGetId(['ticket_id' => $id, 'assign_comments'=> $comments, 'ticket_no'=> $ticketno,'created_by' => $login_user]);


        if(request()->hasfile('assign_filename'))
        {
            $insert = DB::table('helpdesk_portal_log')
            ->insert(['user_login_code' => $login_code,'user_email'=> $login_user,'activity_description'=>'Created Assign Comments / Upload Activity'.','.$ticketno,'ticket_id'=>$id]);
            
            foreach(request()->assign_filename as $file)
            {
                $filename = $file->getClientOriginalName();
                // $new_file = upload_files([$file], "assets/admin/media/helpdesk_support");
                
                $insert = DB::table('helpdesk_files')
                        ->insert(['ticket_id' => $id,'ticket_no'=> request()->ticket_no,'submitted_from'=>'assign_form','filename'=>"dummy_file",'created_by' => $login_user,'assign_activity_id' => $currId]);
                        
            }
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
    // Assign Tab Form Data Store Ends Here


    // Comment Tab Form Data Store Starts Here
    public function store_comment(){
        
        $id = request()->input($this->id_key);        

        $validator = Validator::make(request()->all(), [
            'ticket_id' => $id,
            'ticket_no' => "required",
            'comments' => "required",
        ]);

        $ticketno = DB::table('helpdesk_support_tickets')->where('id', $id)->value('ticket_no');
        $comments = request()->comments;
        $login_user = Auth::user()->email;
        $login_code = Auth::user()->userclient['login_code'];

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $currId = DB::table('helpdesk_support_comment_activity')
              ->insertGetId(['ticket_id' => $id, 'comments_detail'=> $comments, 'ticket_no'=> $ticketno , 'comments_filename' => $filename,'created_by' =>$login_user]);
        
        
        if(request()->hasfile('comments_filename'))
        {

            $insert = DB::table('helpdesk_portal_log')
              ->insert(['user_login_code' => $login_code,'user_email'=> $login_user,'activity_description'=>'Created Comment Activity '.','.$ticketno,'ticket_id'=>$id]);

            foreach(request()->comments_filename as $file)
            {
                $filename = $file->getClientOriginalName();
                //Storage::putFile('photos', $file, 'public');
                
                $insert = DB::table('helpdesk_files')
                            ->insert(['ticket_id' => $id,'ticket_no' => request()->ticket_no,'submitted_from' => 'comment_form','filename' => "dummy_file",'comment_activity_id' => $currId,'created_by' => $login_user]);
            }
        }
        $data = DB_FormFields($this->model);
        

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
    // Comment Tab Form Data Store Ends Here

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

        // $unlink = ['icon' => asset_dir('media/icons/', true), 'image' => asset_dir('media/icons/', true)];
        $affectedRows = delete_rows($this->table, "{$this->id_key} IN(" . implode($ids, ',') . ")", true, $unlink);

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

    //AJAX CALLS FOR FORM STARTS HERE
    public function getFirstNameByUserName(int $owner_id)
    {
        $FirstName = DB::table('users')
            ->select('id', 'first_name')
            ->where('id',$owner_id)
            ->get();
        
        return response()->json($FirstName); 
    }

    public function getLastNameByUserName(int $owner_id)
    {
        $LastName = DB::table('users')
            ->select('id', 'last_name')
            ->where('id',$owner_id)
            ->get();
        
        return response()->json($LastName); 
    }
    public function updatestatus()
    {
        $login_user = Auth::user()->email;
        $login_code = Auth::user()->userclient['login_code'];

        $id = request()->query('id');
        $status = request()->query('status');
        $ticket = request()->query('ticket');

        $update = DB::table('helpdesk_support_tickets')
                    ->where('id', $id)
                    ->update(['status' => $status]);
        $insert = DB::table('helpdesk_portal_log')
                    ->insert(['user_login_code' => $login_code,'user_email'=> $login_user,'activity_description'=>'Changed Ticket Status'.','.$status.','.$ticket,'ticket_id'=>$id]);
        
        return response()->json("done");   
    }  
    public function updateassignee()
    {
        $login_user = Auth::user()->email;
        $login_code = Auth::user()->userclient['login_code'];
        
        $id = request()->query('id');
        $assignee = request()->query('assignee');
        $ticket = request()->query('ticket');

        $update = DB::table('helpdesk_support_tickets')
                    ->where('id', $id)
                    ->update(['assign_to' => $assignee]);
        $insert = DB::table('helpdesk_portal_log')
                    ->insert(['user_login_code' => $login_code,'user_email'=> $login_user,'activity_description'=>'Assigned Ticket'.','.$assignee.','.$ticket,'ticket_id'=>$id]);

        return response()->json("done"); 
        
    }   
    public function getsource(string $ticket)
    {
        return response()->json($ticket); 
    }
    //AJAX CALLS FOR FORM ENDS HERE

    public function createlog($activity_logs){

        foreach ($activity_logs as $logs) {
            if (str_contains($logs->activity_description, 'Created Ticket')) 
            {
                $values = explode(',', $logs->activity_description);
                $day = date('l', strtotime($logs->created));
                $string = $logs->user_email.' '.$values[0].' having ticket No# '.$values[1].' on '.$day.' '.$logs->created;
                $complete_logs[]  = $string;

            } elseif (str_contains($logs->activity_description, 'Updated Ticket')) 
            {
                $values = explode(',', $logs->activity_description);
                $day = date('l', strtotime($logs->created));
                $string = $logs->user_email.' '.$values[0].' having ticket No# '.$values[1].' on '.$day.' '.$logs->created;
                $complete_logs[]  = $string;
            } 
            elseif (str_contains($logs->activity_description, 'Changed Ticket Status')) 
            {
                $values = explode(',', $logs->activity_description);
                $day = date('l', strtotime($logs->created));
                $status = DB::table('helpdesk_support_status')
                ->select('status')
                ->where('id', $values[1])
                ->get();
                $status = $status[0]->status;

                $string = $logs->user_email.' '.$values[0].' to '.$status.' having ticket No# '.$values[2].' '.$day.' '.$logs->created;
                $complete_logs[]  = $string;
            } 
            elseif (str_contains($logs->activity_description, 'Assigned Ticket')) 
            {
                $values = explode(',', $logs->activity_description);
                $day = date('l', strtotime($logs->created));
                $assignee = DB::table('helpdesk_support_assignees')
                ->select('assignee')
                ->where('id', $values[1])
                ->get();
                $assignee = $assignee[0]->assignee;
                $string = $logs->user_email.' '.$values[0].' to '.$assignee.' having ticket No# '.$values[2].' '.$day.' '.$logs->created;
                $complete_logs[]  = $string;
            } 
            elseif (str_contains($logs->activity_description, 'Assign Comment')) 
            {
                $values = explode(',', $logs->activity_description);
                $string = $logs->user_email.' '.$values[0].' having ticket No# '.$values[1].' '.$day.' '.$logs->created;
                $complete_logs[]  = $string;
            } 
            elseif (str_contains($logs->activity_description, 'Created Comment')) 
            {
                $values = explode(',', $logs->activity_description);
                $string = $logs->user_email.' '.$values[0].' having ticket No# '.$values[1].' '.$day.' '.$logs->created;
                $complete_logs[]  = $string;
            }
        }
        return $complete_logs;
    }
}
