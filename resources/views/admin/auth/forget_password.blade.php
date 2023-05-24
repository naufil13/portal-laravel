@extends('admin.layouts.auth')

@section('content')

    <div class="login-content flex-row-fluid d-flex flex-column p-10"
        style="background-image: url('{{ asset_url('media/bg/bg-2.jpg', 1) }}');">

        <!--begin::Wrapper-->
        <div class="d-flex flex-row-fluid flex-center">

            <div class="login-form">
                <!--begin::Form-->
                <form method="POST" id="kt_login_singin_form" class="kt-form"
                    action="{{ admin_url('login/forgetPasswordSubmissions') }}">
                    @csrf

                    <div class="pb-5 pb-lg-15">
                        <h3 class="font-weight-bolder text-white font-size-h2 font-size-h1-lg">Forgotten Password ?</h3>
                        <p class="text-white font-weight-bold font-size-h4">Enter your Username to reset your password</p>
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
                        <input class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" type="text"
                            placeholder="Username" name="email" autocomplete="off" />
                    </div>
                    <!--end::Form group-->
                    <!--begin::Form group-->
                    <div class="form-group d-flex flex-wrap">
                        <button type="submit" id="kt_login_forgot_form_submit_button"
                            class="btn font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4"
                            style="border: 2px solid white;background:transparent;color:white;">Submit</button>
                        <a href="{{ URL::previous() }}" id="kt_login_forgot_cancel"
                            class="btn btn-bg-white font-weight-bolder font-size-h6 px-8 py-4 my-3">Cancel</a>
                    </div>



                </form>
                <!--end::Form-->
            </div>

        </div>
    </div>



@endsection
