@extends('admin.layouts.auth')

@section('content')

    <div class="login-content flex-row-fluid d-flex flex-column p-10"
        style="background-image: url('{{ asset_url('media/bg/bg-2.jpg', 1) }}');">

        <!--begin::Wrapper-->
        <div class="d-flex flex-row-fluid flex-center">

            <div class="login-form">
                <!--begin::Form-->
                <form method="POST" id="kt_login_singin_form" class="kt-form"
                    action="{{ $token && $participant_email_changes ? admin_url('login/do_login') . '?token=' . $token : admin_url('login/do_login') }}">
                    @csrf

                    <!--begin::Title-->
                    <div class="pb-5 pb-lg-15">
                        <h3 class="font-weight-bolder text-white font-size-h2 font-size-h1-lg">Log-In</h3>
                        <div class="text-white font-weight-bold font-size-h4">Please enter your Credentials

                        </div>
                    </div>
                    <!--begin::Title-->

                    <!--begin::Form group-->
                    <div class="form-group">
                        @if ($errors->any())
                            <label class="font-size-h6 font-weight-bolder text-danger">{{ $errors->first() }}</label>
                            <br>
                        @endif
                        @if (session()->has('message'))
                            <div class="alert alert-success">
                                {{ session()->get('message') }}
                            </div>
                        @endif
                        {{-- @if ($token && !$participant_email_changes)
                            <div class="alert alert-danger">
                                Token mismatch or expired.<br />Please login with your previous email
                            </div>
                        @endif --}}
                        <label class="font-size-h6 font-weight-bolder text-white">Username/Email</label>
                        <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="text" name="username" autocomplete="off" placeholder="Enter Username/Email"
                            @if ($token && $participant_email_changes)
                                value="{{ $participant_email_changes->new_email }}"
                                readonly
                            @endif
                        />
                    </div>
                    <!--end::Form group-->
                    <!--begin::Form group-->
                    <div class="form-group">
                        <div class="d-flex justify-content-between mt-n5">
                            <label class="font-size-h6 font-weight-bolder text-white pt-5">Password</label>
                            <a href="{{ admin_url('login/forgotPassword') }}"
                                class="text-white font-size-h6 font-weight-bolder text-hover-white pt-5">Forgot
                                Password ?</a>
                        </div>
                        <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="password" name="password"
                            autocomplete="off" placeholder="Enter Password" />
                    </div>
                    <!--end::Form group-->
                    <!--begin::Action-->
                    <div class="pb-lg-0 pb-5">
                        <button type="submit" id="kt_login_singin_form_submit_button"
                            style="border: 2px solid white;background:transparent;color:white;"
                            class="btn btn-white font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Log-In</button>
                    </div>
                    <!--end::Action-->
                </form>
                <!--end::Form-->
            </div>
        </div>
    </div>

@endsection

{{-- @section('scripts')
    <script src="{{ asset_url('libs/login-general.js', true) }}" type="text/javascript"></script>
@endsection --}}
