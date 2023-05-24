<?php

use App\EmailTemplate;

function pre($array)
{
    echo ("<pre>");
    print_r($array);
    echo ("</pre>");
}

function generate_url($params, $append = true, $url = '')
{
    if (is_string($params)) {
        $params = [$params];
    }
    if (empty($url)) {
        $url = request()->url();
        $query_str = request()->getQueryString();
        if (!empty($query_str)) {
            $q_params = [];
            foreach (explode('&', $query_str) as $item) {
                $item = explode('=', $item);
                if (!in_array($item[0], $params)) {
                    $q_params[$item[0]] = $item[1];
                } else {
                    unset($q_params[$item[0]]);
                }
            }
            if ($append) {
                $params = array_merge($q_params, $params);
            } else {
                $params = $q_params;
            }
            $query_str = http_build_query($params);
            if (count($q_params) > 0) {
                $url .= "?" . $query_str;
            }
        } else {
            $url .= "?do=1";
        }
    } else {
        $url .= "?do=1";
    }
    return $url;
}

/**
 * @param string $path
 * @param string $from
 * @return string
 */
function media_url($path = '', $from = 'front')
{
    $from = ($from === 'front' ? $from : 'admin');
    return asset($from . '/' . $path);
}

/**
 * @param string $path
 * @param string $from
 * @return string
 */
function asset_path($path = '', $from = 'front')
{
    $from = ($from === 'front' ? $from : 'admin');
    return public_path('assets/' . $from . '/' . $path);
}

function asset_dir($path = '', $from = 'front')
{
    return asset_path($path, $from);
}

/**
 * @param string $path
 * @param string $from
 * @return string
 */
function asset_url($path = '', $from = 'front')
{
    $from = ($from === 'front' ? $from : 'admin');
    return asset('assets/' . $from . '/' . $path);
}

/**
 * @param string $path
 * @param bool $module
 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
 */
function admin_url($path = '', $module = false)
{
    $path = ($module ? request()->segment(2) . '/' . $path : $path);
    return url(config('app.admin_dir') . '/' . $path);
}

function getUri($num)
{
    return request()->segment($num);
}


function getVar($name)
{
    return request()->input($name);
}

function _img($path, $width = null, $height = null, $alt_image = null, $params = [])
{
    $file_path = public_path(str_replace(url(''), '', $path));
    if (!file_exists($file_path) || is_dir($file_path)) {
        $file_path = ($alt_image ?? env('IMG_NA'));
    }

    $func = (isset($params['func']) ? $params['func'] : 'resize');
    $ext = (isset($params['ext']) ? $params['ext'] : 'png');
    $quality = (isset($params['quality']) ? $params['quality'] : 100);
    $img = _Image::open($file_path)->{$func}($width, $height)->{$ext}($quality);

    return url($img);
}


function upload_files($inputs, $path, $options = [])
{
    if (!is_array($inputs)) {
        $inputs = [$inputs];
    }
    foreach ($inputs as $input) {
        $data[$input] = false;
        if (request()->file($input)) {
            $file_input = request()->file($input);
            if ($file_input->isValid()) {
                if (is_array($file_input)) {
                    foreach ($file_input as $item) {
                        $_files[] = $file_input->move($path, $item->getClientOriginalName())->getFilename();
                    }
                    $data[$input] = json_encode($_files);
                } else {
                    $file_obj = $file_input->move($path, $file_input->getClientOriginalName());
                    //$data = $file_obj;
                    $data[$input] = $file_obj;
                    //$data[$input]->file = $file_obj->getFilename();
                }
            } else {
                $data[$input]->status = false;
                $data[$input]->error = $file_input->getErrorMessage();
            }
        }
    }
    return $data;
}

function replace_columns($content, $data = [])
{
    if (count($data) && !empty($content)) {
        foreach ($data as $key => $val) {
            $content = str_replace('{' . $key . '}', $val, $content);
        }
    }
    return $content;
}


function getModuleDetail($module = '', $where = '')
{
    if (empty($module)) {
        $module = getUri(2);
    };

    //Cache::forget($module . '_info');
    $row = \Cache::rememberForever($module . '_info', function () use ($module) {
        $_module = new \App\Module();
        $_module = $_module->selectRaw("*, IF(icon !='', icon, 'module.png') AS icon");
        $_module = $_module->where('module', $module);
        if (!empty($where)) {
            $_module = $_module->whereRaw($where);
        }
        return $_module->first();
    });

    if (strpos($row->icon, 'icon-') !== false) {
        $row->module_icon = '<i class="m-nav__link-icon ' . $row->icon . '"></i>';
    } else {
        $row->module_icon = '<img width="22" src="' . asset_url('uploads/img/icons/22_' . $row->icon) . '"/>';
    }
    return $row;
}

function get_option($name)
{
    // return \App\Setting::where('name',$name)->select('value')->first();
    \Cache::forget('DB_options'); // comment after
    $options = \Cache::rememberForever('DB_options', function () {
        $opts = \App\Setting::all();
        return Arr::pluck($opts, 'value', 'name');
    });
    return $options[$name];
}
function setEnv($name, $value)
{
    $path = base_path('.env');
    if (file_exists($path)) {
        file_put_contents($path, str_replace(
            $name . '=' . env($name), $name . '=' . $value, file_get_contents($path)
        ));
    }
}

function opt($name)
{
    return get_option($name);
}

function DB_columns($table)
{
    $columns = DB::select(DB::raw("SHOW COLUMNS FROM {$table}"));
    return $columns;
}

function DB_list_fields($row)
{
    return array_keys((array)$row);
}

function DB_found_rows()
{
    return DB::select(DB::raw('SELECT FOUND_ROWS() AS total'))[0]->total;
}

function DB_enumValues($table, $field, $assoc = true)
{
    $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'")[0]->Type;
    preg_match('/^enum\((.*)\)$/', $type, $matches);
    $enum = null;
    foreach (explode(',', $matches[1]) as $value) {
        $v = trim($value, "'");
        if ($assoc) {
            $enum = Arr::add($enum, $v, $v);
        } else {
            $enum[] = $v;
        }
    }
    return $enum;
}

/**
 * @param $data
 * @param string $selected
 * @param null $template
 * @param array $pluck_column
 * @return string
 *
 */
function selectBox($data, $selected = '', $template = null, $pluck_column = [])
{
    $template = $template ?? '<option {selected} value="{key}">{val}</option>';
    if (is_array($pluck_column) && count($pluck_column) > 0 && is_array($data)) {
        //$data = Arr::pluck($data, key($pluck_column), $pluck_column[key($pluck_column)]);
        $data = Arr::pluck($data, $pluck_column[key($pluck_column)], key($pluck_column));
    }

    $HTML = '';
    if (is_array($data)) {
        foreach ($data as $key => $OP) {
            if (is_array($OP)) {
                $HTML .= "<optgroup label='{$key}'>";
                foreach ($OP as $opt_key => $item) {
                    $_selected = ((is_array($selected) && in_array($opt_key, $selected) || (!is_array($selected) && $opt_key == $selected)) ? 'selected' : '');
                    $HTML .= replace_columns($template, ['key' => $opt_key, 'val' => $item, 'selected' => $_selected]);
                }
                $HTML .= "</optgroup>";
            } else {

                $_selected = ((is_array($selected) && in_array($key, $selected) || (!is_array($selected) && $key == $selected)) ? 'selected' : '');
                $HTML .= replace_columns($template, ['key' => $key, 'val' => $OP, 'selected' => $_selected]);
            }
        }
    } else {
        $OP = DB::select($data);
        foreach ($OP as $item) {
            $item = (is_array($item) ? $item : (array)$item);
            $_keys = array_keys($item);
            $item['key'] = $key = $item[$_keys[0]];
            $item['val'] = Crypto::decryptData($item[$_keys[1]], Crypto::getAwsEncryptionKey());
            $item['selected'] = ((is_array($selected) && in_array($key, $selected) || (!is_array($selected) && $key == $selected)) ? 'selected' : '');
            $HTML .= replace_columns($template, $item);
        }
    }
    return $HTML;
}


function delete_rows($table, $where = null, $force_delete = TRUE, $unlink_files = [])
{
    // if (count($unlink_files) > 0) {
    //     foreach ($unlink_files as $field_name => $file_path) {
    //         $SQL = DB::table($table)->select($field_name);
    //         if (is_array($where)) {
    //             $SQL = $SQL->where($where);
    //         } else if (!empty($where)) {
    //             $SQL = $SQL->whereRaw($where);
    //         }
    //         $rows = $SQL->get();
    //         foreach ($rows as $row) {
    //             @unlink($file_path . $row->{$field_name});
    //         }
    //     }
    // }

    $SQL = DB::table($table);
    if (is_array($where)) {
        $SQL = $SQL->where($where);
    } else if (!empty($where)) {
        $SQL = $SQL->whereRaw($where);
    }

    $deleteRows = $SQL->get();
    foreach ($deleteRows as $row) {
        activity_log('delete', $table, $row->id, 0, null, $row);
    }

    if ($force_delete) {
        $affectedRows = $SQL->delete();
    } else {
        $update_files = collect(array_keys($unlink_files))->flip()->map(function ($key, $val) {
            return $update_files[$key] = '';
        })->toArray();
        $affectedRows = $SQL->update($update_files);
    }

    return $affectedRows;
}

function developer_log($table = '', $description = '', $type = 'db', $user_id = 0)
{
    if (Schema::hasTable('developer_logs')) {

        if ($description instanceof Exception) {
            $msg = $description->getMessage() . "\n";
            $msg .= $description->getFile() . " : " . $description->getLine() . "\n";

            $description = $msg;
        }

        if (getUri(1) == config('app.admin_dir')) {
            $table = (!empty($table) ? $table : getUri(2));
            $user_id = ($user_id > 0) ? $user_id : Auth::id();
        } else {
            $table = (!empty($table) ? $table : getUri(1));
            $user_id = ($user_id > 0) ? $user_id : Auth::id();
        }
        $data = [
            //'datetime' => sqlDtaTime(),
            'type' => $type,
            'table' => $table,
            'user_id' => $user_id,
            'user_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),

            'current_URL' => url()->current(),
            'description' => $description,
        ];
        $OBJ = new \App\DeveloperLog();
        $OBJ->fill($data)->save();

        //DB::table('activity_logs')->insert($data);
    }
}

function helpdesk_activity_log(string $activity, string $description, int $ticket_id, string $ticket_number, string $created_by)
{
    $table = 'helpdesk_activity_log';
    $affected =
        DB::table($table)->insert([
            [
                'activity' => $activity,
                'description' => $description,
                'ticket_id' => $ticket_id,
                'ticket_number' => $ticket_number,
                'created_by' => $created_by
            ]
        ]);
}

function activity_log($activity, $table = '', $rel_id = 0, $user_id = 0, $description = null, $response = array())
{
    if (!is_array($rel_id)) {
        $rel_id = [$rel_id];
    }

    if (count($rel_id) > 0 && Schema::hasTable('activity_logs')) {
        foreach ($rel_id as $relid) {
            if (getUri(1) == config('app.admin_dir')) {
                $table = (!empty($table) ? $table : getUri(2));
                $user_id = ($user_id > 0) ? $user_id : Auth::id();
            } else {
                $table = (!empty($table) ? $table : getUri(1));
                $user_id = ($user_id > 0) ? $user_id : Auth::id();
            }
            $decrypt_response = decryptArray($response);
            $data = [
                'activity' => $activity,
                'table' => $table,
                'user_id' => $user_id,
                'user_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'rel_id' => $relid,
                'current_URL' => url()->current(),
                'description' => $description,
                'response' => json_encode($decrypt_response)
            ];
            $OBJ = new \App\ActivityLog();
            $OBJ->fill($data)->save();
            // dd($data);
            //DB::table('activity_logs')->insert($data);
        }
    }
}

function decryptArray($fields)
{
    $decData = [];

    foreach($fields as $key => $value) {
        $decData[$key] = Crypto::decryptData($value, Crypto::getAwsEncryptionKey());
    }
    return $decData;
}
function get_email_template($var_data_query, $template_name, $message = '')
{

    if (!empty($template_name)) {
        $template = DB::table('email_templates')->where('name', $template_name)->first();
        $template->message = str_replace(['../../../'], [url('')], $template->message);
    } else {
        $template = new stdClass();
        $template->message = $template->subject = $message;
    }

    if (is_object($var_data_query)) {
        $var_data_query = collect($var_data_query)->toArray();
    }

    if (!is_array($var_data_query)) {
        $var_data_query = DB::selectRaw($var_data_query);
    }

    $var_data_query['site_url'] = url('');
    $var_data_query['current_url'] = url()->current();
    $var_data_query['admin_url'] = admin_url('');
    $var_data_query['site_title'] = get_option('site_title');
    $var_data_query['contact_email'] = get_option('contact_email');
    $var_data_query['copyright'] = get_option('copyright');
    $var_data_query['logo_url'] = asset_url(config('app.admin_dir') . '/img/' . get_option('logo'));


    foreach ($var_data_query as $col => $val) {
        $template->subject = stripslashes(str_ireplace('[' . $col . ']', $val, $template->subject));
        $template->message = stripslashes(str_ireplace('[' . $col . ']', $val, $template->message));
    }
    $template->subject = preg_replace('/\[(.*)\]/', '', $template->subject);
    $template->message = preg_replace('/\[(.*)\]/', '', $template->message);

    if (empty($template_name) && !empty($message)) {
        return $template->message;
    }
    return $template;
}

/**
 * @param $table
 * @param array $ignore
 * @param null $data
 * @return mixed
 */
function DB_FormFields($table, $ignore = [], $data = null)
{


    if (is_object($table)) {
        $OBJ = new $table;
        $table = $OBJ->getTable();
    }
    if ($data === null) {
        $data = request()->input();
    }

    $DB_DATA['table'] = $table;
    $DB_DATA['data'] = [];
    $DB_DATA['where'] = [];

    $check_encrypted = check_encrypted_modules($DB_DATA['table']);
    $columns = DB_columns($table);

    if (count($columns) > 0 && count($data) > 0) {
        foreach ($columns as $column) {

            if (isset($data[$column->Field]) && !in_array($column->Field, $ignore) && !in_array($column->Key, ['PRI'])) {
                $COL_FIELD = $check_encrypted->is_encrypted == 'Yes' ? Crypto::encryptData($data[$column->Field], Crypto::getAwsEncryptionKey()) : $data[$column->Field];
                $DB_DATA['data'][$column->Field] = (is_array($data[$column->Field]) ? json_encode($data[$column->Field]) : $COL_FIELD);
            }
            if ($column->Key == 'PRI' && isset($data[$column->Field])) {
                $DB_DATA['where'][$column->Field] = $data[$column->Field];
            }
        }
    }
    return $DB_DATA;
}

function check_encrypted_modules($module){
    return DB::table('modules')->where('module', $module)->first();
}

/**
 * @param $table
 * @param $data
 * @param null $where
 * @return int
 */
function save($table, $data, $where = null)
{
    if (is_object($table)) {
        $OBJ = new $table;
        if ($where === null) {
            $row = $OBJ->firstOrCreate($data);
            return $row->id;
        } else if (is_array($where)) {
            $OBJ = DB::table($OBJ->getTable());
            $OBJ->where($where);
            return $OBJ->update($data);
        } else {
            return false;
        }
    } else if (is_string($table)) {
        $OBJ = DB::table($table);
        if ($where === null) {
            return $OBJ->insertGetId($data);
        } else if (is_array($where)) {
            $OBJ->where($where);
            return $OBJ->update($data);
        } else {
            return false;
        }
    }
}


function user_do_action($action = null, $module = null, $strict = false)
{
    $id = getUri(4);
    $module = $module ?? getUri(2);
    if ($action !== null) {
        switch ($action) {
            case 'index':
                $action = null;
                break;
            case 'form':
                $action = 'add';
                if ($id > 0) {
                    $action = 'edit';
                }
                break;
        }
    }

    $row = DB::selectOne("SELECT m.id
     , um.actions AS user_actions
     , m.actions
FROM users AS u
        INNER JOIN user_type_module_rel AS um ON (u.user_type_id = um.user_type_id)
        INNER JOIN modules AS m ON (m.id = um.module_id)
    WHERE u.id = " . Auth::user()->id . "
    AND m.module=" . DBescape($module));

    $module_actions = collect(explode('|', str_replace(['update', 'add', 'edit'], ['edit', 'add|store', 'edit|store'], $row->actions)))->unique()->toArray();
    $user_actions = collect(explode('|', str_replace(['update', 'add', 'edit'], ['edit', 'add|store', 'edit|store'], $row->user_actions)))->unique();

    $user_actions = $user_actions->toArray();

    if (empty($action) && $row->id > 0) {
        return true;
    } else if (in_array($action, $user_actions) && in_array($action, $module_actions)) {
        return true;
    } else if (!in_array($action, $user_actions) && !in_array($action, $module_actions) && $strict) {
        return true;
    } else {
        return false;
    }
}

function sqlDtaTime($datetime = null, $format = 'Y-m-d H:i:s')
{
    $date = date($format, $datetime == null ? time() : strtotime(str_replace(['-'], '/', $datetime)));
    return $date;
}

function number_to_int($number)
{
    return intval(str_replace(',', '', $number));
}

function dbEscape($value, $like = false)
{
    if ($like) {
        $char = '\\';
        return str_replace(
            [$char, '%', '_'],
            [$char . $char, $char . '%', $char . '_'],
            $value
        );
    }

    return DB::connection()->getPdo()->quote($value);
}

function last_query()
{
    $log = DB::getQueryLog();
    $query = last($log)['query'];
    $bindings = last($log)['bindings'];

    $boundSql = str_replace(['%', '?'], ['%%', "'%s'"], $query);
    $boundSql = vsprintf($boundSql, $bindings);

    return $boundSql;
}

function getWhereClause($query, $search_key = 's', $filter_key = 'f')
{
    $search = request($search_key);
    $filter = request($filter_key);

    foreach ($search as $key => $s) {
        if (preg_match("/'/u", $s)) {
            $search[$key] = str_replace('\'', "''", $s);
        }
    }

    $search_q = ' ';

    foreach ($search as $field => $value) {
        if (!empty($value)) {

            $re = "/(,)(.*?)(as|AS) ({$field})/";
            preg_match($re, $query, $table_alias);
            $column_alias = trim($table_alias[2]);

            if (empty($column_alias)) {
                preg_match('/\,(.*)?\.' . $field . '\b/i', $query, $table_alias);
                //dump($table_alias);
                $column_alias = trim($table_alias[1] . '.' . $field);
                if (!isset($table_alias[1])) {
                    $column_alias = $field;
                }
            }

            $operator = $filter[$field];
            $search_q .= filter_param($operator, $column_alias, $value) . " \n";
            //dd($search_q);
        }
    }

    //$search_q = substr($search_q, 4);
    return $search_q;
}

function filter_param($operator, $field, $value)
{
    if ($value == "Active") {

        $search_q = "AND {$field} LIKE '" . dbEscape($value, true) . "%' ";

        return $search_q;
    }

    switch ($operator) {
        case '%-%':
            $search_q = "AND {$field} LIKE '%" . dbEscape($value, true) . "%'";
            break;
        case '%!-%':
            $search_q = "AND {$field} NOT LIKE '%" . dbEscape($value, true) . "%'";
            break;
        case '-%':
            $search_q = "AND {$field} LIKE '" . dbEscape($value, true) . "%'";
            break;
        case '%-':
            $search_q = "AND {$field} LIKE '%" . dbEscape($value, true) . "'";
            break;
        case '=':
            $search_q = "AND {$field} = " . dbEscape($value);
            break;
        case '!=':
            $search_q = "AND {$field} != " . dbEscape($value);
            break;
        case '>':
            $search_q = "AND {$field} > " . dbEscape($value);
            break;
        case '>=':
            $search_q = "AND {$field} >= " . dbEscape($value);
            break;
        case '<':
            $search_q = "AND {$field} < " . dbEscape($value);
            break;
        case '<=':
            $search_q = "AND {$field} <= " . dbEscape($value);
            break;
        case 'date_range':
            $dates = explode(' - ', $value);
            $search_q = "AND {$field} BETWEEN " . dbEscape($dates[0]) . " AND " . dbEscape($dates[1]);
            break;
        default:
            $search_q = "AND {$field} LIKE '%" . dbEscape($value, true) . "%' ";
    }

    return $search_q;
}

function set_notification($text, $key = 'error')
{
    session()->flash('notification' . $key, $text);
}

function inserted_id()
{
    return DB::getPdo()->lastInsertId();
}


function thumb_box($input, $thumb_url, $delete_url)
{
    ob_start();
?>
    <div class="kt-avatar kt-avatar--outline kt-avatar--circle-" id="kt_apps_user_add_avatar fImg">
        <a href="<?php echo $thumb_url ?>" data-fancybox="image">
            <div class="kt-avatar__holder del-img" style="background-image: url(<?php echo _img($thumb_url, 115, 115); ?>);"></div>
        </a>
        <label class="kt-avatar__upload" data-skin="dark" data-toggle="kt-tooltip" title="choose image">
            <i class="fa fa-pen"></i>
            <?php echo $input; ?>
        </label>
        <span class="kt-avatar__cancel" data-skin="dark" data-toggle="kt-tooltip" title="remove image" data-original-title="Cancel avatar" href="<?php echo $delete_url; ?>">
            <i class="fa fa-times"></i>
        </span>
    </div>
<?php
    $HTML = ob_get_clean();
    return $HTML;
}


function insert_string($table, $data)
{
    $keys = $values = [];

    foreach ($data as $key => $val) {
        $keys[] = '`' . $key . '`';
        $values[] = dbEscape($val);
    }
    return 'INSERT INTO ' . $table . ' (' . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ')';
}

function update_string($table, $values, $where = '')
{
    foreach ($values as $key => $val) {
        $valstr[] = $key . ' = ' . $val;
    }

    return 'UPDATE ' . $table . ' SET ' . implode(', ', $valstr) . $where;
}

function uploadedFilesIcons(string $file_type)
{
    switch ($file_type) {
        case 'pdf':
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/' . $file_type . '.svg') . '"/>';
            break;

        case 'docx':
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/doc.svg') . '"/>';
            break;

        case 'docs':
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/doc.svg') . '"/>';
            break;

        case 'jpg':
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/jpg.svg') . '"/>';
            break;

        case 'jpeg':
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/jpg.svg') . '"/>';
            break;

        case 'png':
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/png.svg') . '"/>';
            break;

        default:
            return '<img class="max-h-65px" src="' . asset('theme/assets/media/svg/files/file.png') . '"/>';
    }
}

function sendTicketEmail($owner, $action, $ticket)
{

    // $owner = 'muhammad.shafique@lathran.com';
    $data = ['action' => $action, 'ticket' => $ticket];
    $ticketStatus = DB::table('helpdesk_ticket_statuses')->find($data['ticket']['ticket_status'] ?? 1);
    $data['ticket']['ticket_status'] = $ticketStatus->status;
    $data['ticket']['ticket_owner'] = $owner;

    if ($ticket['ticket_priority']) {
        $ticketPriority = DB::table('helpdesk_priorities')->find($ticket['ticket_priority']);
        $data['ticket']['ticket_priority'] = $ticketPriority->priority;
    }

        $emailTemplate = EmailTemplate::where('name','Ticket Info')->where('status','Active')->first();
        if(isset($emailTemplate)){

            $html = $emailTemplate->message;
            $output = preg_replace_callback(
                '~\{(.*?)\}~',
                function ($key) use($data) {
                    $variable["opt('site_title')"] = opt('site_title');
                    $variable["opt('site_url')"] = opt('site_url');
                    $variable["data['action']"] = $data['action'];
                    $variable["data['ticket']['ticket_owner']"] = $data['ticket']['ticket_owner'];
                    $variable["data['ticket']['ticket_number']"] = $data['ticket']['ticket_number'];
                    $variable["data['ticket']['ticket_creator']"] = $data['ticket']['ticket_creator'];
                    $variable["data['ticket']['ticket_title']"] = $data['ticket']['ticket_title'];
                    $variable["data['ticket']['ticket_status']"] = $data['ticket']['ticket_status'];
                    $variable["data['ticket']['ticket_priority']"] = $data['ticket']['ticket_priority'];
                    $variable["data['ticket']['ticket_description']"] = $data['ticket']['ticket_description'];
                    $variable["data['ticket']['ticket_creator']"] = Crypto::decryptData($data['ticket']['ticket_creator'], Crypto::getAwsEncryptionKey());
                    $variable["date('Y')"] = date('Y');
                    if ($variable[$key[1]] != null) {
                        return $variable[$key[1]];
                    }else{
                        return "";
                        return $key[0];
                    }
                },
                $html,
            );
            // dd($output);
            $blade_from_db = view('admin.mails.dynamic_mail',['data',$data,'html' => $output])->render();
        Mail::send([], $data, function ($message) use ($owner, $action, $ticket,$blade_from_db) {
            $subject = "HelpDesk Support " . $ticket['ticket_number'] . " - " . $action;
            $message->from(opt('smtp_email_from_address'), opt('smtp_email_from_name'));
            $message->to($owner, 'Evo-User')->subject($subject);
            $message->bcc(getSupportEmails());
            $message->setBody($blade_from_db,'text/html');
            // must be fetch from settings table
        });
    }
        }
function getSupportEmails()
{
    $data = json_decode(opt('support_emails'));
    $dataToReturn =[];
        foreach ($data as $key => $value) {
            $dataToReturn[] = $value->value;
        }
        return $dataToReturn;
}
function send_aws_sns_sms($country_code, $phone, $message)
{
    $phone_number = "+" . $country_code . $phone;
    $params = [
        'credentials' => [
            'key'    => config('services.sns.key'),
            'secret' => config('services.sns.secret'),
        ],
        'region' => config('services.sns.region'),
        'version' => 'latest',
        'http' => [
            'verify' => false,
        ],
        // 'ua_append' => [
        //     'L5MOD/' . AwsServiceProvider::VERSION,
        // ],
    ];
    $sns = new \Aws\Sns\SnsClient($params);
    $args = [
        'Message' => $message,
        'PhoneNumber' => $phone_number,
        'MessageAttributes' => [
            'AWS.SNS.SMS.SMSType'  => [
                'DataType'    => 'String',
                'StringValue' => 'Transactional',
            ]
        ],
    ];
    $sns->publish($args);
}

function curl_request(
    $url = null,
    $method = "GET",
    $body = array(),
    $headers = array(
        "Accept: */*",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Content-Type: application/json",
        "cache-control: no-cache"
    ),
    $timeout = 30
) {

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_HTTPHEADER => $headers,
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return $response ?? $err;
}
