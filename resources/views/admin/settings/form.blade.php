<style>
    #v-pills-tab i {
  padding-right: 10px;
}
</style>
@php

    $form_buttons = ['save', 'view', 'delete', 'back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="directories">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <input type="hidden" name="id" value="{{ $row->id }}">
        <!-- begin:: Content -->

        <div class="row mt-10">
            <div class="col-lg-12">

                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">

                        <div class="row">
                            <div class="col-3">
                                <div class="nav flex-column nav-pills mb-5" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                    <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" aria-controls="general"><i class="flaticon-settings"></i> General Setting</a>
                                    <a class="nav-link" id="header_footer-tab" data-toggle="pill" href="#header_footer" aria-controls="header_footer"><i class="flaticon2-cube-1"></i> Header & Footer</a>
                                    <a class="nav-link" id="contact-tab" data-toggle="pill" href="#contact" aria-controls="contact"><i class="flaticon2-open-text-book"></i> Contact Detail</a>
                                    <a class="nav-link" id="admin-tab" data-toggle="pill" href="#admin" aria-controls="admin"><i class="flaticon2-help"></i> Admin Setting</a>
                                    {{-- <a class="nav-link" id="social-tab" data-toggle="pill" href="#social" aria-controls="social"><i class="flaticon2-link"></i> Social Networks</a> --}}
                                    <a class="nav-link" id="smtp-tab" data-toggle="pill" href="#smtp" aria-controls="smtp"><i class="flaticon2-mail"></i> SMTP Setting</a>
                                    {{-- <a class="nav-link" id="db-tab" data-toggle="pill" href="#db" aria-controls="db"><i class="flaticon2-mail"></i> Database Setting</a> --}}
                                </div>
                            </div>
                            <div class="col-9">
                                <div class="tab-content mb-5 mr-5" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="general">@include('admin.settings.general')</div>
                                    <div class="tab-pane fade" id="header_footer">@include('admin.settings.header_footer')</div>
                                    <div class="tab-pane fade" id="contact">@include('admin.settings.contact')</div>
                                    <div class="tab-pane fade" id="admin">@include('admin.settings.admin')</div>
                                    <div class="tab-pane fade" id="social">@include('admin.settings.social')</div>
                                    <div class="tab-pane fade" id="smtp">@include('admin.settings.smtp')</div>
                                    {{-- <div class="tab-pane fade" id="db">@include('admin.settings.database')</div> --}}
                                    {{--<div class="tab-pane fade" id="widgets">@include('admin.settings.footer') </div>--}}
                                </div>
                            </div>
                        </div>

                        <div class="btn-group">
                            @php
                                $Form_btn = new Form_btn();
                                echo $Form_btn->buttons($form_buttons);
                            @endphp
                        </div>
                    </div>
                </div>


            </div>






        </div>
    </form>
    <!--end::Form-->
@endsection

{{-- Scripts --}}
@section('scripts')
    <script>

        $("form#directories").validate({
            // define validation rules
            rules: {
                'name': {
                    required: true,
                },
                'designation': {
                    required: true,
                },
            },
            /*messages: {
            'name' : {required: 'Name is required',},'designation' : {required: 'Designation is required',},    },*/
            //display error alert on form submit
            invalidHandler: function (event, validator) {
                KTUtil.scrollTop();
                //validator.errorList[0].element.focus();
            },
            submitHandler: function (form) {
                form.submit();
            }

        });
    </script>@endsection
