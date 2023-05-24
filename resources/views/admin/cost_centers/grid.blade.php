@php
$params = [
    'title' => 'Import module',
    'class' => 'btn btn-label-warning btn-md btn-sm',
    //'href' => admin_url() . '{_module}/import_module/',//{QUERY_STR}
    'href' => '#upload-zip-modal',
    'attr' => 'data-toggle="modal" data-target="#upload-zip-modal"',
    'icon_cls' => 'la la-cogs',
];
Form_btn::add_button('import_module', $params);

$form_buttons = ['new'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}
    <!-- begin:: Content -->
    <form action="{{ admin_url('', true) }}" method="get" enctype="multipart/form-data">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-body">
                        @php
                            $status_column_data = DB_enumValues('modules', 'status');
                            $grid = new Grid();
                            $grid->status_column_data = $status_column_data;
                            $grid->filterable = false;
                            $grid->show_paging_bar = false;
                            $grid->url = admin_url('', true);
                            $grid->grid_buttons = ['edit', 'delete', 'status' => ['status' => 'status'], 'view', 'export_module'];
                            
                            $grid->init($paginate_OBJ, $query);
                            
                            $grid->dt_column(['id' => ['title' => 'ID', 'width' => '20', 'align' => 'center', 'th_align' => 'center', 'hide' => true]]);
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
                            $grid->dt_column(['icon' => ['align' => 'center', 'image_path' => asset_url('media/icons/', true)]]);
                            $grid->dt_column([
                                'image' => [
                                    'align' => 'center',
                                    'wrap' => function ($value, $field, $row) {
                                        $img = asset_url('media/icons/' . $value, true);
                                        return grid_img($img, 48, 48);
                                    },
                                ],
                            ]);
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

            </div>

        </div>
    </form>
    <!-- end:: Content -->

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection
