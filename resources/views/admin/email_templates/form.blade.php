@php

$pass_data['form_buttons'] = ['save', 'view', 'delete'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="email_templates">
        @csrf
        @include('admin.layouts.inc.stickybar', $pass_data)
        <input type="hidden" name="id" value="{{ $row->id }}">
        <input type="hidden" name="id" class="form-control" placeholder="ID" value="{{ $row->id }}">
        <!-- begin:: Content -->


        <div class="row mt-10">
            <div class="col-lg-8">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="name"
                                class="col-2 col-form-label text-right required"><?php echo __('Name'); ?>:</label>
                            <div class="col-8">
                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="<?php echo __('Name'); ?>" value="<?php echo htmlentities($row->name); ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="subject" class="col-2 col-form-label text-right required"><?php echo __('Subject'); ?>:</label>
                            <div class="col-8">
                                <input type="text" name="subject" id="subject" class="form-control"
                                    placeholder="<?php echo __('Subject'); ?>" value="<?php echo htmlentities($row->subject); ?>" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-2 col-form-label text-right"><?php echo __('Status'); ?>:</label>
                            <div class="col-8">
                                <select name="status" id="status" class="form-control m-select2">
                                    <option value="">- Select Status - </option>
                                    <?php echo selectBox(DB_enumValues('email_templates', 'status'), $row->status); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-2 col-form-label text-right"><?php echo __('Application'); ?>:</label>
                            <div class="col-8">
                                <select name="application" class="form-control m_selectpicker">
                                    <option disabled selected>- Select Application - </option>
                                    @foreach ($applications as $app)
                                    <option value="{{$app->id}}" {{$row->application == $app->id ? 'selected' : ''}}> {{$app->application_name}} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="message" class="col-2 col-form-label text-right">Email Template:</label>
                            <div class="col-10">
                                {{-- <input type="text" name="message" class="form-control" value=""> --}}
                                <textarea name="message" id="message" placeholder="Email Template"
                                    class="form-control"
                                    rows="3"><?php echo htmlentities($row->message); ?></textarea>
                            </div>
                        </div>

                        <div class="btn-group">
                            <input type="submit" class="btn btn-md btn-primary btn-sm" value="<?php echo __('Submit'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> Shortcodes </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-12">
                                <button class="btn btn-warning btn-sm mb-2 eventDefault">opt('site_title')</button>
                                <button class="btn btn-warning btn-sm mb-2 eventDefault">opt('site_url')</button>
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
    <script type="text/javascript">
    $('.eventDefault').click(function (event){
        event.preventDefault();
        var message = document.getElementById('message');
        message.value += '{'+this.innerHTML+'}';
        // $('#message').value += this.innerHTML;
    });
        (function($) {

            var tree = $("#tree-module").jstree({
                'plugins': ["checkbox"],
                'checkbox': {
                    "three_state": false
                },
                types: {
                    default: {
                        icon: "fa fa-folder"
                    },
                    file: {
                        icon: "fa fa-file"
                    }
                },
            });

            tree.on("changed.jstree", function(e, data) {
                console.log(data);
                if (data.node) {
                    $('#' + data.node.id + '_anchor').find('input:checkbox').prop('checked', data.node.state
                        .selected);
                }
                if (data.action == 'deselect_node') {
                    tree.jstree("close_node", "#" + data.node.id);
                } else {
                    tree.jstree("open_node", "#" + data.node.id);
                }
            });

            /*$(document).ready(function () {
                $('.tree-form').on('submit', function (e) {
                    $('.jstree input[type=checkbox]', this).each(function () {
                        $(this).prop('checked', false).removeAttr('checked');
                    });
                    $('.jstree-undetermined', this).each(function () {
                        $(this).parent().find('input').prop('checked', true);
                    });
                    $('.jstree-clicked', this).each(function () {
                        $(this).find('input').prop('checked', true);
                    });
                });
            });*/
        })(jQuery)
    </script>
    <script>
        $("form#user_types").validate({
            // define validation rules
            rules: {
                'user_type': {
                    required: true,
                },
                'level': {
                    required: true,
                    digits: true,
                },
            },
            /*messages: {
            'user_type' : {required: 'User Type is required',},'level' : {required: 'Level is required',integer: 'Level is valid integer',},    },*/
            //display error alert on form submit
            invalidHandler: function(event, validator) {
                validator.errorList[0].element.focus();

                /*var alert = $('#_msg');
                alert.removeClass('m--hide').show();
                mUtil.scrollTo(alert, -200);*/
                //mUtil.scrollTo(validator.errorList[0].element, -200);
            },

            submitHandler: function(form) {
                form.submit();
            }

        });
    </script>
@endsection
