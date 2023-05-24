@php
    $form_buttons = ['new', 'delete', 'import', 'export'];
@endphp
@extends('admin.layouts.admin')

@section('content')

    <form action="{{ admin_url('', true) }}" method="get" enctype="multipart/form-data" id="members-form">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <div class="row">
            <div class="col-lg-12">

                @include('admin.members.stats')

                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_members">
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

                    <div class="kt-portlet__body kt-padding-0">
                        @php
                            $params = [
                                'title' => 'Sync Directory',
                                'class' => 'self-action',
                                'href' => admin_url('members/ajax/sync_directory/{_id}'),
                                'icon_cls' => 'la la-share-alt kt-font-warning',
                            ];
                            Grid_btn::add_button('sync_directory', $params);

                            $params = [
                                'title' => 'Add Directories',
                                'class' => '',
                                'href' => admin_url('directories/add_directory/{_id}'),
                                'icon_cls' => 'la la-group kt-font-success',
                            ];
                            Grid_btn::add_button('add_directory', $params, true);

                            $params = [
                                'title' => 'Directories',
                                'class' => '',
                                'href' => admin_url('', 1) . '/ajax/directories/{_id}/',
                                'icon_cls' => 'la la-book kt-font-info',
                            ];
                            Grid_btn::add_button('directories', $params, true);

                            $grid = new Grid();
                            $grid->status_column_data = $status_column_data;
                            $grid->filterable = false;
                            $grid->show_paging_bar = false;
                            $grid->grid_buttons = ['edit', 'delete', 'status' => ['status' => 'status'], 'view', 'duplicate', 'directories', 'add_directory', 'sync_directory'];

                            $grid->init($paginate_OBJ, $query);

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
                            $grid->dt_column(['logo' => ['align' => 'center',
                                'wrap' => function($value, $field, $row) use ($_info) {
                                    return grid_img(asset_url("{$_info->module}/{$value}"), 48, 48);
                                }
                            ]]);
                            $grid->dt_column(['grid_actions' => ['width' => '150',
                                'check_action' => function($row, $html, $button){
                                    if($button == 'sync_directory') {
                                        if($row['company'] != 'Naz Textiles Pvt Ltd'){
                                            return $html;
                                        } else { return;}
                                    }
                                    return $html;
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
    </form>
    <script>
        $(document).ready(function () {
            $('a[action="sync_directory"]').bind('click', function (e) {
                e.preventDefault();

                let notify = $.notify('<strong>Synchronize</strong> Do not close this page...', {
                    type: 'info',
                    newest_on_top: true,
                    allow_dismiss: false,
                    showProgressbar: true
                });

                let _this = $(this);
                let url = _this.attr('href');
                $.ajax(url, {
                    type: 'GET',
                    data: {},
                    dataType: 'JSON',
                    success: function (json, status, xhr) {
                        //console.log(json);
                        notify.close();
                    },
                    error: function (jqXhr, textStatus, errorMessage) {
                        notify.close();
                        $.notify('<strong>Error</strong> ' + errorMessage, {type: 'danger'});
                    }
                });
            })
        });
    </script>
@endsection

{{-- Scripts --}}
@section('scripts')


@endsection
