@php
$pass_data['form_buttons'] = [' '];
@endphp
@extends('admin.layouts.admin')

@section('content')
{{-- Content --}}

<form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="#sub-form" onsubmit="return mySubmitFunction(event)">
    @csrf
    @include('admin.layouts.inc.stickybar', $pass_data)
    <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
    <!-- begin:: Content -->
    <div class="row mt-10">
        <div class="col-lg-12">
            <div class="card-custom card">
                <div class="card-header">
                    <h3 class="card-title"> Personal Information </h3>
                </div>
                <div class="card-body">


                    <div class="form-group row">
                        <label for="module" class="col-2 col-form-label text-right required">First Name: </label>
                        <div class="col-4">
                            <input name="first_name" value="{{ old('first_name', Crypto::decryptData(Auth::user()->first_name, Crypto::getAwsEncryptionKey())) }}" class="form-control" type="text">
                        </div>

                        <label for="module" class="col-2 col-form-label text-right required">Last Name: </label>
                        <div class="col-4">
                            <input name="last_name" value="{{ old('last_name', Crypto::decryptData(Auth::user()->last_name, Crypto::getAwsEncryptionKey())) }}" class="form-control" type="text">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="module" class="col-2 col-form-label text-right required">Email: </label>
                        <div class="col-4">
                            <label class="form-control">{{ old('email', Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey())) }}</label>
                        </div>
                        <label for="module" class="col-2 col-form-label text-right required">Tenant:</label>
                        <div class="col-4">
                            <label class="form-control">{{ old('tenant', $tenant) }}</label>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="module" class="col-2 col-form-label text-right required">User type:</label>
                        <div class="col-4">
                            <label class="form-control">{{ old('user_type', Auth::user()['usertype']->user_type) }}</label>
                        </div>
                        <label for="module" class="col-2 col-form-label text-right required">Username:</label>
                        <div class="col-4">
                            <label class="form-control">{{ old('username', Auth::user()->username) }}</label>
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="module" class="col-2 col-form-label text-right required">Two Factor Authentication:</label>
                        <div class="col-4">
                            <span class="switch switch-outline switch-icon switch-primary">
                                <label class="check">
                                    <input type="checkbox" name="two_factor"  {{ Auth::user()->two_factor_auth == 1 ? 'checked':'' }}>
                                    <span></span>
                                </label>
                            </span>
                        </div>

                    </div>

                </div>
            </div>
            <div class="card-custom card mt-10">
                <div class="card-header">
                    <h3 class="card-title"> {{ __('Portal Credential') }} </h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label for="password" class="col-2 col-form-label required">{{ __('Old Password') }}:</label>
                        <div class="col-6">
                            <input type="password" name="old_password" id="old_password" class="form-control" autocomplete="off" />
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-2 col-form-label required">{{ __('New Password') }}:</label>
                        <div class="col-6">
                            <input type="password" name="new_password" id="new_password" class="form-control" />
                            <p>Password must be a minimum of 8 and maximum of 30 characters with atleast one numric and special character</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password" class="col-2 col-form-label required">{{ __('Confirm Password') }}:</label>
                        <div class="col-6">
                            <input type="password" name="conf_password" id="conf_password" class="form-control" />
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-md btn-primary btn-sm submit">
                            <i class="la la-save"></i>Submit Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end:: Content -->
</form>
@endsection

{{-- Scripts --}}

@section('scripts')
<script>
    function mySubmitFunction(e) {

        e.preventDefault();

        old_pass = $('#old_password').val();
        new_pass = $('#new_password').val();
        conf_pass = $('#conf_password').val();

        if (old_pass && new_pass && conf_pass) {


            $.ajax({
                type: "get",
                url: `{{ admin_url('check_password', true) }}/` + old_pass,
                data: {
                    'id': <?php echo Auth::User()->id ?>
                },
                success: function(res) {
                    if (res == "False") {
                        Swal.fire("Wait!", "Your Old Password Is Wrong", "error");
                    } else if (new_pass != conf_pass) {
                        Swal.fire("Wait!", "Your New & Confirmed Password Did Not Match", "error");
                    } else {

                        var form = document.getElementById("#sub-form");

                        $('.submit').click(function(e) {
                            form.submit();
                        });
                    }
                }
            });
        } else {
            var form = document.getElementById("#sub-form");

            $('.submit').click(function(e) {
                form.submit();
            });
        }
    }
</script>
@endsection
