<?php

class Grid
{

    var $title = '';
    private $paginate_OBJ;

    var $id_field = 'id';

    var $module_uri = 2;
    var $url = null;

    var $form_buttons = [];
    var $grid_buttons = [];

    var $is_front = FALSE;
    var $selectAllCheckbox = TRUE;
    var $serial = FALSE;
    var $actionColumn = TRUE;
    var $sorting = TRUE;
    var $search_box = TRUE;
    var $advance_search_html = '';
    var $thead = '';
    var $form_start = '';
    var $form_close = '';

    var $filterable = TRUE;
    var $filter_options = array(
        '%-%' => 'Contain',
        '%!-%' => 'Not Contain',
        '-%' => 'Start With',
        '%-' => 'End With',
        '=' => 'Equal',
        '!=' => 'Not Equal',
        '>' => 'Greater Then',
        '>=' => 'Greater Then Equal',
        '<' => 'Less Then',
        '=<' => 'Less Then Equal',
        //'between' => 'Between',
        //'date_range' => 'Date range',
    );

    var $show_paging_bar = TRUE;
    var $record_not_found = 'Record not found.';

    var $show_entries = [25, 50, 75, 100, ['all' => 'All']];



    var $grid_start = '<div class="widget">';
    var $grid_end = '</div>';

    private $dt_columns = [];
    private $db_fields = [];

    protected $rows = [];
    var $total_rows;
    var $limit;
    var $start;

    var $status_column_data = '';
    var $column_data = [];

    var $form_method = 'GET';
    var $db_error;
    var $display_records = 'Displaying {start} - {show_limit} of {total} records';

    var $escape_tags = ['title', 'field', 'filterable', 'sortable', 'selector', 'search_input', 'overflow', 'input_options'];
    var $meta_column = ['search' => 's', 'filter' => 'f', 'sort' => 'sort', 'dir' => 'dir', 'page' => 'page', 'limit' => 'limit'];

    var $css_classes = [
        'checkbox' => 'm-checkbox--brand',
        'filter' => 'la la-filter',
        'filter_select' => 'm_selectpicker',
        'sort_arrow_up' => 'la la-arrow-up',
        'sort_arrow_down' => 'la la-arrow-down',
    ];
    private $show_columns = [];
    var $image_size = '60x60';
    var $alt_image = '';


    function __construct()
    {

        $this->alt_image = env('IMG_NA');
        $this->url = $this->url ?? admin_url('', true);

        /*$this->form_start = '<form action="' . $this->url . '" method="' . $this->form_method . '" enctype="multipart/form-data" class="grid_form print-me"  data-print-hide=".search-tr, .gth-ids, .gtd-ids,.gth-grid_actions, .gtd-grid_actions, tfoot">';
        $this->form_close = '</form>';*/
    }


    function init($paginate_OBJ, $SQL = '')
    {

        $this->paginate_OBJ = $paginate_OBJ;

        $paginate_data = collect($this->paginate_OBJ)->toArray();
        $rows = $paginate_data['data'];
        unset($paginate_data['data']);

        $this->total_rows = $this->meta_column['total'] = $paginate_data['total'];
        $this->meta_column['page'] = $paginate_data['current_page'];
        $this->meta_column['pages'] = $paginate_data['last_page'];
        $this->limit = $this->meta_column['perpage'] = $paginate_data['per_page'];
        $this->start = $paginate_data['from'];

        if ($this->is_front) {
            $this->show_paging_bar = $this->actionColumn = $this->sorting = $this->search_box = $this->selectAllCheckbox = false;
        }

        if ($this->selectAllCheckbox) {
            $_FIELD = [
                'title' => __('#'),
                'width' => '40',
                'align' => 'center',
                'sortable' => false,

            ];
            $this->dt_columns['ids'] = $_FIELD;
        }

        if ($this->serial) {
            $_FIELD = [
                'title' => __('S.No'),
                'width' => '40',
                'sortable' => false,
                'filterable' => false,
                'search_input' => '',
                'align' => 'center',
                'th_align' => 'center',

            ];
            $this->dt_columns['serial'] = $_FIELD;
        }

        $list_fields = DB_list_fields($rows[0]);
        if (count($list_fields) == 0 && !empty($SQL)) {
            $columns = DB::getPdo()->query(str_replace('?', "?", $SQL));
            foreach (range(0, $columns->columnCount() - 1) as $i) {
                $list_fields[] = $columns->getColumnMeta($i)['name'];
            }
        }

        foreach ($list_fields as $field) {
            array_push($this->db_fields, $field);
            $this->dt_columns[$field] = $field;
        }

        $this->rows = [];
        foreach ($rows as $i => $row) {
            $row = (array)$row;
            $_row = [];
            if ($this->selectAllCheckbox) $_row['ids'] = $row['id'];
            if ($this->serial) $_row['serial'] = ($this->start + ($i));

            foreach ($row as $col => $value) {
                $_row[$col] = $value;
            }

            if ($this->actionColumn) {
                $_row['grid_actions'] = null;
            };
            array_push($this->rows, $_row);
        }

        if ($this->actionColumn) {
            $_FIELD = [
                'title' => __('Actions'),
                'sortable' => false,
                'align' => 'center',
                'width' => '120',
                'th_align' => 'center',
                'filterable' => false,
                'overflow' => 'initial',
            ];
            $this->dt_columns['grid_actions'] = $_FIELD;
        }

        return $this->rows;
    }


    function dt_column($column)
    {
        $field = key($column);
        if (in_array($field, $this->db_fields)) {
            foreach ($this->db_fields as $field) {
                if (isset($column[$field])) {
                    $this->dt_columns[$field] = $column[$field];
                }
            }
        } else if (isset($this->dt_columns[$field])) {
            $this->dt_columns[$field] = array_merge($this->dt_columns[$field], $column[$field]);
        }
    }


    private function get_columns()
    {
        $JSON = [];

        foreach ($this->dt_columns as $column => $attrs) {

            $_FIELD = [];
            $_FIELD['field'] = $column;
            $_FIELD['title'] = __(ucwords(str_replace('_', ' ', $column)));
            $_FIELD['sortable'] = $this->sorting;
            $_FIELD['filterable'] = $this->filterable;

            if (is_array($attrs)) {
                $_FIELD = array_merge($_FIELD, $attrs);
            }
            array_push($JSON, $_FIELD);
        }

        return $JSON;
    }


    public function showGrid()
    {

        $grid = '';
        /*if($this->show_validation_errors){
            $grid .= show_validation_errors();
        }*/

        $grid .= $this->form_start;
        //$grid .= $this->selection_box();
        $grid .= $this->getAdvanceSearch();
        $grid .= '<div class="table-responsive">';
        $grid .= '<table class="table table-hover dt-table  icon-color grid-table kt-margin-0">';
        $grid .= '<thead>';

        if ($this->search_box) {
            $grid .= $this->getSearch();
        }
        $grid .= $this->getTHead();
        $grid .= '</thead>';
        $grid .= '<tbody>';
        $grid .= $this->getTBody();
        $grid .= '</body>';
        if ($this->show_paging_bar) {
            $grid .= $this->getTFoot();
        }
        $grid .= '</table>';
        $grid .= '</div>';
        $grid .= $this->form_close;

        return $grid;
    }

    /**
     * @return string
     */
    function gridHeader()
    {
        ob_start();
        if (!empty($this->title)) {
            echo '<div class="panel-heading">
                    <h6 class="panel-title"><i class="icon-insert-template"></i> ' . $this->title . '</h6>
                  </div>';
        }
        if (count($this->form_buttons) > 0) {
            echo get_form_actions($this->form_buttons, $this->module_uri, $this->form_action_privilege);
        }

        return ob_get_clean();
    }


    function getAdvanceSearch()
    {
        if (!empty($this->advance_search_html)) {
            return '<span class="x-print">' . $this->advance_search_html . '<span>';
        }
    }

    private function generate_attr($attrs)
    {
        $_attr = '';
        foreach ($attrs as $attr => $val) {
            if (in_array($attr, $this->escape_tags)) continue;
            $_attr .= ' ' . $attr . '="' . $val . '"';
        }
        return $_attr;
    }

