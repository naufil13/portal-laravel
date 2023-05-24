<?php

/**
 * Class Grid_btn
 * @property Grid_btn $actions_btn
 */
class Grid_btn
{
    var $base_url = '';

    var $module = '';
    var $user_actions = '';
    var $module_actions;

    public static $status_column_data = [];

    private static $buttons = [];
    public static $public_buttons = [];

    //private


    function __construct()
    {
        if (empty($this->module)) {
            $this->module = getUri(2);
        }
        if (empty($this->base_url)) {
            $this->base_url = admin_url();
        }

        $this->init();
        $this->app_grid_buttons();
    }


    function init()
    {

        $user_type_id = Auth::user()->user_type_id;

        $SQL = "SELECT
                user_type_module_rel.actions AS user_actions
                , modules.actions
            FROM users
                INNER JOIN user_type_module_rel ON (users.user_type_id = user_type_module_rel.user_type_id)
                INNER JOIN modules ON (user_type_module_rel.module_id = modules.id)
            WHERE user_type_module_rel.user_type_id='{$user_type_id}'
            AND modules.module = '{$this->module}'";

        $row = DB::selectOne($SQL);

        $this->user_actions = array_unique(explode('|', str_replace(array('update'), array('edit'), $row->user_actions)));
        $this->module_actions = array_unique(explode('|', str_replace(array('update'), array('edit'), $row->actions)));

        $user_actions = [];
        foreach ($this->module_actions as $module_action) {
            if (in_array($module_action, $this->user_actions)) {
                $user_actions[] = $module_action;
            }
        }
        $this->user_actions = $user_actions;
    }

    /**
     * @param $action
     * @param array $params {_module}/form/{_id}/{QUERY_STR}
     * @param bool $public
     */
    public static function add_button($action, $params = [], $public = false)
    {

        if ($public) {
            self::$public_buttons[] = $action;
        }

        $_params = array(
            'title' => '',
            'href' => '',
            'class' => '',
            'attr' => '',
            'icon_cls' => 'la la-edit',
            'dropdown_items' => [],
            'dropdown_items_cls' => []
        );
        $params = array_merge($_params, $params);

        $icon_cls = $params['icon_cls'];

        if (count($params['dropdown_items']) == 0) {

            $_button_HTML = "<a {$params['attr']} data-skin=\"dark\" data-toggle=\"kt-tooltip\" class=\"btn btn-sm btn-clean btn-icon mr-2\" action='{$action}'";
            foreach ($params as $atr => $param) {
                if (in_array($atr, ['icon_cls', 'dropdown_items', 'dropdown_items_cls', 'attr'])) continue;
                $_button_HTML .= $atr . "='{$param}'";
            }
            $_button_HTML .= "><i class='{$icon_cls}'></i></a>";
        } else {
            $_button_HTML = '<div class="dropdown dropup">';
            $_button_HTML .= '<a action=\'{$action}\' href="#" class="' . $params['class'] . '" title="' . $params['title'] . '" data-toggle="dropdown">';
            $_button_HTML .= '<i class="' . $icon_cls . '"></i>';
            $_button_HTML .= '</a>';
            $_button_HTML .= '<div class="dropdown-menu dropdown-menu-right">';
            foreach ($params['dropdown_items'] as $k => $item) {
                $href = replace_columns($params['href'], ['dropdown_item' => $item]);

                $_button_HTML .= '<a class="dropdown-item" ' . $item['attr'] . ' href="' . $href . '">';
                if (isset($params['dropdown_items_cls'][$k]) && !empty($params['dropdown_items_cls'][$k])) {
                    $_button_HTML .= '<i class="' . $params['dropdown_items_cls'][$k] . '"></i>';
                }
                $_button_HTML .= $item . '</a>';
            }
            $_button_HTML .= '</div></div>';
        }

        self::$buttons[$action] = $_button_HTML;
    }

    public function grid_buttons($rows, $id_field = 'id', $buttons, $callback = null)
    {
        $rows = (array)$rows;
        $this->module_actions = array_merge($this->user_actions, self::$public_buttons);

        $HTML = '';
        foreach ($buttons as $key => $button) {
            $rows['_module'] = $this->module;
            $rows['_id'] = $rows[$id_field];

            if (!is_array($button)) {
                $__button = $button;
                if (!in_array($__button, $this->module_actions)) continue;
                $rows['QUERY_STR'] = getVar($__button);
                if (is_callable($callback)) {
                    $button_html = replace_columns(self::$buttons[$__button], $rows);
                    $HTML .= call_user_func($callback, $rows, $button_html, $__button);
                } else {
                    $HTML .= replace_columns(self::$buttons[$__button], $rows);
                }
            } else {
                $__button = $key;
                if (!in_array($__button, $this->module_actions)) continue;

                $QUERY_STR = [];
                foreach ($button as $__attr => $__tag) {
                    array_push($QUERY_STR, str_replace(['{', '}'], '', replace_columns($__attr . '={' . $__tag . '} ', $rows)));
                }
                $rows['QUERY_STR'] = '?' . join('&', $QUERY_STR);

                if ($callback !== null && function_exists($callback)) {
                    $button_html = replace_columns(self::$buttons[$__button], $rows);
                    $HTML .= call_user_func($callback, $rows, $button_html, $__button);
                } else {
                    $HTML .= replace_columns(self::$buttons[$__button], $rows);
                }
            }
        }

        return $HTML;
    }

    function app_grid_buttons()
    {

        $params = [
            'title' => 'Edit',
            'href' => $this->base_url . '/{_module}/form/{_id}/{QUERY_STR}',
            'icon_cls' => 'flaticon2-pen text-primary',
        ];
        $this->add_button('edit', $params);


        $params = [
            'title' => 'Delete',
            'class' => '',
            'href' => $this->base_url . '/{_module}/delete/{_id}/{QUERY_STR}',
            'icon_cls' => 'flaticon2-trash text-danger',
        ];
        $this->add_button('delete', $params);

        $params = [
            'title' => 'Status',
            'href' => $this->base_url . '/{_module}/status/{_id}/{dropdown_item}/{QUERY_STR}',
            'class' => '',
            'icon_cls' => 'la la-ellipsis-h',
            'dropdown_items' => ['Active', 'Inactive'],
            //'dropdown_items_cls' => ['la la-edit', 'la la-print'],
        ];
        $this->add_button('-status', $params);


        $params = [
            'title' => 'View',
            'href' => $this->base_url . '/{_module}/view/{_id}/',
            'icon_cls' => 'la la-eye',
        ];
        $this->add_button('view', $params);

        $params = [
            'title' => 'Download',
            'href' => $this->base_url . '/{_module}/download/{_id}/',
            'icon_cls' => 'la la-download',
        ];
        $this->add_button('download', $params);

        $params = [
            'title' => 'Duplicate',
            'href' => $this->base_url . '/{_module}/duplicate/{_id}/',
            'icon_cls' => 'la la-copy',
        ];
        $this->add_button('duplicate', $params);
    }
}
