@php
    $pass_data['form_buttons'] = ['save', 'view', 'delete', 'back'];
@endphp
@extends('admin.layouts.admin', ['is_form', true])

@section('content')
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="static_blocks">
        @csrf
        <input type="hidden" name="id" value="{{ $row->id }}">
        <input type="hidden" name="id" class="form-control" placeholder="ID" value="{{ $row->id }}">

        @include('admin.layouts.inc.stickybar', $pass_data)
        <!-- begin:: Content -->

        <div class="row">
            <div class="col-lg-12">
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon-file"></i> </span>
                                <h3 class="kt-portlet__head-title"> {{ $_info->title }} Form </h3>
                            </div>
                        </div>
                        @include('admin.layouts.inc.portlet')
                    </div>

                    <div class="kt-portlet__body">
                        <div class="mt10"></div>


                        <div class="form-group row">
                            <label for="title" class="col-2 col-form-label text-right required"><?php echo __('Title');?>:</label>
                            <div class="col-6">
                                <input type="text" name="title" id="title" class="form-control" placeholder="<?php echo __('Title');?>" value="<?php echo htmlentities($row->title);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="identifier" class="col-2 col-form-label text-right required"><?php echo __('Identifier');?>:</label>
                            <div class="col-6">
                                <input type="text" name="identifier" id="identifier" class="form-control" placeholder="<?php echo __('Identifier');?>" value="<?php echo htmlentities($row->identifier);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="content" class="col-2 col-form-label text-right"><?php echo __('Content');?>:</label>
                            <div class="col-10">
                                <textarea name="content" id="content" placeholder="<?php echo __('Content');?>" class="editor form-control" cols="30" rows="5"><?php echo $row->content;?></textarea>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="status" class="col-2 col-form-label text-right"><?php echo __('Status');?>:</label>
                            <div class="col-6">
                                <select name="status" id="status" class="form-control m_selectpicker">
                                    <option value="">Select Status</option>
                                    <?php echo selectBox(DB_enumValues('static_blocks', 'status'), ($row->status));?>
                                </select>
                            </div>
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
        $("form#static_blocks").validate({
            // define validation rules
            rules: {
                'title': {
                    required: true,
                },
                'identifier': {
                    required: true,
                },
            },
            /*messages: {
            'title' : {required: 'Title is required',},'identifier' : {required: 'Identifier is required',},    },*/
            //display error alert on form submit
            invalidHandler: function (event, validator) {
                validator.errorList[0].element.focus();
            },

            submitHandler: function (form) {
                form.submit();
            }

        });
    </script>
@endsection