    function getTHead()
    {
        if (!empty($this->thead)) {
            return $this->thead;
        }
        ob_start();
        $s_col = $this->meta_column['sort'];
        $dir_col = $this->meta_column['dir'];

        $_columns = $this->get_columns();

        echo '<tr class="tr-title">';
        
        foreach ($_columns as $column) {
            if ($column['hide']) continue;
            array_push($this->show_columns, $column);
            switch ($column['field']) {
                case 'ids':
                    // echo "<th class='gth-{$column['field']}' align='{$column['align']}' width='{$column['width']}'><label class='kt-checkbox kt-checkbox--solid {$this->css_classes['checkbox']}'> <input type='checkbox' id='checkAll' class='check-all'><span></span> </label></th>" . "\n";
                    break;
                case 'grid_actions':
                    echo "<th class='gth-grid_actions text-center' align='{$column['th_align']}' width='{$column['width']}'>{$column['title']}</th>" . "\n";
                    break;
                default:
                    if ($column['sortable'] && $this->sorting) {
                        $order_dir = 'DESC';
                        $order_icon = '';
                        if ($this->sort == $column['field']) {
                            if ($this->order == $order_dir) {
                                $order_dir = 'ASC';
                                $order_icon = "<i class='{$this->css_classes['sort_arrow_down']}'></i>";
                            } else {
                                $order_icon = "<i class='{$this->css_classes['sort_arrow_up']}'></i>";
                            }
                        }

                        $_order_url = generate_url([$s_col => $column['field'], $dir_col => $order_dir]);
                        echo "<th class='gth-{$column['field']}' align='{$column['th_align']}' width='{$column['width']}'>";
                        echo "<a href='{$_order_url}'>{$column['title']} {$order_icon}</a>" . "\n";
                        echo "</th>" . "\n";
                    } else {
                        echo "<th width='{$column['width']}' align='{$column['th_align']}'>{$column['title']}</th>" . "\n";
                    }
                    break;
            }
        }
        echo '</tr>';
        $HTML = ob_get_clean();
        return $HTML;
    }


    function getSearch()
    {
        ob_start();
        $f_col = $this->meta_column['filter'];
        $filter_data = request($f_col);

        $s_col = $this->meta_column['search'];

        $search_data = request($s_col);
        $_columns = $this->get_columns();

        echo '<tr class="search-tr" style="display:none !important;">';
        foreach ($_columns as $column) {
            if (isset($column['hide'])) continue;
            /** --------- Filter */
            $filter_value = Arr::first($this->filter_options);

            if (!empty($column['filter_value'])) {
                $filter_value = $column['filter_value'];
            }
            if (!empty($filter_data[$column['field']])) {
                $filter_value = $filter_data[$column['field']];
            }

            $filter_tag_open = $filter_tag_end = $filter_html = '';

            if ($column['filterable']) {
                $filter_tag_open .= '<div class="m-input-icon m-input-icon--right">';
                $filter_tag_end .= '</div>';

                $filter_html = '<span class="filter-div m-input-icon__icon m-input-icon__icon--right">';
                $filter_html .= '<span class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-center" m-dropdown-toggle="click" m-dropdown-persistent="true">';
                $filter_html .= "<span class='m-dropdown__toggle'><i class='{$this->css_classes['filter']}'></i></span>";

                $filter_html .= '<div class="m-dropdown__wrapper"><span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
                                <div class="m-dropdown__inner"><div class="m-dropdown__body">
                                <div class="m-dropdown__content">';
                $filter_html .= "<select name='{$f_col}[{$column['field']}]' id='{$f_col}-{$column['field']}' class='{$this->css_classes['filter_select']}'>" . selectBox($this->filter_options, $filter_value) . "</select>";
                $filter_html .= '</div></div></div>
                                </span></span></span>';
            }
            /** --------- Filter */
            $input_value = htmlentities($search_data[$column['field']], ENT_QUOTES, "UTF-8");
            //$input_value = $search_data[$column]['field'];

            echo "<th width='{$column['width']}' class='sth-{$column['field']}' align='center'>";
            switch ($column['field']) {
                case 'ids':
                    //echo "<th>&nbsp;</th>" . "\n";
                    break;
                case 'grid_actions':
                    echo "<button type='submit' class='btn btn-label-brand btn-bold'><i class=\"flaticon-search\"></i> Search</button>" . "\n";
                    break;
                default:
                    if (count($column['input_options']) && is_array($column['input_options'])) {
                        $s_input = key($column['input_options']);
                        $s_class = $column['input_options']['class'];
                        $_attr = $column['input_options']['attr'];

                        switch ($s_input) {
                            case 'options':
                                $onchange = $column['input_options']['onchange'];

                                $_cls = (!empty($s_class) ? $s_class : 'm-bootstrap-select m_selectpicker');
                                $options = $column['input_options']['options'];
                                if (!key_exists('', $column['input_options']['options'])) {
                                    $options = ['' => $column['title']];
                                    $options += $column['input_options']['options'];
                                    //$options = array_merge($options, $column['input_options']['options']);
                                }

                                echo $filter_tag_open;
                                echo "<select name='{$s_col}[{$column['field']}]' id='{$column['field']}' {$_attr} class='form-control {$_cls}' " . ($onchange ? 'onchange="$(this).closest(\'form\').submit();"' : '') . ">" . selectBox($options, $input_value) . "</select>" . "\n";
                                echo $filter_html;
                                echo $filter_tag_end;
                                break;
                            case 'date_range':
                                $_cls = (!empty($s_class) ? $s_class : 'datepicker');
                                echo '<div class="row">';
                                echo '<div class="col-sm-6 m-input-icon m-input-icon--right">';
                                echo "<input type='text' autocomplete='off' placeholder='From' {$_attr} class='form-control {$_cls}' name='{$s_col}[{$column['field']}][from]' value='{$search_data[$column['field']]['from']}' />" . "\n";
                                echo '</div><div class="col-sm-6 m-input-icon m-input-icon--right">';
                                echo "<input type='text' autocomplete='off' placeholder='To' {$_attr} class='form-control {$_cls}' name='{$s_col}[{$column['field']}][to]' value='{$search_data[$column['field']]['to']}'/>" . "\n";
                                echo '</div></div>';
                                break;
                            default:
                                $_cls = (!empty($s_class) ? $s_class : '');
                                echo $filter_tag_open;
                                echo "<input type='text' class='form-control {$_cls}' name='{$s_col}[{$column['field']}]' placeholder='{$column['title']}' value='{$input_value}' {$_attr}>" . "\n";
                                echo $filter_html;
                                echo $filter_tag_end;
                                break;
                        }
                    } else if (isset($column['search_input'])) {
                        echo $column['search_input'] . "\n";
                    } else if (key_exists($column['field'], $this->search_fields_html)) {
                        echo $this->search_fields_html[$column['field']];
                    } else {
                        echo $filter_tag_open;
                        echo "<input type='text' class='form-control' name='{$s_col}[{$column['field']}]' placeholder='{$column['title']}' value='{$input_value}'>" . "\n";
                        echo $filter_html;
                        echo $filter_tag_end;
                    }

                    break;
            }
            echo "</th>";
        }
        echo '</tr>';
        $HTML = ob_get_clean();
        return $HTML;
    }


