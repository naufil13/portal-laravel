@php
$params = [
    'title' => 'Refresh',
    'class' => 'btn btn-label-danger btn-md btn-sm',
    'href' => admin_url('{_module}'),
    'icon_cls' => 'la la-refresh',
];
Form_btn::add_button('refresh', $params, true);
$form_buttons = ['new', 'delete', 'import', 'export', 'refresh'];
@endphp
@extends('admin.layouts.admin')

@section('content')

    <form action="{{ admin_url('', true) }}" method="post" enctype="multipart/form-data" id="kt_form_1">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-body">
                        @php
                            
                            $grid = new Grid();
                            $grid->status_column_data = $status_column_data;
                            $grid->filterable = false;
                            $grid->show_paging_bar = false;
                            $grid->grid_buttons = ['edit', 'delete', 'status' => ['status' => 'status'], 'view', 'duplicate', 'my_files', 'popup'];
                            
                            $grid->init($paginate_OBJ, $query);
                            
                            $grid->dt_column(['id' => ['title' => 'ID', 'width' => '20', 'align' => 'center', 'th_align' => 'center', 'hide' => true]]);
                            $grid->dt_column(['user_id' => ['hide' => true]]);
                            $grid->dt_column([
                                'username' => [
                                    'wrap' => function ($value, $field, $row, $grid) {
                                        return "<a target='_blank' href='" . admin_url("users/view/{$row['user_id']}") . "'>{$value}</a>";
                                    },
                                ],
                            ]);
                            
                            $grid->dt_column([
                                'status' => [
                                    'overflow' => 'initial',
                                    'align' => 'center',
                                    'th_align' => 'center',
                                    'filter_value' => '=',
                                    'input_options' => ['options' => $grid->status_column_data, 'class' => '', 'onchange' => true],
                                    'wrap' => function ($value, $field, $row, $grid) {
                                        return status_options($value, $row, $field, $grid);
                                    },
                                ],
                            ]);
                            $grid->dt_column([
                                'ordering' => [
                                    'width' => '90',
                                    'align' => 'center',
                                    'th_align' => 'center',
                                    'wrap' => function ($value, $field, $row, $grid) {
                                        return ordering_input($value, $row, $field, $grid);
                                    },
                                ],
                            ]);
                            
                            $grid->dt_column(['created' => ['input_options' => ['class' => 'm_datepicker']]]);
                            $grid->dt_column([
                                'grid_actions' => [
                                    'width' => '150',
                                    'check_action' => function ($row, $html, $button) {
                                        //if($button != 'delete')
                                        return $html;
                                    },
                                ],
                            ]);
                            
                            echo $grid->showGrid();
                        @endphp
                    </div>
                </div>
                <div class="kt-portlet__foot mt-2">
                    <!--begin: Pagination(sm)-->
                    <div class="kt-pagination kt-pagination--sm kt-pagination--danger">
                        @php
                            echo $grid->getTFoot();
                        @endphp
                    </div>
                    <!--end: Pagination-->
                    &nbsp;&nbsp;
                </div>
            </div>
        </div>
    </form>

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection
