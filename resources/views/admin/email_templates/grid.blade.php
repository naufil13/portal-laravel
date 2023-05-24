@php
$pass_data['form_buttons'] = ['new', 'delete', 'import', 'export'];
@endphp
@extends('admin.layouts.admin', $pass_data)

@section('content')

    <form action="{{ admin_url('', true) }}" method="get" enctype="multipart/form-data">
        @csrf
        @include('admin.layouts.inc.stickybar', $pass_data)
        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-body">
                        @php
                            $grid = new Grid();
                            $grid->status_column_data = $status_column_data;
                            $grid->filterable = false;
                            $grid->show_paging_bar = false;
                            $grid->grid_buttons = ['edit', 'delete', 'status' => ['status' => 'status'], 'view', 'duplicate'];
                            
                            
                            $grid->init($paginate_OBJ);
                            
                            $grid->dt_column(['id' => ['title' => 'ID', 'width' => '20', 'align' => 'center', 'th_align' => 'center', 'hide' => true]]);
                            //$grid->dt_column(['user_type' => ['width' => '200']]);
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
                            
                            //$grid->dt_column(['created' => ['input_options' => ['class' => 'm_datepicker']]]);
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
