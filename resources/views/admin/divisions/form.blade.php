@php
$pass_data['form_buttons'] = ['back'];
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
            <div class="col-lg-12">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Division Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="division_name"
                                    value="{{ old('division_name', Crypto::decryptData($row->division_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Division Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Address:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="address"
                                    value="{{ old('address', Crypto::decryptData($row->address, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Country') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="country_id" id="country_id" class="form-control m-select2">
                                    <option value="" disabled selected>Select Country</option>
                                    <?php echo selectBox('SELECT id, name FROM countries', old('country_id', $row->country_id)); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('State') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="state_id" id="state_id" class="form-control m-select2">
                                    <option value="" disabled selected>Select State</option>
                                    <?php
                                    if ($row->state_id) {
                                        echo selectBox("SELECT id, name FROM states WHERE country_id=$row->country_id", old('state_id', $row->state_id));
                                    }
                                    if (old('state_id')) {
                                        echo selectBox('SELECT id, name FROM states WHERE country_id=' . old('country_id'), old('state_id', $row->state_id));
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right">{{ __('City') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="city_id" id="city_id" class="form-control m-select2">
                                    <option value="" disabled selected>Select City</option>
                                    <?php
                                    if ($row->city_id) {
                                        echo selectBox("SELECT id, name FROM cities WHERE state_id=$row->state_id", old('city_id', $row->city_id));
                                    }
                                    if (old('city_id')) {
                                        echo selectBox('SELECT id, name FROM cities WHERE state_id=' . old('state_id'), old('city_id', $row->city_id));
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Zip Code:</label>
                            <div class="col-6">
                                <input name="zip_code"
                                    value="{{ old('zip_code', Crypto::decryptData($row->zip_code, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Zip Code">
                                    @error('zip_code')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required"> Phone: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="phone"
                                    value="{{ old('phone', Crypto::decryptData($row->phone, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Phone">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Contact Name:</label>
                            <div class="col-6">
                                <input name="contact_name"
                                    value="{{ old('contact_name', Crypto::decryptData($row->contact_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Contact Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Contact Email:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="contact_email"
                                    value="{{ old('contact_email', Crypto::decryptData($row->contact_email, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Contact Email">
                                    @error('contact_email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Contact Phone:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="contact_phone"
                                    value="{{ old('contact_phone', Crypto::decryptData($row->contact_phone, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Contact Phone">
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
                $("#state_id").empty();
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
