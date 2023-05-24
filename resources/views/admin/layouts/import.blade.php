@php
    $pass_data['form_buttons'] = ['back'];
@endphp
@extends('admin.layouts.admin', $pass_data)

@section('content')
{{-- Content --}}
<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <div class="kt-portlet__head-label">
                            <span class="kt-portlet__head-icon"> <i class="flaticon-list-2"></i> </span>
                            <h3 class="kt-portlet__head-title"> Information </h3>
                        </div>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-group">
                            <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-angle-down"></i></a>
                        </div>
                    </div>
                </div>

                <div class="kt-portlet__body">
                    <div class="mt10"></div>
                    <form action="" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-lg-2 ">Upload</label>
                            <div class="col-lg-6">
                                <input type="file" name="file" class="form-control-file">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <input type="submit" name="submit" class="btn btn-success" value="submit">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="kt-portlet__foot">
                    &nbsp;&nbsp;
                </div>
            </div>
        </div>

    </div>
</div>
<!-- end:: Content -->

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection
