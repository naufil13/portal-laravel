<?php

/**
 * Class Form_btn
 * @property Form_btn $form_btn
 */
class Form_btn
{
    var $base_url = '';

    var $module = '';
    var $id_uri = 4;
    var $user_actions = '';
    var $module_actions;

    var $status_column_data = [];

    private static $buttons = [];
    private static $public_buttons = [];

    //private


    function __construct()
    {
        if (empty($this->module)) {
            $this->module = getUri(2);
        }
        if (empty($this->base_url)) {
            $this->base_url = admin_url('');
        }

        $this->init();
        $this->app_buttons();
    }


    function init()
    {
        $user_type_id = Auth::User()->load('usertype')->user_type_id;

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
    }

    /**
     * @param $action
     * @param array $params {_module}/form/{_id}/{QUERY_STR}
     * @param bool $public
     */
    public static function add_button($action, $params = array(), $public = false)
    {

        if ($public) {
            self::$public_buttons[] = $action;
        }

        $_params = array(
            'title' => '',
            'href' => '',
            'attr' => '',
            'class' => 'btn-md btn-sm btn-info',
            'icon_cls' => 'la la-icon',
            'dropdown_items' => []
        );
        $params = array_merge($_params, $params);

        $icon_cls = $params['icon_cls'];

        if (count($params['dropdown_items']) == 0) {
            $_button_HTML = "<a " . (Str::contains($params['attr'], 'data-toggle=') ? '' : 'data-toggle="kt-tooltip"') . " data-skin=\"dark\" data-original-title='{$params['title']}' action='{$action}' " . $params['attr'];
            foreach ($params as $atr => $param) {
                if (in_array($atr, ['icon_cls', 'dropdown_items', 'dropdown_items_cls', 'attr'])) continue;
                $_button_HTML .= $atr . "='{$param}'";
            }
            $_button_HTML .= "><span><i class='{$icon_cls}'></i><span>{$params['title']}</span></span></a>";
        } else {
            $_button_HTML = '<div class="btn-group">';
            $_button_HTML .= '<button type="submit" action="' . $action . '" class="' . $params['class'] . '" data-toggle="kt-tooltip" data-skin="dark" data-original-title="' . $params['title'] . '" ' . $params['attr'] . '>';
            $_button_HTML .= '<span>';
            $_button_HTML .= '<i class="' . $icon_cls . '"></i>';
            $_button_HTML .= '<span>' . $params['title'] . '</span>';
            $_button_HTML .= '</span>';
            $_button_HTML .= '</button>';

            $_button_HTML .= '<button type="button" class="btn ' . (!empty($params['dropdown_cls']) ? $params['dropdown_cls'] : 'btn-brand') . ' dropdown-toggle dropdown-toggle-split m-btn m-btn--md" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>';
            $_button_HTML .= '<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">';
            foreach ($params['dropdown_items'] as $k => $item) {
                $href = replace_columns($params['href'], ['dropdown_item' => $k]);
                if ($k == '_divider_') {
                    $_button_HTML .= '<div class="' . $item['class'] . '"></div>';
                } else {
                    $_button_HTML .= '<a class="dropdown-item" href="' . $item['href'] . '" action="' . $k . '" ' . $item['attr'] . '>';
                    $_button_HTML .= '<i class="' . $item['icon_cls'] . '"></i> ' . $item['title'];
                    $_button_HTML .= '</a>';
                }
            }
            $_button_HTML .= '</div>';

            $_button_HTML .= '</div>';
        }

        self::$buttons[$action] = $_button_HTML;
    }

    function buttons($buttons)
    {
        $this->module_actions = array_merge($this->user_actions, self::$public_buttons);

        $HTML = '';
        //$HTML .= '<div class="m-btn-group m-btn-group--pill btn-group mr-2 form-btns" role="group" aria-label="...">';//Todo::form-btns
        foreach ($buttons as $key => $button) {
            $rows['_module'] = $this->module;

            if (!is_array($button)) {
                if (in_array($button, ['new'])) $button = 'add';
                if (in_array($button, ['save'])) $button = 'edit';
                if (in_array($button, ['save'])) $button = 'update';

                $__button = $button;

                if (!in_array($__button, $this->module_actions)) continue;
                $rows['QUERY_STR'] = getVar($__button);
                $HTML .= replace_columns(self::$buttons[$__button], $rows);
            } else {
                $__button = $key;
                if (!in_array($__button, $this->module_actions)) continue;
                if (in_array($__button, ['new'])) $button = 'add';
                if (in_array($__button, ['save'])) $button = 'edit';
                if (in_array($__button, ['save'])) $button = 'update';

                $QUERY_STR = [];
                foreach ($button as $__attr => $__tag) {
                    array_push($QUERY_STR, str_replace(['{', '}'], '', replace_columns($__attr . '={' . $__tag . '} ', $rows)));
                }
                $rows['QUERY_STR'] = '?' . join('&', $QUERY_STR);

                $HTML .= replace_columns(self::$buttons[$__button], $rows);
            }
        }

        //$HTML .= '</div>';
        return $HTML;
    }

