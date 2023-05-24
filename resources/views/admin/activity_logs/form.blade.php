@php

    $form_buttons = ['-save', 'view', 'delete', 'back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="activity_log">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <input type="hidden" name="id" class="form-control" placeholder="{{ __(ID) }}" value="{{ $row->id }}">
        <!-- begin:: Content -->


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
                                <h3 class="kt-portlet__head-title"> {{ $_info->title }} Form </h3>
                            </div>
                        </div>
                        @include('admin.layouts.inc.portlet')
                    </div>

                    <div class="kt-portlet__body">
                        <div class="mt10"></div>


                        <div class="form-group row">
                            <label for="activity" class="col-2 col-form-label">{{ __('Activity') }}:</label>
                            <div class="col-6">
                                <input type="text" name="activity" id="activity" class="form-control" placeholder="<?php echo __('Activity');?>" value="<?php echo htmlentities($row->activity);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="table" class="col-2 col-form-label">{{ __('Table') }}:</label>
                            <div class="col-6">
                                <input type="text" name="table" id="table" class="form-control" placeholder="<?php echo __('Table');?>" value="<?php echo htmlentities($row->table);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="user_id" class="col-2 col-form-label">{{ __('User ID') }}:</label>
                            <div class="col-6">
                                <input type="text" name="user_id" id="user_id" class="form-control" placeholder="<?php echo __('User ID');?>" value="<?php echo htmlentities($row->user_id);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="user_ip" class="col-2 col-form-label">{{ __('User Ip') }}:</label>
                            <div class="col-6">
                                <input type="text" name="user_ip" id="user_ip" class="form-control" placeholder="<?php echo __('User Ip');?>" value="<?php echo htmlentities($row->user_ip);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="user_agent" class="col-2 col-form-label">{{ __('User Agent') }}:</label>
                            <div class="col-6">
                                <input type="text" name="user_agent" id="user_agent" class="form-control" placeholder="<?php echo __('User Agent');?>" value="<?php echo htmlentities($row->user_agent);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="rel_id" class="col-2 col-form-label">{{ __('Rel ID') }}:</label>
                            <div class="col-6">
                                <input type="text" name="rel_id" id="rel_id" class="form-control" placeholder="<?php echo __('Rel ID');?>" value="<?php echo htmlentities($row->rel_id);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="current_URL" class="col-2 col-form-label">{{ __('Current URL') }}:</label>
                            <div class="col-6">
                                <input type="text" name="current_URL" id="current_URL" class="form-control" placeholder="<?php echo __('Current URL');?>" value="<?php echo htmlentities($row->current_URL);?>"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="description" class="col-2 col-form-label">{{ __('Description') }}:</label>
                            <div class="col-10">
                                <textarea name="description" id="description" class="form-control" placeholder="<?php echo __('Description');?>" cols="30" rows="5"><?php echo $row->description;?></textarea>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                    </div>

                    <div class="kt-portlet__foot">
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

        $("form#activity_log").validate({
            // define validation rules
            rules: {},
            /*messages: {
                },*/
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
