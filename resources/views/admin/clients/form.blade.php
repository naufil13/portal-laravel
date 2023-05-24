@php
$pass_data['form_buttons'] = ['save', 'back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('admin.layouts.inc.stickybar', $pass_data)
        <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
        <!-- begin:: Content -->
        <div class="row mt-10">
            <div class="col-lg-9">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Tenant Name:
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-6">
                                <input name="client_name"
                                    value="{{ old('client_name', $row->client_name ? Crypto::decryptData($row->client_name, Crypto::getAwsEncryptionKey()) : '') }}"
                                    class="form-control" type="text" placeholder="Enter Tenant Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Login Code:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="login_code" value="{{ old('login_code', $row->login_code) }}"
                                    class="form-control" type="text" placeholder="Enter Login Code">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Contact Person
                                Name:<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="contact_person_name"
                                    value="{{ old('contact_person_name', $row->contact_person_name) }}"
                                    class="form-control" type="text" placeholder="Enter Contact Person Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Tenant Phone:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="client_phone"
                                    value="{{ old('client_phone', $row->client_phone ? Crypto::decryptData($row->client_phone, Crypto::getAwsEncryptionKey()) : '') }}"
                                    class="form-control" type="text" placeholder="Enter Tenant Phone">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Email:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="client_email" value="{{ old('client_email', $row->client_email) }}"
                                    class="form-control" type="text" placeholder="Enter Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Address:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="client_address" value="{{ old('client_address', $row->client_address) }}"
                                    class="form-control" type="text" placeholder="Enter Client Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Country') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="country" id="country_id" class="form-control m-select2">
                                    <option value="">Select Country</option>
                                    <?php echo selectBox('SELECT id, name FROM countries', old('country', $row->country)); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('State') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="state" id="state_id" class="form-control m-select2">
                                    <option value="">Select State</option>
                                    <?php
                                    if ($row->state) {
                                        echo selectBox("SELECT id, name FROM states WHERE country_id=$row->country", old('state', $row->state));
                                    }
                                    if (old('state')) {
                                        echo selectBox('SELECT id, name FROM states WHERE country_id=' . old('country'), old('state', $row->state));
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('City') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="city" id="city_id" class="form-control m-select2">
                                    <option value="">Select City</option>
                                    <?php
                                    if ($row->city) {
                                        echo selectBox("SELECT id, name FROM cities WHERE state_id=$row->state", old('city', $row->city));
                                    }
                                    if (old('city')) {
                                        echo selectBox('SELECT id, name FROM cities WHERE state_id=' . old('state'), old('city', $row->city));
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Zip Code:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="zip_code" value="{{ old('zip_code', $row->zip_code) }}"
                                    class="form-control" type="text" placeholder="Zip Code" id="zip_code">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-xl-3 col-lg-3 col-form-label text-right">Tenant Logo:<span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-9 col-xl-6">
                                <div class="image-input image-input-outline" id="kt_image_1">
                                    <div class="image-input-wrapper"
                                        style="background-image: url({{ _img(asset_url('media/tenant_logo/' . $row->tenant_logo, 1), 115, 115) }})">
                                    </div>
                                    <label
                                        class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                        data-action="change" data-toggle="tooltip" title=""
                                        data-original-title="Change logo">
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        <input type="file" name="tenant_logo" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="profile_avatar_remove" />
                                    </label>
                                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                        data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                    </span>
                                </div>
                                <span class="form-text text-muted">Allowed file types: png, jpg, jpeg.</span>
                            </div>
                        </div>

                        <div class="form-group row">

                            @php
                                $val = [];
                            @endphp
                            @foreach ($up_applications as $capacity)
                                @php
                                    $val[] = $capacity->application_id;
                                @endphp
                            @endforeach

                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Applications') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="application_id[]" id="application_id" class="form-control m-select2"
                                    multiple="multiple">
                                    <option value="" disabled>Select Application</option>
                                    <?php echo selectBox('SELECT id, application_name FROM applications', old('application_id', $val)); ?>
                                </select>
                            </div>

                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-md btn-primary btn-sm">
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

    <script type="text/javascript">
        $(document).ready(function($) {

            $('#country_id').on('change', function(e) {
                var company_id = $(this).val();
                $("#city_id").empty();
                $('#city_id').append(
                    '<option value="">Select City</option>');
                $.ajax({
                    type: "get",
                    url: `{{ admin_url('getStateByCountry', true) }}/` + company_id,
                    success: function(res) {
                        if (res.length > 0) {
                            console.log(res);
                            $("#state_id").empty();
                            $('#state_id').append(
                                '<option value="">Select State</option>');
                            $.each(res, function(index, stateObj) {
                                $('#state_id').append('<option value="' + stateObj
                                    .id + '">' + stateObj.name +
                                    '</option>');
                            })
                        } else {
                            $("#state_id").empty();
                            $('#state_id').append(
                                '<option value="">No Record Found</option>');
                        }
                    }
                });
            });

            $('#state_id').on('change', function(e) {
                var state_id = $(this).val();
                $.ajax({
                    type: "get",
                    url: `{{ admin_url('getCityByState', true) }}/` + state_id,
                    success: function(res) {
                        if (res.length > 0) {
                            console.log(res);
                            $("#city_id").empty();
                            $('#city_id').append(
                                '<option value="">Select City</option>');
                            $.each(res, function(index, stateObj) {
                                $('#city_id').append('<option value="' + stateObj
                                    .id + '">' + stateObj.name +
                                    '</option>');
                            })
                        } else {
                            $("#city_id").empty();
                            $('#city_id').append(
                                '<option value="">No Record Found</option>');
                        }
                    }
                });
            });

        });
    </script>


@endsection
