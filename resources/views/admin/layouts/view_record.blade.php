@php
$pass_data['form_buttons'] = $config['buttons'];
$edit_key = array_search('edit', $config['buttons'], true);
if ($edit_key !== null && user_do_action('edit')) {
    $pass_data['form_buttons'][$edit_key] = 'edit_form';
}
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}
    <!-- begin:: Content -->
    <form action="{{ admin_url('', true) }}" method="get" enctype="multipart/form-data">
        @csrf
        @include('admin.layouts.inc.stickybar', $pass_data)
        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet_head-title mt-5"> View {{ $_info->title }}</h3>
                            </div>
                        </div>
                        {{-- @include('admin.layouts.inc.portlet') --}}
                    </div>

                    <div class="kt-portlet__body kt-padding-0">
                        @php
                            $view = new Record_view();
                            $view->row = $row;
                            if (count($config)) {
                                foreach ($config as $conf_key => $conf) {
                                    $view->{$conf_key} = $conf;
                                }
                            }
                            echo $view->showView();
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