    function getTBody()
    {
        ob_start();
        if (count($this->rows) > 0) {
            $_columns = $this->get_columns();

            foreach ($this->rows as $i => $row) {
                $O_E = ($i % 2 == 0) ? 'odd' : 'even';
                echo "<tr class='{$O_E}'>";
                $f_key = -1;
                foreach ($row as $field => $value) {
                    $f_key++;
                    $column = $_columns[$f_key];

                    $column['class'] .= 'gtd-' . $field;
                    $style = (!empty($column['width']) ? "max-width: {$column['width']}px;" : "");
                    $style .= (!empty($column['overflow']) ? "overflow: {$column['overflow']};" : "");
                    $overflow_style = "white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:40px;";

                    if ($column['hide']) continue;
                    switch ($field) {
                        case 'ids':
                            // echo "<td " . $this->generate_attr($column) . ">";
                            // $value = "<label class='kt-checkbox kt-checkbox--solid {$this->css_classes['checkbox']}'><input type='checkbox' name='{$column['field']}[]' value='{$value}' class='chk-id'><span></span></label>";
                            // if (isset($column['wrap']) && is_callable($column['wrap'])) {
                            //     $value = call_user_func($column['wrap'], $value, $field, $row, $this);
                            // }
                            // echo $value . "</td>" . "\n";
                            break;
                        default:
                            if ($field == 'grid_actions') {
                                $Grid_btn = new Grid_btn();
                                $value =  $Grid_btn->grid_buttons($row, $this->id_field, $this->grid_buttons, $column['check_action']);
                            }
                            if (isset($column['wrap']) && is_callable($column['wrap'])) {
                                $value = call_user_func($column['wrap'], $value, $field, $row, $this);
                            }
                            unset($column['wrap'], $column['check_action']);
                            if (isset($column['image_path']) && !empty($column['image_path'])) {
                                echo "<td " . $this->generate_attr($column) . " style='{$style}'>";
                                if (empty($column['image_size'])) {
                                    $column['image_size'] = $this->image_size;
                                }
                                $image_size = explode('x', $column['image_size']);
                                $thumb_file = _img($column['image_path'] . '/' . $value, $image_size[0], $image_size[1], $this->alt_image, 'zoomCrop');

                                echo "<a href='" . $column['image_path'] . '/' . $value . "' data-fancybox=\"gallery\"><img src='" . $thumb_file . "' alt='{$value}' width='{$image_size[0]}' height='{$image_size[1]}' title='{$value}' class='img-fluid img_center -kt-img-rounded -shadow-sm' data-skin='dark' data-toggle='kt-tooltip' title='click to zoom'></a>";
                                echo "</td>" . "\n";
                            } else {  

                                if($column['title']=='Status' || $column['title']=='Actions' ){
                                    echo "<td " . $this->generate_attr($column) . " style='{$style}'>{$value}</td>" . "\n";
                                }
                                else{
                                    if(strlen($value) > 30){
                                        echo "<td " . $this->generate_attr($column) . " style='{$style}{$overflow_style}'>{$value}</td>" . "\n";
                                    }else{
                                        echo "<td " . $this->generate_attr($column) . " style='{$style}'>{$value}</td>" . "\n";
                                    }
                                } 
                            }
                            break;
                    }
                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="' . (count($this->show_columns)) . '" valign="middle" align="left">' . $this->record_not_found . '</td></tr>';
        }

        return $HTML = ob_get_clean();
    }

    function getTFoot()
    {
        return "";
        $limit_field = $this->meta_column['limit'];
        $page = $this->page;
        $last       = ceil($this->total_rows / $this->limit);
        ob_start();
?>
<?php echo $this->createLinks(); ?>
<div class="kt-pagination__toolbar">
    <select class="form-control kt-font-danger" title="Limit" style="width: 60px;"
        onchange="window.location = '<?php echo generate_url($limit_field, false) . '&' . $limit_field . '='; ?>' + this.value">
        <?php
                $_show_entries = [];
                foreach ($this->show_entries as $k => $v) {
                    $_v = $_k = $v;
                    if (is_array($v)) {
                        $_k = key($v);
                        $_v = $v[$_k];
                    }
                    $_show_entries[$_k] = $_v;
                }
                echo selectBox($_show_entries, request($limit_field)); ?>
    </select>
    <span class="pagination__desc">
        <?php
                $total_limit = (($this->start - 1) + $this->limit);
                $total_limit = ($total_limit == 0 ? $this->total_rows : $total_limit);
                $data = new stdClass();
                $data->start = number_format($this->start);
                $data->show_limit = number_format(($total_limit > $this->total_rows ? $this->total_rows : $total_limit));
                $data->total = number_format($this->total_rows);
                echo replace_columns($this->display_records, $data);
                ?>
    </span>

</div>
<?php
        $html = ob_get_clean();
        return $html;
    }

    /**
     * @return string
     */

    public function createLinks($params = [], $view = null)
    {
        $view = $view ?? 'admin.layouts.inc.pagination';
        $uris = collect(request()->query())->except('page')->toArray();
        $html = $this->paginate_OBJ->appends($uris)->links($view, $params);
        return $html;
    }


    function selection_box()
    {
        ob_start();
    ?>
<div class="selection-box">
    <div class="row align-items-center">
        <div class="col-xl-12">
            <div class="m-form__group m-form__group--inline">
                <div class="m-form__label m-form__label-no-wrap">
                    <label class="m--font-bold m--font-danger-">
                        Selected
                        <span id="m_datatable_selected_number">10</span>
                        records:
                    </label>
                </div>
                <div class="m-form__control">
                    <div class="btn-toolbar">
                        <div class="dropdown">
                            <button type="button" class="btn btn-accent btn-sm dropdown-toggle" data-toggle="dropdown">
                                Update status
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a class="dropdown-item" href="#">
                                    Pending
                                </a>
                                <a class="dropdown-item" href="#">
                                    Delivered
                                </a>
                                <a class="dropdown-item" href="#">
                                    Canceled
                                </a>
                            </div>
                        </div>
                        &nbsp;&nbsp;&nbsp;
                        <button class="btn btn-sm btn-danger" type="button" id="m_datatable_check_all">
                            Delete All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
        return ob_get_clean();
    }
}