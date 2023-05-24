@php

$form_buttons = ['save', 'view', 'back'];
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
                                    placeholder="<?php echo __('First Name'); ?>" value="<?php echo Crypto::decryptData(htmlentities($row->first_name), Crypto::getAwsEncryptionKey()); ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="last_name" class="col-2 col-form-label">{{ __('Last Name') }}:</label>
                            <div class="col-6">
                                <input type="text" name="last_name" id="last_name" class="form-control"
                                    placeholder="<?php echo __('Last Name'); ?>" value="<?php echo Crypto::decryptData(htmlentities($row->last_name), Crypto::getAwsEncryptionKey()); ?>" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-2 col-form-label required">{{ __('Email') }}:</label>
                            <div class="col-6">
                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="<?php echo __('Email'); ?>" value="<?php echo Crypto::decryptData(htmlentities($row->email), Crypto::getAwsEncryptionKey()); ?>" />
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

                        <div class="form-group row">
                            <label for="phone" class="col-2 col-form-label required">{{ __('Phone') }}:</label>
                            <div class="col-3">
                                <select name="country_code" id="country_code" class="form-control m-select2">
                                    <option value="" disabled selected>Select Country</option>
                                    <?php echo selectBox('SELECT phonecode, name FROM countries', old('country_code', $row->country_code), '<option {selected} value="{key}">{val} (+{key})</option>'); ?>
                                </select>
                            </div>
                            <div class="col-3">
                                <input type="text" name="phone" id="phone" class="form-control"
                                    placeholder="<?php echo __('Phone'); ?>" value="<?php echo Crypto::decryptData(htmlentities($row->phone), Crypto::getAwsEncryptionKey()); ?>" />
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
                                    <option value="">Select User Type</option>
                                    <?php echo selectBox("SELECT id, user_type FROM user_types WHERE hierarchy <= '" . Auth::user()->usertype->hierarchy . "'", $row->user_type_id); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="username"
                                class="col-2 col-form-label -text-right required">{{ __('Username') }}:</label>
                            <div class="col-6">
                                <input type="text" name="username" id="username" class="form-control"
                                    placeholder="<?php echo __('Username'); ?>" value="<?php echo Crypto::decryptData(htmlentities($row->username), Crypto::getAwsEncryptionKey()); ?>" />
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
                        <div class="form-group row">
                            <label for="password" class="col-2 col-form-label required">{{ __('Confirm Password') }}:</label>
                            <div class="col-6">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                                    placeholder="<?php echo __('Confirm Password'); ?>" value="" />
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

            var userSites = [];
            $(document).ready(function($) {
                // console.log($('#client_id').find(":selected").val());
                var selectedClientId = $('#tenants_id').find(":selected").val();
                var clinical_studies_dropdown = '';
                // console.log({{ $user_application_trials_sites_roles }});

                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getClinicalTrialsByClient', true) }}/` + selectedClientId,
                        success: function(clinic) {
                            if (clinic) {
                            // console.log('res',clinic);
                                $.each(clinic, function(index, stateObj) {
                                    clinical_studies_dropdown += '<option value="'+stateObj.id+'" data-clinic-id="' + stateObj.id + '">'+stateObj.study_name+'</option>';
                                })
                            }
                        }
                    });

                // console.log('res',clinical_studies_dropdown);

                @if ($user_application_roles[0]->user_id > 0)
                    <?php $_SESSION['trial_count'] = 0; ?>

                    // get Applications For User
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getAllowedApplicationsForUser', true) }}/` + {{ $row->id }},
                        success: function(res) {
                            if (res) {
                                // console.log(res);
                                $('tbody').empty();
                                if(!res.client_application) {
                                    alert("invalid tenant id");
                                    return;
                                }

                                if(!res.client_application.applications.length) {
                                    alert("No applications for this tenant!");
                                    return;
                                }

                                const allApplications = res.client_application.applications;
                                $.each(allApplications, function(index, application) {
                                    // console.log(stateObj.application_role);
                                    let appExist = null;
                                    if(res.user_allowed_apps.length) {
                                        appExist = res.user_allowed_apps.find(item => item.application_id == application.id);
                                    }

                                    $('#role_table').append(
                                    `<tr data-application-id=${application.id}>` +
                                        `<td>` +
                                            application.application_name +
                                            `</td>` +

                                        `<td>` +
                                            `<div class="form-group row">` +
                                                `<div class="col-3">` +
                                                    `<span class="switch switch-outline switch-icon switch-primary">` +
                                                        `<label class="check">` +
                                                            `<input id="test-${index}" type="checkbox" ${ appExist ? 'checked' : ''} class="onChecks"
                                                                name="application_name[]"
                                                                value="` +
                                                                application.id + `">`
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
                                                    <select class="form-control role_id" id="role_id-${index}" name="role_id[]">\
                                                        <option value>Select Role</option>\

                                                        <?php // foreach($roles as $role){ ?>
                                                        <!-- <option data-role="${appExist ? appExist.application_role : ''}" value='<?php // echo $role->role; ?>'> -->
                                                            <?php // echo $role->role; ?></option>\
                                                        <?php // } ?>

                                                        ${application.roles.map(role => `<option data-role="${appExist ? appExist.application_role : ''}" value='${role.user_type}'>${role.user_type}</option>`)}
                                                    </select>\
                                                </div>\
                                            </div>\
                                        </td>


                                        <td class="sites_select">\
                                            <div class="form-group row">\
                                                <div class="col-12">\
                                                    <select class="form-control study_id_drop" id="clinical_study-${application.id}" name="clinic_studies[]"
                                                        onchange="getClinicalSites(this,`+application.id+`)">\
                                                        <option value>Select Clinical Study</option>\
                                                        `+clinical_studies_dropdown+`\
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
                        },
                        complete: function() {
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
                            // console.log('trials',trials);

                            // for(i=0;i<$('select[name="clinic_studies[]"]').length;i++){
                            //     $('select[name="clinic_studies[]"]').eq(i).val(trials[i].trial_id).change();
                            // }
                            if(trials.length) {
                                trials.forEach(trail => {
                                    // get select element by application id
                                    //
                                //    var select = $(`tr[data-application-id="${trail.application_id}"]`).find(`#clinical_study`)
                                   var select = $(`tr[data-application-id="${trail.application_id}"]`).find(`#clinical_study-${trail.application_id}`)
                                    // set the val
                                   select.val(select.find(`option[data-clinic-id=${trail.trial_id}]`).val()).change()
                                });
                            }
                        }
                    });

                    // get Portal Trail Sites By UserId
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getPortalTrailSitesByUserId', true) }}/` + {{ $row->id }},
                        success: function(resp) {
                            userSites = resp;
                            // console.log({userSites});
                        },
                    });
                @endif


                // when user change tennant
                $('#tenants_id').on('change', function(e) {
                    var client_id = $(this).val();
                    var clinical_studies = '';
                    // alert(client_id);
                    //get clinical studies

                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getApplicationsByClientId', true) }}/` + client_id,
                        success: function(res) {
                            // console.log({res});
                            if(!res.applications) return
                            if (res.applications.length > 0) {
                                // when user change the tenants
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

                                        $.each(res.applications, function(index, application) {
                                            $('#role_table').append(
                                                `<tr>` +
                                                    `<td>` +
                                                    application.application_name +
                                                    `</td>` +

                                                    `<td>` +
                                                    `<div class="form-group row">` +
                                                    `<div class="col-3">` +
                                                    `<span class="switch switch-outline switch-icon switch-primary">` +
                                                    `<label>` +
                                                    `<input type="checkbox" name="application_name[]" value="` +
                                                    application.id +
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
                                                                ${application.roles.map(role => `<option value='${role.user_type}'>${role.user_type}</option>`)}
                                                                <?php // foreach($roles as $role){ ?>
                                                                <!-- <option value='<?php echo $role->role; ?>'><?php echo $role->role; ?></option>\ -->
                                                                <?php // } ?>
                                                            </select>\
                                                        </div>\
                                                    </div>\
                                                    </td>\

                                                    <td class="sites_select">\
                                                        <div class="form-group row">\
                                                            <label for="clinical_studies" class="col-12 required">Clinical Studies:</label>\
                                                            <div class="col-12">\
                                                                <select class="form-control" data-appid="`+application.id+`" id="clinical_study" name="clinic_studies[]" onchange="getClinicalSites(this,`+application.id+`)">\
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
                // console.log(i);
                var study_id = e.value;
                // console.log({userSites});
                var sites_option_selected = <?php echo $user_application_trials_sites_roles; ?>;
                // console.log($(selectObject).parent().parent().parent().closest('div').find('.sites_select').find('#siteDropdown').html());
                var formgroupRow = $(e).parents('.sites_select').find('#siteDropdown');
                // console.log(formgroupRow);

                var sitesbyAjax = '<div class="col-12"><div class="row justify-content-between">';
                $.ajax({
                    type: "get",
                    url: `{{ admin_url('getSitesByClinicalStudy', true) }}/` + study_id, //+ '/?user_id={{ $row->id }}',
                    success: function(sites) {
                        // console.log({sites});
                        if (sites) {
                            $.each(sites, function(index, stateObj) {
                                var siteChecked = -1;

                                sitesbyAjax += `<div class="col-md-6 ${(index + 1) % 2 == 0 ? 'd-flex flex-row-reverse' : ''}">
                                    <span class="switch switch-outline switch-icon switch-primary">
                                        <strong>${stateObj.site_name}: &nbsp; &nbsp;</strong>
                                        <label>
                                            <input type="checkbox" name="sites[]" value="${i}|${stateObj.id}|${study_id}" data-site-id="${stateObj.id}">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>`;

                                if(!sites[index + 1]) {
                                    sitesbyAjax += '</div></div>';
                                }
                                //sitesbyAjax += '<option value="' +i+'|'+ stateObj.id + '|'+ study_id+  '">' + stateObj.site_name + '</option>';
                            })
                            // console.log(sitesbyAjax);
                            formgroupRow.empty();
                            formgroupRow.html('');
                            formgroupRow.append(sitesbyAjax);
                            // console.log('testing', $('select[name="clinic_studies[]"]'));
                            if(userSites.length) {
                                userSites.forEach((item, index) => {
                                    var row = $(`tr[data-application-id="${item.application_id}"]`)
                                    const applicationId = $(row).attr("data-application-id")
                                    var select = row.find(`#clinical_study-${item.application_id}`)
                                    var trialId = $(select).find(":selected").val();
                                    var input = $(row).find(`input[data-site-id=${item.site_id}]`);
                                    if(input.length) {
                                        input.prop('checked', true);
                                    }
                                })
                            }
                            if (i == $('select[name="clinic_studies[]"]').length) {
                                is_load_change = 1;
                            }
                            // if (is_load_change == 0) {
                            //     if(sites_option_selected.length) {
                            //         formgroupRow.find('select[name="sites[]"]').val(sites_option_selected[i].site_id)
                            //         .change();
                            //     }
                            // }
                        }
                    }
                });
            }

        $(document).on('change', '.onChecks', function() {
            if (!this.checked) {
                // checkbox is checked
                $(this).closest('tr').find('.role_id').val('')
                $(this).closest('tr').find('.study_id_drop').val('')
                $(this).closest('tr').find('input[type=checkbox]').prop('checked', false)
                $(this).closest('tr').find('#siteDropdown').html('')

                // alert($(this).val());
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
