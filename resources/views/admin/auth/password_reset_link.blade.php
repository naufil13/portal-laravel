@extends('admin.layouts.auth')

@section('content')

    <div class="login-content flex-row-fluid d-flex flex-column p-10"
        style="background-image: url('{{ asset_url('media/bg/bg-2.jpg', 1) }}');">

        <div class="d-flex flex-row-fluid flex-center">

            <div class="login-form">
                <!--begin::Form-->
                    <form method="POST" id="reset_form" class="kt-form"
                        action="{{ admin_url('login/resetPassword') }}">
                        @csrf
                        <input type="hidden" value="{{ $token }}" name="reset_token">
                        <input type="hidden" value="{{ $email }}" name="email">
                        <div class="pb-5 pb-lg-15">
                            <h3 class="font-weight-bolder text-white font-size-h2 font-size-h1-lg">Reset Your Password</h3>
                            <p class="text-muted font-weight-bold font-size-h4">Please Reset Your Password</p>
                        </div>
                        @if(!$email)
                            <div class="alert alert-danger">
                                <p>This link has been expired kindly use the new link sent on your email</p>
                            </div>
                        @endif
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
                            <label class="font-size-h6 font-weight-bolder text-white">Email</label>
                            <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="text"
                                autocomplete="off" placeholder="Enter Username" value="{{$email}}" disabled="disabled" />
                        </div>
                        <!--end::Title-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-white">Password</label>
                            <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="password" name="password"
                                autocomplete="off" id="pass" placeholder="Enter Password" @if(!$email) disabled="disabled" @endif />
                        </div>

                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-white">Confirm Password</label>
                            <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="password"
                                autocomplete="off" id="confirm_pass" placeholder="Enter Password" @if(!$email) disabled="disabled" @endif />
                            <div class="alert error-msg alert-danger mt-5 d-none" role="alert">
                                password & confirm password don't match!
                            </div>

                        </div>
                        <!--end::Form group-->
                        <!--begin::Form group-->
                        <div class="form-group d-flex flex-wrap">
                            <button type="submit" id="kt_login_forgot_form_submit_button"
                                class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4" @if(!$email) disabled="disabled" @endif>Submit</button>
                            <a href="{{ URL::previous() }}" id="kt_login_forgot_cancel"
                                class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3">Cancel</a>
                        </div>



                    </form>
                <!--end::Form-->
            </div>

        </div>

    </div>



@endsection

@section('scripts')
    <script type="text/javascript">
        $('#reset_form').submit(function(e) {
            e.preventDefault();
            var password = $("#pass").val();
            var confirmPassword = $("#confirm_pass").val();
            if (password != confirmPassword) {
                $(".error-msg").removeClass("d-none")
                $(".error-msg").addClass("d-block");;
            }else{
                $(".error-msg").addClass("d-none")
                $(".error-msg").removeClass("d-block");
                this.submit();
            }
        })
    </script>
@endsection
