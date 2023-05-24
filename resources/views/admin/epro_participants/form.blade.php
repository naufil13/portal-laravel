@php

$form_buttons = ['save', 'view'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="users">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <input type="hidden" name="id" class="form-control" placeholder="{{ __('ID') }}" value="{{ $row->id }}">
        <!-- begin:: Content -->


        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="first_name" class="col-2 col-form-label required">{{ __('First Name') }}:</label>
                            <div class="col-6">
                                <input type="text" name="first_name" id="first_name" class="form-control"
                                    placeholder="<?php echo __('First Name'); ?>" value="<?php echo htmlentities($row->first_name); ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="last_name" class="col-2 col-form-label">{{ __('Last Name') }}:</label>
                            <div class="col-6">
                                <input type="text" name="last_name" id="last_name" class="form-control"
                                    placeholder="<?php echo __('Last Name'); ?>" value="<?php echo htmlentities($row->last_name); ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-2 col-form-label required">{{ __('Email') }}:</label>
                            <div class="col-6">
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="<?php echo __('Email'); ?>" value="<?php echo htmlentities($row->email); ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-2 col-form-label required">{{ __('Tenant') }}:</label>
                            <div class="col-6">
                                <select name="tenants_id" id="tenants_id" class="form-control m-select2">
                                    <option value="">Select Tenant</option>
                                    @foreach ($tenants as $tenant)
                                        <option {{ $row->tenants_id == $tenant->id ? 'selected' : '' }}
                                            value="{{ $tenant->id }}">
                                            {{ Crypto::decryptData($tenant->tenant_name, Crypto::getAwsEncryptionKey()) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-2 col-form-label required">StandAlone:</label>
                            <div class="col-6">
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label class="check">
                                        <input type="hidden" name="is_standalone" value="No">
                                        <input type="checkbox" name="is_standalone" <?php echo $row->is_standalone == 'Yes' ? 'checked' : ''; ?> value="Yes">

                                        <span></span>
                                    </label>
                                </span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- table --}}
                <div class="card card-custom gutter-b mt-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="card-label">Application Roles</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="example-preview">
                            <table class="table table-bordered mb-6" id="role_table">
                                <thead>
                                    <tr>
                                        <th scope="col">Application</th>
                                        <th scope="col">Access</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Additional Params</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                {{-- table --}}

                <div class="card-custom card mt-10">
                    <div class="card-header">
                        <h3 class="card-title"> {{ __('Portal Credential') }} </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-2 col-form-label required">{{ __('User Type') }}:</label>
                            <div class="col-6">
                                <select name="user_type_id" id="user_type_id" class="form-control m-select2">
                                    <option value="" disabled >Select User Type</option>
                                    <?php echo selectBox("SELECT id, user_type FROM user_types WHERE user_type= 'Participant' And  hierarchy <= '" . Auth::user()->usertype->hierarchy . "'", $row->user_type_id); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="username"
                                class="col-2 col-form-label -text-right required">{{ __('Username') }}:</label>
                            <div class="col-6">
                                <input type="text" name="username" id="username" class="form-control"
                                    placeholder="<?php echo __('Username'); ?>" value="<?php echo htmlentities($row->username); ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-2 col-form-label required">{{ __('Password') }}:</label>
                            <div class="col-6">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="<?php echo __('Password'); ?>" value="" />
                                <p>Password must be a minimum of 8 and maximum of 30 characters with atleast one numric and special character</p>
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
        @if ($row->id > 0)
            // $('#password').rules('remove');
            // @endif

        $(document).ready(function($) {



            // console.log($('#client_id').find(":selected").val());
            var selectedClientId = $('#tenants_id').find(":selected").val();
            var clinical_studies_dropdown = '';
            @if ($user_application_trials_sites_roles[0]->user_id > 0)
                $.ajax({
                type: "get",
                url: `{{ admin_url('getClinicalTrialsByClient', true) }}/` + selectedClientId,
                success: function(clinic) {
                if (clinic) {
                // console.log('res',clinic);
                $.each(clinic, function(index, stateObj) {
                clinical_studies_dropdown += '<option value="'+stateObj.id+'">'+stateObj.study_name+'</option>';
                })
                }
                }
                });
            @endif
            // console.log('res',clinical_studies_dropdown);


            @if ($user_application_roles[0]->user_id > 0)
                <?php $_SESSION['trial_count'] = 0; ?>
                $.ajax({
                type: "get",
                url: `{{ admin_url('getAllowedApplicationsForUser', true) }}/` +
                {{ $row->id }},
                success: function(res) {
                if (res) {
                console.log(res);
                $('tbody').empty();

                $.each(res, function(index, stateObj) {
                console.log(stateObj.application_role);


                $('#role_table').append(
                `<tr>` +
                    `<td>` +
                        stateObj.application_name +
                        `</td>` +

                    `<td>` +
                        `<div class="form-group row">` +
                            `<div class="col-3">` +
                                `<span class="switch switch-outline switch-icon switch-primary">` +
                                    `<label class="check">` +
                                        `<input id="test" type="checkbox" checked="checked" class="onChecks"
                                            name="application_name[]"
                                            value="` +
                                            stateObj.application_id + `">`
                                        +
                                        `<span></span>` +
                                        `</label>` +
                                    `</span>` +
                                `</div>` +
                            `</div>` +
                        `</td>` +


                    `<td>\
                        <div class="form-group row">\
                            <div class="col-12">\
                                <select class="form-control role_id" id="role_id" name="role_id[]">\
                                    <option value>Select Role</option>\

                                    <?php foreach($roles as $role){ ?>
                                    <option data-role="`+stateObj.application_role+`" value='<?php echo $role->role; ?>'>
                                        <?php echo $role->role; ?></option>\
                                    <?php } ?>
                                </select>\
                            </div>\
                        </div>\
                    </td>
                    <?php if($user_application_trials_sites_roles[0]->user_id > 0){ ?>

                    <td class="sites_select">\
                        <div class="form-group row">\
                            <div class="col-12">\
                                <select class="form-control .study_id_drop" id="clinical_study" name="clinic_studies[]"
                                    onchange="getClinicalSites(this,`+index+`)">\
                                    <option value>Select Clinical Study</option>\
                                    `+clinical_studies_dropdown+`\
                                </select>\
                            </div>\
                        </div>\
                        <div class="form-group row" id="siteDropdown">\

                        </div>\
                    </td>\

                    <?php } ?>

                </tr>`
                );
                });
                }



                },
                complete:function(){
                $('.role_id').each(function(){
                var select=$(this);
                $(this).find('option').each(function(){
                $(this).attr('selected');
                if($(this).data('role')==$(this).val()){
                select.val($(this).val());
                }
                });
                });

                $('.study_id_drop').each(function(){
                var select=$(this);
                $(this).find('option').each(function(){
                $(this).attr('selected');
                if($(this).data('role')==$(this).val()){
                select.val($(this).val());
                }
                });
                });


                var trials=<?php echo $user_application_trials_sites_roles; ?>;
                console.log('trials',trials);

                for(i=0;i<$('select[name="clinic_studies[]"]').length;i++){ $('select[name="clinic_studies[]"]').eq(i).val(trials[i].trial_id).change(); } } }); // } @endif



            $('#tenants_id').on('change', function(e) {
                var client_id = $(this).val();
                var clinical_studies = '';
                // alert(client_id);
                //get clinical studies

                $.ajax({
                    type: "get",
                    url: `{{ admin_url('getApplicationsByClientId', true) }}/` + client_id,
                    success: function(res) {
                        if (res.length > 0) {

                            $.ajax({
                                type: "get",
                                url: `{{ admin_url('getClinicalTrialsByClient', true) }}/` +
                                    client_id,
                                success: function(clinic) {
                                    if (clinic) {
                                        $.each(clinic, function(index, stateObj) {
                                            clinical_studies +=
                                                '<option value="' + stateObj
                                                .id +
                                                '">' + stateObj.study_name +
                                                '</option>';
                                        })
                                    }



                                    $('tbody').empty();

                                    $.each(res, function(index, stateObj) {
                                        $('#role_table').append(
                                            `<tr>` +
                                            `<td>` +
                                            stateObj.application_name +
                                            `</td>` +

                                            `<td>` +
                                            `<div class="form-group row">` +
                                            `<div class="col-3">` +
                                            `<span class="switch switch-outline switch-icon switch-primary">` +
                                            `<label>` +
                                            `<input type="checkbox" name="application_name[]" value="` +
                                            stateObj.application_id +
                                            `">` +
                                            `<span></span>` +
                                            `</label>` +
                                            `</span>` +
                                            `</div>` +
                                            `</div>` +
                                            `</td>` +

                                            `<td>\
                                            <div class="form-group row">\
                                                <div class="col-12">\
                                                    <select class="form-control abc" id="role_id" name="role_id[]">\
                                                        <option value>Select Role</option>\

                                                        <?php foreach($roles as $role){ ?>
                                                        <option value='<?php echo $role->role; ?>'><?php echo $role->role; ?></option>\
                                                        <?php } ?>
                                                    </select>\
                                                </div>\
                                            </div>\
                                        </td>\

                                        <td class="sites_select">\
                                            <div class="form-group row">\
                                                <label for="clinical_studies" class="col-12 required">Clinical Studies:</label>\
                                                <div class="col-12">\
                                                    <select class="form-control" data-appid="`+stateObj.application_id+`" id="clinical_study" name="clinic_studies[]" onchange="getClinicalSites(this,`+stateObj.application_id+`)">\
                                                        <option value>Select Clinical Study</option>\
                                                        ` + clinical_studies + `\
                                                    </select>\
                                                </div>\
                                            </div>\
                                            <div class="form-group row" id="siteDropdown">\

                                            </div>\

                                        </td>\
                                    </tr>`
                                        );
                                    });
                                }
                            });


                        }
                    }
                });
            });

        });


        var is_load_change = 0;

        function getClinicalSites(e, i) {
            // alert(i);
            var study_id = e.value;

            var sites_option_selected = <?php echo $user_application_trials_sites_roles; ?>;
            // console.log($(selectObject).parent().parent().parent().closest('div').find('.sites_select').find('#siteDropdown').html());
            var formgroupRow = $(e).parents('.sites_select').find('#siteDropdown');
            console.log(formgroupRow);

            var sitesbyAjax = '';
            $.ajax({
                type: "get",
                url: `{{ admin_url('getSitesByClinicalStudy', true) }}/` + study_id,
                success: function(sites) {
                    if (sites) {
                        $.each(sites, function(index, stateObj) {
                            sitesbyAjax += '<option value="' +i+'|'+ stateObj.id + '|'+ study_id+  '">' + stateObj.site_name +
                                '</option>';
                        })
                        // console.log(sitesbyAjax);
                        formgroupRow.empty();
                        formgroupRow.html('');
                        formgroupRow.append(
                            `<div class="col-12">\
                                <label for="sites" class="col-12 required">Sites:</label>\
                                <select class="form-control" id="sites_dropdown" name="sites[]" multiple>\
                                    <option value disabled>Select Sites</option>\
                                    ` + sitesbyAjax + `\
                                </select>\
                            </div>`
                        );
                        console.log('testing', $('select[name="clinic_studies[]"]'));

                        if (i == $('select[name="clinic_studies[]"]').length) {
                            is_load_change = 1;
                        }
                        if (is_load_change == 0) {
                            formgroupRow.find('select[name="sites[]"]').val(sites_option_selected[i].site_id)
                                .change();

                        }

                        //
                    }
                }
            });

            // console.log(value);
        }
        // $(document).on('change','#clinical_study', function(e) {
        //     var study_id = $(this).val();
        //     $('.justADiv').remove();
        //     // $('#select').removeAttr('selected').find('option:first').attr('selected', 'selected');
        //     $(this).closest('div').find('#sites_dropdown').val('Select Sites')
        //     $.ajax({
        //         type: "get",
        //         url: `{{ admin_url('getSitesByClinicalStudy', true) }}/` + study_id,
        //         success: function(sites) {
        //             if (sites) {
        //                 $.each(sites, function(index, stateObj) {
        //                     sitesbyAjax += '<option value="'+stateObj.id+'">'+stateObj.site_name+'</option>';
        //                 })
        //                 // console.log(sitesbyAjax);
        //                 $('.sites_select').append(
        //                     `<div class="form-group row justADiv">\
    //                         <div class="col-12">\
    //                             <select class="form-control" id="sites_dropdown" name="sites[]">\
    //                                 <option value>Select Sites</option>\
    //                                 `+sitesbyAjax+`\
    //                             </select>\
    //                         </div>\
    //                     </div>`
        //                 );
        //             }
        //         }
        //     });
        // });

        $(document).on('change', '.onChecks', function() {
            if (!this.checked) {
                // checkbox is checked
                $(this).closest('tr').find('.role_id').val('Select Role')
                alert($(this).val());
                // console.log( $(this).prev("td").prev("td"));
                // console.log( $(this).closest("td").next("td"))
                // console.log($(this).closest('div').next('div').find('input').attr('id'));
                // console.log($(this).closest('td').find(".abc"));
                // console.log($(this).parent().next('td').html());
                // console.log($(this).closest('.row').next('div').find('.row').html());
                // alert($(this).parent().next().find('.abc').html());
                // console.log($(this).parent().closest('.row').next('td').find('.form-control').html());
                // console.log();
            }
        });
    </script>
@endsection
