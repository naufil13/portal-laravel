<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Breadcrumb;

use Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SettingController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // dd(config('session.lifetime'));
        /** -------- Breadcrumb */

        Breadcrumb::add_item($this->_info->title, $this->_route);

        /** -------- Response */
        return view('admin.settings.form', compact('row'), ['_info' => $this->_info]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $OPT = getVar('opt');
        // dd($OPT);
        if(request()->hasfile('opt')) {
            foreach(request()->file('opt') as $col => $file) {
                $name = time().'.'.$file->extension();
                $name = $file->getClientOriginalName();
                $file->move(asset_path('images', 1), $name);
                $OPT[$col] = $name;
            }
        }

        foreach ($OPT as $name => $value) {
            $SQL = \DB::table($this->table);
            if(opt($name) && !empty($value)){
                $SQL->where('name', $name)->update(['value' => $value]);
            } else if (!empty($value)) {
                $SQL->insert(['name' => $name, 'value' => $value]);
            }
        }
        Cache::forget('DB_options');
        \Artisan::call('config:cache');
        return redirect()->back()->with('success', 'Record has been updated!');
    }
}