    function app_buttons()
    {

        $params = [
            'title' => 'New',
            'class' => 'btn btn-light-info font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/form/', //{QUERY_STR}
            'icon_cls' => 'la la-file',
        ];
        $this->add_button('add', $params);
        $this->add_button('new', $params);

        $params = [
            'title' => 'Delete',
            'class' => 'btn btn-light-danger font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/delete',
            'icon_cls' => 'la la-trash',
        ];
        $this->add_button('delete', $params);

        $params = [
            'title' => 'Submit',
            'href' => $this->base_url . '/{_module}/{dropdown_item}/{QUERY_STR}',
            'class' => 'btn btn-light-primary font-weight-bold btn-sm ml-2',
            'icon_cls' => 'la la-floppy-o',
            'dropdown_items' => [
                'save_new' => ['title' => 'Save & New', 'icon_cls' => 'la la-plus', 'href' => $this->base_url . '/{_module}/form'],
                //'save_duplicate' => ['title' => 'Save & Duplicate', 'icon_cls' => 'la la-copy', 'href' => $this->base_url . '{_module}/form/{_id}'],
                '_divider_' => ['class' => 'dropdown-divider'],
                //'save_close' => ['title' => 'Save & Close', 'icon_cls' => 'la la-undo', 'href' => $this->base_url . '/{_module}'],
                'save_stay' => ['title' => 'Save & Stay', 'icon_cls' => 'la la-undo', 'href' => $this->base_url . '/{_module}/form/' . getUri($this->id_uri)],
            ],
            'dropdown_cls' => 'btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split',
        ];

        $this->add_button('save', $params);
        $this->add_button('edit', $params);
        $this->add_button('update', $params);

        $params = [
            'title' => 'Save',
            'href' => $this->base_url . '/{_module}/save/{QUERY_STR}',
            'class' => 'btn btn-light-primary font-weight-bold btn-sm ml-2',
            'icon_cls' => 'la la-floppy-o',
        ];
        $this->add_button('only_save', $params);

        $params = [
            'title' => 'Edit',
            'class' => 'btn btn-light-success font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/form/' . getUri($this->id_uri),
            'icon_cls' => 'la la-pencil',
        ];
        $this->add_button('edit_form', $params, true);

        $params = [
            'title' => 'View',
            'class' => 'btn btn-light-warning font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/view/' . getUri($this->id_uri),
            'icon_cls' => 'la la-object-ungroup',
        ];
        $this->add_button('view', $params);

        $params = [
            'title' => 'Import',
            'class' => 'btn btn-light-warning font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/import',
            'icon_cls' => 'la la-terminal',
        ];
        $this->add_button('import', $params);

        $params = [
            'title' => 'Export',
            'class' => 'btn btn-light-info font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/export',
            'icon_cls' => 'la la-superscript',
        ];
        $this->add_button('export', $params);

        $params = [
            'title' => 'Download',
            'class' => 'btn btn-light-primary font-weight-bold btn-sm ml-2',
            'href' => $this->base_url . '/{_module}/download/' . getUri($this->id_uri),
            'icon_cls' => 'la la-download',
        ];
        $this->add_button('download', $params);

        $params = [
            'title' => 'Duplicate',
            'class' => 'btn btn-label-info btn-md btn-sm',
            'href' => $this->base_url . '/{_module}/duplicate/' . getUri($this->id_uri),
            'icon_cls' => 'la la-copy',
        ];
        $this->add_button('duplicate', $params);

        $params = [
            'title' => 'Back',
            'class' => 'btn btn-default btn-md btn-sm ml-2',
            'href' => $this->base_url . '/{_module}',
            'icon_cls' => 'la la-angle-left',
        ];
        $this->add_button('back', $params, true);

        $params = [
            'title' => 'Print',
            'class' => 'btn btn-label-info btn-md btn-sm',
            'href' => '#',
            'icon_cls' => 'la la-print',
        ];
        $this->add_button('print', $params, true);
    }
}
