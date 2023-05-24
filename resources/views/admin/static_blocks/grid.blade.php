@php
    $status_column_data = DB_enumValues('static_blocks', 'status');
    $pass_data['form_buttons'] = ['new', 'delete', 'import', 'export'];
@endphp
@extends('admin.layouts.admin', $pass_data)

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <div class="kt-portlet__head-label">
                            <span class="kt-portlet__head-icon">
                                @if(!empty($_info->image))
                                    <img src="{{ _img(asset_url('media/icons/' . $_info->image, true), 28, 28) }}" alt="{{ $_info->title }}">
                                @else
                                    <i class="{{ (!empty($_info->icon) ? $_info->icon : 'flaticon-list-2') }}"></i>
                                @endif
                            </span>
                            <h3 class="kt-portlet__head-title"> {{ $_info->title }} </h3>
                        </div>
                    </div>
                    @include('admin.layouts.inc.portlet')
                </div>

                <div class="kt-portlet__body">
                    <div class="mt10"></div>

                    @php
                        $grid = new Grid();
                        $grid->status_column_data = $status_column_data;
                        $grid->filterable = false;
                        $grid->show_paging_bar = false;
                        $grid->grid_buttons = ['edit', 'delete', 'status' => ['status' => 'status'], 'view', 'duplicate'];

                        $grid->init($paginate_OBJ);

                        $grid->dt_column(['id' => ['title' => 'ID', 'width' => '20', 'align' => 'center', 'th_align' => 'center', 'hide' => true]]);
                        $grid->dt_column(['status' => ['overflow' => 'initial', 'align' => 'center', 'th_align' => 'center', 'filter_value' => '=', 'input_options' => ['options' => $grid->status_column_data, 'class' => '', 'onchange' => true],
                            'wrap' => function($value, $field, $row, $grid) {
                                return status_options($value, $row, $field, $grid);
                            }
                        ]]);
                        $grid->dt_column(['ordering' => ['width' => '90', 'align' => 'center', 'th_align' => 'center',
                            'wrap' => function($value, $field, $row, $grid) {
                                return ordering_input($value, $row, $field, $grid);
                            }
                        ]]);

                        $grid->dt_column(['created' => ['input_options' => ['class' => 'm_datepicker']]]);
                                            $grid->dt_column(['grid_actions' => ['width' => '150',
                            'check_action' => function($row, $html, $button){
                                //if($button != 'delete')
                                {
                                    return $html;
                                }
                            }
                        ]]);

                        echo $grid->showGrid();
                    @endphp
                </div>
                <div class="kt-portlet__foot">
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

    </div>
    <!-- end:: Content -->

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection
