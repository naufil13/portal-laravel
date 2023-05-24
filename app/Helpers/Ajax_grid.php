<?php

/**
 * Class M_AJAX_grid
 * @property Admin_template $admin_template
 * @property Grid_btn $actions_btn
 */
class Ajax_grid
{

    var $limit = 25;
    var $start = 0;
    private $page = 1;

    var $cookie = false;
    var $web_storage = false;
    var $paging = true;
    var $filtering = true;
    var $sorting = true;
    var $theme = 'default';
    var $wrapper_class = '';
    var $scroll = false;
    var $footer = true;

    var $page_size_select = array(25, 50, 75, 100);

    var $search_filter_column = 'filter';
    var $search_filter_options = array(
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
    );

    var $id_field = 'id';
    var $order_by = '';
    var $order = 'DESC';

    private $query = '';

    var $custom_func = array();
    var $form_buttons = array();
    var $grid_buttons = array();

    var $dt_columns = array();

    var $db_fields = array();
    var $rows = array();
    var $total_rows = 0;

    var $db_error = '';
    var $url = '';


    var $status_column_data = '';


    function __construct()
    {
        $ci =& get_instance();
        if(empty($this->url)){
            $this->url = admin_url(getUri(2));
        }
        if(empty($this->order_by)){
            $this->order_by = $this->id_field;
        }

        $ci->load->library('Grid_btn');


        if(empty($this->status_column_data)){
            //$this->actions_btn->status_column_data = get_enum_values(getUri(2), 'status');
        }
    }


    function init($query, $offset = 0, $order_by = ''){

        $ci =& get_instance();

        $_FIELD = [
            'title' => __('#'),
            'width' => '40',
            'sortable' => 'false',
            'textAlign' => 'center',
            'selector' => ['class' => 'm-checkbox--solid m-checkbox--brand'],

        ];
        $this->dt_columns['chk_id'] = $_FIELD;

        if (strpos($query, 'SQL_CALC_FOUND_ROWS') !== 7) {
            $query = "SELECT SQL_CALC_FOUND_ROWS " . substr($query, 6);
        }

        $datatable = getVar('datatable');
        $pagination = $datatable['pagination'];
        $sort = $datatable['sort'];

        $this->page = ($pagination['page'] > 0 ? $pagination['page'] : 1);
        /**
         * -------- ORDER BY
         */
        if (empty($order_by)) {
            $order_by = " {$this->id_field} {$this->order}";
        }

        if(!empty($sort['field'])) {
            $order_by = " {$sort['field']} {$sort['sort']}";
        }

        $query .= " ORDER BY {$order_by}";

        /**
         *  ------- Pagination
         */
        $this->start = $offset;
        if($offset == 0 && $pagination['page'] > 1){
            $this->start = (($pagination['page'] - 1) * $pagination['perpage']);
        }

        /**
         * -------- LIMIT
         */
        if($pagination['perpage'] > 0){
            $this->limit = $pagination['perpage'];
        }
        if (strtolower($this->limit) != 'all') {
            $query .= " LIMIT {$this->start}, {$this->limit}";
        }

        $this->query = $query;
        /**
         * ----- Final Query
         */
        $result = $ci->db->query($query);
        if(!$result){
            $data['status'] = false;
            $this->db_error = $ci->db->error()['message'];
        }
        $this->total_rows = $ci->db->found_rows();

        /**
         * list Fields
         */
        if($this->start == 0) {
            $list_fields = $result->list_fields();
            foreach ($list_fields as $field) {
                array_push($this->db_fields, $field);
                $this->dt_columns[$field] = $field;
            }
        }


        $this->rows = [];
        foreach ($result->result_array() as $row) {
            $_row = new stdClass();
            $_row->chk_id = $row['id'];
            foreach ($row as $field => $value) {
                if (array_key_exists($field, $this->custom_func)) {
                    $value = call_user_func($this->custom_func[$field], $value, $row, $field);
                    $_row->{$field} = $value;
                } else {
                    $_row->{$field} = $value;
                }
            }

            $_row->grid_actions = $ci->actions_btn->grid_buttons($row, $this->id_field, $this->grid_buttons);
            array_push($this->rows, $_row);
        }

        $_FIELD = [
            'title' => __('Actions'),
            'sortable' => 'false',
            'textAlign' => 'center',
            'overflow' => 'visible',
            'filterable' => 'false',
        ];
        $this->dt_columns['grid_actions'] = $_FIELD;


        return $this->query;
    }
    /**
     * @return Admin_template
     */
    public function data()
    {
        if($this->total_rows){
            $JSON['status'] = true;
            $JSON['data'] = $this->rows;
            $JSON['meta'] = [
                'total' => $this->total_rows,
                'page' => ($this->page),
                'pages' => ceil($this->total_rows / $this->limit),
                'perpage' => $this->limit,
                'sort' => $this->order,
                'field' => $this->order_by,
            ];

        } else {
            $JSON['status'] = false;
            $JSON['message'] = $this->db_error;
        }

        return $JSON;
    }


    function dt_column($column){

        $field = key($column);
        if(in_array($field, $this->db_fields)){
            foreach ($this->db_fields as $field) {
                if(isset($column[$field])){
                    $this->dt_columns[$field] = $column[$field];
                }
            }
        }else if(isset($this->dt_columns[$field])){
            $this->dt_columns[$field] = array_merge($this->dt_columns[$field],$column[$field]);
        }
    }


    private function get_columns(){

        $JSON = [];

        foreach ($this->dt_columns as $column => $attrs) {
            $_FIELD = [];
            $_FIELD['field'] = $column;
            $_FIELD['title'] = __(ucwords(str_replace('_', ' ', $column)));
            $_FIELD['sortable'] = ($this->sorting ? 'true' : 'false');
            $_FIELD['filterable'] = ($this->filtering ? 'true' : 'false');

            foreach ($attrs as $attr => $val) {
                $_FIELD[$attr] = $val;
            }

            array_push($JSON, $_FIELD);
        }


        return $JSON;
    }


    function generate_script(){

        if(!in_array($this->limit, $this->page_size_select)){
            array_unshift($this->page_size_select, $this->limit);
        }

        ob_start();
        ?>
        <script>
            //== Class definition
            var RemoteAjax = function() {

                var generate_grid = function() {

                    var datatable = jQuery('.m_datatable').mDatatable({
                        data: {
                            type: 'remote',
                            source: {
                                read: {
                                    method: 'GET',
                                    url: '<?php echo $this->url;?>',
                                    map: function(raw) {
                                        // sample data mapping
                                        var dataSet = raw;
                                        if (typeof raw.data !== 'undefined') {
                                            dataSet = raw.data;
                                        }
                                        return dataSet;
                                    },
                                },
                            },
                            pageSize: <?php echo intval($this->limit);?>,
                            saveState: {
                                cookie: <?php echo ($this->cookie ? 'true' : 'false');?>,
                                webstorage: <?php echo ($this->web_storage ? 'true' : 'false');?>
                            },
                            serverPaging: <?php echo ($this->paging ? 'true' : 'false');?>,
                            serverFiltering: <?php echo ($this->filtering ? 'true' : 'false');?>,
                            serverSorting: <?php echo ($this->sorting ? 'true' : 'false');?>,
                        },

                        // layout definition
                        layout: {
                            theme: '<?php echo $this->theme;?>', // datatable theme
                            class: '<?php echo $this->wrapper_class;?>', // custom wrapper class
                            scroll: <?php echo ($this->scroll ? 'true' : 'false');?>, // enable/disable datatable scroll both horizontal and vertical when needed.
                            footer: <?php echo ($this->footer ? 'true' : 'false');?> // display/hide footer
                        },

                        // column sorting
                        sortable: <?php echo ($this->sorting ? 'true' : 'false');?>,

                        pagination: <?php echo ($this->paging ? 'true' : 'false');?>,

                        toolbar: {
                            items: {
                                pagination: {
                                    pageSizeSelect: [<?php echo join(',', $this->page_size_select);?>],
                                },
                            },
                        },

                        search: {
                            input: $('#generalSearch'),
                        },

                        // columns definition
                        <?php
                        $patterns = ['/\"template\"\:\"(.*?)\"/'];
                        $replacements = ['"template": $1'];
                        ?>
                        columns: <?php echo preg_replace($patterns, $replacements, json_encode($this->get_columns()));?>,
                    });



                    var query = datatable.getDataSourceQuery();

                    // on checkbox checked event
                    $('.m_datatable')
                        .on('m-datatable--on-init', function (e, args) {
                            console.log('m-datatable--on-init');
                            console.log(datatable);
                            /**/
                        })
                        .on('m-datatable--on-layout-updated', function (e, args) {
                            console.log('layout-updated');

                            /*if($('thead tr', datatable).length <= 1) {
                                var clone = $('thead tr', datatable).clone().addClass('tr-search');
                                $('thead').prepend(clone);

                                $('thead .tr-clone th').each(function () {
                                    var title = $(this).text();
                                    var field = $(this).data('field');
                                    $(this).removeAttr('class').removeAttr('data-field').addClass('m-datatable__cell');
                                    $('span', this).html('');
                                    if (field == 'chk_id' || field == 'grid_actions') {
                                        $(this).html(' ');
                                    } else {
                                        $('span', this).html('<input type="text" class="form-control m-input" placeholder="' + title + '" />');
                                    }
                                });
                                $('thead').prepend(clone);
                            }*/

                        })
                        .on('m-datatable--on-destroy', function (e, args) {
                            console.log('m-datatable--on-destroy');

                        })
                        .on('m-datatable--on-ajax-done', function (e, args) {
                            console.log('m-datatable--on-ajax-done');

                        })
                        .on('m-datatable--on-ajax-fail', function (e, args) {
                            console.log('m-datatable--on-ajax-fail');

                        })
                        .on('m-datatable--on-check', function (e, args) {
                            console.log('m-datatable--on-check');
                            var count = datatable.getSelectedRecords().length;
                            $('#m_datatable_selected_number').html(count);
                            if (count > 0) {
                                $('#m_datatable_group_action_form').collapse('show');
                            }
                        })
                        .on('m-datatable--on-uncheck ', function (e, args) {
                            console.log('m-datatable--on-uncheck');
                            var count = datatable.getSelectedRecords().length;
                            $('#m_datatable_selected_number').html(count);
                            if (count === 0) {
                                $('#m_datatable_group_action_form').collapse('hide');
                            }
                        });

                    $(document).on('change', 'input.ordering', function () {
                        $('#m_datatable_group_action_form').collapse('show');
                    });
                    $(document).on('click', '.update-grid', function () {
                        var grid_data = datatable.find('input, select, textarea').serialize();
                        $.ajax({
                            url: '<?php echo $this->url . '/AJAX/update_grid'?>',
                            data: grid_data,
                            dataType: "json",
                            type: "POST",
                            success: function (json) {
                                //console.log(json);
                            }
                        });
                    });
                    $(document).on('click', '.update-grid', function () {
                        var grid_data = datatable.find('input, select, textarea').serialize();
                        $.ajax({
                            url: '<?php echo $this->url . '/AJAX/update_grid'?>',
                            data: grid_data,
                            dataType: "json",
                            type: "POST",
                            success: function (json) {
                                //console.log(json);
                            }
                        });
                    });

                };

                return {
                    // public functions
                    init: function() {
                        generate_grid();
                    },
                };
            }();

            jQuery(document).ready(function() {
                RemoteAjax.init();
            });
        </script>
        <?php
        return ob_get_clean();
    }


    function record_selection($prams = array()){
        ob_start();
        ?>
        <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 collapse" id="m_datatable_group_action_form">
            <div class="row align-items-center">
                <div class="col-xl-12">
                    <div class="m-form__group m-form__group--inline text-center">
                        <div class="m-form__label m-form__label-no-wrap">
                            <label class="m--font-bold m--font-danger-">
                                Selected
                                <span id="m_datatable_selected_number"></span>
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
                                        <?php
                                        if (count($this->status_column_data) > 0) {
                                            foreach ($this->status_column_data as $status) {
                                                echo '<a class="dropdown-item" href="' . $this->url . '/status/' . $status . '">' . $status . '</a>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                &nbsp;&nbsp;&nbsp;
                                <a href="" class="btn btn-sm btn-danger" type="button" id="m_datatable_check_all">
                                    Delete Selected
                                </a>
                                &nbsp;&nbsp;&nbsp;
                                <a type="button" class="btn btn-success btn-sm update-grid"><?php echo __('Update Grid');?></a>
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
