@extends('admin.layouts.auth')

@section('content')
    <div class="login-content flex-row-fluid d-flex flex-column p-10"
        style="background-image: url('{{ asset_url('media/bg/bg-2.jpg', 1) }}');">

        <!--begin::Wrapper-->
        <div class="d-flex flex-row-fluid flex-center">

            <div class="login-form">
                <!--begin::Form-->
                <form method="POST" action="{{ admin_url('user_info/pass_change_first_attempt') }}" id="#sub-form"
                    onsubmit="return mySubmitFunction(event)" enctype="multipart/form-data">
                    @csrf

                    <div>
                        <h3 class="font-weight-bolder text-white font-size-h2 font-size-h1-lg">Set a New Password</h3>
                        <p class="text-muted font-weight-bold text-white font-size-h4">Enter your old password</p>
                    </div>
                    <!--end::Title-->
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
                        <input class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" type="password"
                            placeholder="Enter old password" name="old_password" id="old_password" autocomplete="off" />
                    </div>

                    <div>
                        <p class="text-muted font-weight-bold text-white font-size-h4">Enter your new password</p>
                    </div>

                    <div class="form-group">
                        <input class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" type="password"
                            placeholder="Enter new password" name="new_password" id="new_password" autocomplete="off" />
                    </div>

                    {{-- confirm password field --}}

                    <div>
                        <p class="text-muted font-weight-bold text-white font-size-h4">Confirm Password</p>
                    </div>

                    <div class="form-group">
                        <input class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" type="password"
                            placeholder="Enter new password" name="confirm_password" id="confirm_password"
                            autocomplete="off" />
                    </div>


                    <!--end::Form group-->
                    <!--begin::Form group-->
                    <div class="form-group d-flex flex-wrap">
                        <button type="submit" id="kt_login_forgot_form_submit_button"
                            class="btn font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4 submit"
                            style="border: 2px solid white;background:transparent;color:white;">Submit</button>
                    </div>



                </form>
                <!--end::Form-->
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function mySubmitFunction(e) {
            var form = document.getElementById("#sub-form");
            e.preventDefault();
            old_pass = $('#old_password').val();
            new_pass = $('#new_password').val();
            conf_pass = $('#confirm_password').val();
            // console.log(conf_pass);
            if (old_pass && new_pass && conf_pass) {
                if (new_pass != conf_pass) {
                    Swal.fire("Wait!", "Your New & Confirmed Password Did Not Match", "error");
                } else {
                    form.submit();
                }
            }
        }
    </script>
@endsection
