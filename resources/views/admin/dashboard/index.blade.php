@extends('admin.layouts.admin')
@section('title', 'Dashboard')
@php
$pass_data['form_buttons'] = array();
@endphp
@section('content')
    {{-- Content --}}
    @include('admin.layouts.inc.stickybar', $pass_data)
    <!-- begin:: Content -->

    <div class="alert alert-custom alert-white alert-shadow fade show gutter-b" role="alert">
        <div class="alert-icon">
            <span class="svg-icon svg-icon-primary svg-icon-2x">
                <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo3/dist/../src/media/svg/icons/Shopping/Sort3.svg--><svg
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px"
                    viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <rect x="0" y="0" width="24" height="24" />
                        <path
                            d="M18.5,6 C19.3284271,6 20,6.67157288 20,7.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 C17.6715729,20 17,19.3284271 17,18.5 L17,7.5 C17,6.67157288 17.6715729,6 18.5,6 Z M12.5,11 C13.3284271,11 14,11.6715729 14,12.5 L14,18.5 C14,19.3284271 13.3284271,20 12.5,20 C11.6715729,20 11,19.3284271 11,18.5 L11,12.5 C11,11.6715729 11.6715729,11 12.5,11 Z M6.5,15 C7.32842712,15 8,15.6715729 8,16.5 L8,18.5 C8,19.3284271 7.32842712,20 6.5,20 C5.67157288,20 5,19.3284271 5,18.5 L5,16.5 C5,15.6715729 5.67157288,15 6.5,15 Z"
                            fill="#000000" />
                    </g>
                </svg>
                <!--end::Svg Icon-->
            </span>
        </div>
        <div class="text-dark font-weight-bold my-1 mr-3">
            <h4>Dashboard</h4>
        </div>
    </div>

    <div class="row">
        @foreach ($applications as $application)
            <!-- Working Code -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6">
                <!--begin::Card-->
                <div class="card card-custom gutter-b card-stretch">
                    <!--begin::Body-->
                    <div class="card-body pt-4">
                        <!--begin::Toolbar-->

                        <!--end::Toolbar-->
                        <!--begin::User-->
                        <div class="d-flex align-items-center mb-7">
                            <!--begin::Pic-->
                            <div class="flex-shrink-0 mr-4">
                                <div class="symbol symbol-circle symbol-lg-75">
                                    <img src="{{ asset_url('media/applications/' . $application->image, 1) }}">
                                </div>
                            </div>
                            <!--end::Pic-->
                            <!--begin::Title-->
                            <div class="d-flex flex-column">
                                <a href="#"
                                    class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0">{{ $application->application_name }}</a>
                            </div>
                            <!--end::Title-->
                        </div>
                        <!--end::User-->
                        <!--begin::Desc-->
                        <p class="mb-1" >
                           <b>Description:</b> <br>{{ $application->application_description }}
                        </p>
                        <p class="mb-1">
                            @if(count(json_decode($application->technology_stack)[0])> 0)
                            <b>Tech Stack : </b><br>
                            @foreach (json_decode($application->technology_stack) as $stack)
                            <a class="badge badge-primary">{{$stack->value}}</a>
                            @endforeach
                            @endif
                        </p>
                        <p class="mb-1" >
                           <b>GA Version:</b> {{ $application->ga_version }}
                        </p>
                        <p class="mb-1" >
                           <b>GA Release Date:</b> {{ date('d-m-Y', strtotime($application->ga_release_date)) }}
                        </p>
                        <p class="mb-3" >
                           <b>Status:</b> <a class="badge badge-warning">{{ $application->application_status == 1 ? 'Active':'In-active' }}</a>
                        </p>
                        <!--end::Desc-->
                        <!--begin::Info-->

                        <!--end::Info-->
                        @if ($trial_site_count > 1)
                            <button type="button" class="btn btn-light-success font-weight-bolder btn-block"
                                data-toggle="modal" data-target="#exampleModalLong">
                                Launch App
                            </button>
                            <div class="modal fade" id="exampleModalLong" data-backdrop="static" tabindex="-1"
                                role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Launch App For</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <i aria-hidden="true" class="ki ki-close"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <?php foreach ($participant_trial_name as $participant_trial): ?>
                                            <p id="launchApp_{{ $application->id }}"
                                                class="btn btn-block btn-sm btn-light-success font-weight-bolder text-uppercase py-4 launchApp"
                                                data-trial-id="{{ $participant_trial->trial_id }}">
                                                {{ Crypto::decryptData($participant_trial->study_name, Crypto::getAwsEncryptionKey()) }}
                                            </p>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                        @php
                            $today = date("Y-m-d");
                            $expire = $application->ga_release_date; //from database

                            $today_time = strtotime($today);
                            $expire_time = strtotime($expire);
                        @endphp
                            @if ($expire_time < $today_time)
                            <p id="launchApp_{{ $application->id }}"
                                class="btn btn-block btn-sm btn-light-success font-weight-bolder text-uppercase py-4 launchApp"
                                data-trial-id="0">
                                Launch App
                            </p>
                            @else
                            <p class="btn btn-block btn-sm btn-light-success font-weight-bolder text-uppercase py-4 disabled" style="cursor: context-menu">
                                Launch App
                            </p>
                            @endif
                        @endif

                    </div>
                    <!--end::Body-->
                </div>
                <!--end:: Card-->
            </div>



            <!-- WOrking Code -->
        @endforeach
    </div>

@endsection

{{-- Scripts --}}
@section('scripts')

    <script type="text/javascript">
        $(document).ready(function($) {

            $(".launchApp").click(function() {
                var t_id = $(this).attr("data-trial-id");
                var txt = event.target.id.substring(10);

                console.log(txt);
                $.ajax({
                    type: 'GET',
                    url: `{{ admin_url('openApp/', true) }}`,
                    data: {
                        applicationId: txt,
                        trial_id: t_id
                    },
                    success: function(res) {
                        if (res) {
                            console.log(res);
                            const form = document.createElement('form');
                            form.method = 'GET';
                            form.action = res.application_url;
                            form.setAttribute("target", "_blank");

                            var token = document.createElement('input');
                            token.type = 'hidden';
                            token.name = "token";
                            token.value = res.token;
                            form.appendChild(token);

                            var application_code = document.createElement('input');
                            application_code.type = 'hidden';
                            application_code.name = "application_code";
                            application_code.value = res.application_code;
                            form.appendChild(application_code);

                            var email = document.createElement('input');
                            email.type = 'hidden';
                            email.name = "email";
                            email.value = res.email;
                            form.appendChild(email);

                            var application_role = document.createElement('input');
                            application_role.type = 'hidden';
                            application_role.name = "application_role";
                            application_role.value = res.application_role;
                            form.appendChild(application_role);

                            var first_name = document.createElement('input');
                            first_name.type = 'hidden';
                            first_name.name = "first_name";
                            first_name.value = res.first_name;
                            form.appendChild(first_name);

                            var last_name = document.createElement('input');
                            last_name.type = 'hidden';
                            last_name.name = "last_name";
                            last_name.value = res.last_name;
                            form.appendChild(last_name);

                            var username = document.createElement('input');
                            username.type = 'hidden';
                            username.name = "username";
                            username.value = res.username;
                            form.appendChild(username);

                            var sso_tenant_id = document.createElement('input');
                            sso_tenant_id.type = 'hidden';
                            sso_tenant_id.name = "sso_tenant_id";
                            sso_tenant_id.value = res.client_login_code;
                            form.appendChild(sso_tenant_id);

                            // Clinical Trial
                            var clinical_trial_id = document.createElement('input');
                            clinical_trial_id.type = 'hidden';
                            clinical_trial_id.name = "clinical_trial_id";
                            clinical_trial_id.value = res.clinical_trial_id;
                            form.appendChild(clinical_trial_id);

                            // Sites
                            var site_id = document.createElement('input');
                            site_id.type = 'hidden';
                            site_id.name = "site_id";
                            site_id.value = res.site_id;
                            form.appendChild(site_id);

                            document.body.appendChild(form);
                            form.submit();

                        }
                    }
                });
            });

        });
    </script>

@endsection
