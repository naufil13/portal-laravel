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
                        <h3 class="card-title"> {{ $_info->title }} Site Contact Details </h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Tenant') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="tenants_id" id="tenants_id" class="form-control m-select2">
                                    <option value="" selected disabled>Select Tenant</option>
                                    @foreach ($tenants as $tenant)
                                        @if (old('tenants_id'))
                                            <option {{ old('tenants_id') == $tenant->id ? 'selected' : '' }}
                                                value="{{ $tenant->id }}">
                                                {{ Crypto::decryptData($tenant->tenant_name, Crypto::getAwsEncryptionKey()) }}
                                            </option>
                                        @else
                                            <option {{ $row->tenant_id == $tenant->id ? 'selected' : '' }}
                                                value="{{ $tenant->id }}">
                                                {{ Crypto::decryptData($tenant->tenant_name, Crypto::getAwsEncryptionKey()) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Site-Code: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="site_code"
                                    value="{{ old('site_code', Crypto::decryptData($row->site_code, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Site Code">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Site Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="site_name"
                                    value="{{ old('site_name', Crypto::decryptData($row->site_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Site Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Site Contact Person
                                Name: <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="site_contact_person_name"
                                    value="{{ old('site_contact_person_name', Crypto::decryptData($row->site_contact_person_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Site Contact Person Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Street Address: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="street_address"
                                    value="{{ old('street_address', Crypto::decryptData($row->street_address, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Street Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Site Type') }}: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="site_type_id" id="site_type_id" class="form-control m-select2">
                                    <option value="">Select Site Type</option>
                                    <?php echo selectBox("SELECT id, type FROM site_types WHERE tenant_code='" . Auth::user()->userclient['login_code'] . "'", old('site_type_id', $row->site_type_id)); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Country') }}: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="country" id="country_id" class="form-control m-select2">
                                    <option value="" disabled selected>Select Country</option>
                                    <?php echo selectBox('SELECT id, name FROM countries', old('country', $row->country)); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('State') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="state_id" id="state_id" class="form-control m-select2">
                                    <option value="" disabled selected>Select State</option>
                                    <?php
                                    if ($row->state_id) {
                                        echo selectBox("SELECT id, name FROM states WHERE country_id=$row->country", old('state_id', $row->state_id));
                                    }
                                    if (old('state_id')) {
                                        echo selectBox('SELECT id, name FROM states WHERE country_id=' . old('country'), old('state_id', $row->state_id));
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('City') }}:
                                <span class="text-danger">*</span></label>
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
                            <label for="title" class="col-3 col-form-label text-right">Zip Code:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="zip_code"
                                    value="{{ old('zip_code', Crypto::decryptData($row->zip_code, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Zip Code" id="zip_code">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Latitude:</label>
                            <div class="col-6">
                                <input name="latitude"
                                    value="{{ old('latitude', Crypto::decryptData($row->latitude, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Latitude">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Longitude:</label>
                            <div class="col-6">
                                <input name="longitude"
                                    value="{{ old('longitude', Crypto::decryptData($row->longitude, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Longitude">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Email:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="email"
                                    value="{{ old('email', Crypto::decryptData($row->email, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Email Address">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Phone Number:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="phone_number"
                                    value="{{ old('phone_number', Crypto::decryptData($row->phone_number, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Phone Number">
                            </div>
                        </div>

                    </div>
                </div>

                {{-- KPI Detail --}}
                <div class="card-custom card mt-10">
                    <div class="card-header">
                        <h3 class="card-title">Site KPI Details </h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Service Level Benchmark:</label>
                            <div class="col-6">
                                <input name="service_level_benchmark"
                                    value="{{ old('service_level_benchmark', Crypto::decryptData($row->service_level_benchmark, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Service Level Benchmark">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Wait Time Benchmark:</label>
                            <div class="col-6">
                                <input name="wait_time_benchmark"
                                    value="{{ old('wait_time_benchmark', Crypto::decryptData($row->wait_time_benchmark, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Wait Time Benchmark">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">Procedure Time Benchmark:</label>
                            <div class="col-6">
                                <input name="proceedure_time_benchmark"
                                    value="{{ old('proceedure_time_benchmark', Crypto::decryptData($row->proceedure_time_benchmark, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Procedure Time Benchmark">
                            </div>
                        </div>

                    </div>
                </div>
                {{-- KPI Detail End --}}

                {{-- Test --}}
                <div class="card-custom card mt-10">
                    <div class="card-header">
                        <h3 class="card-title">Site Working Hour Date & Time </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Timezone') }}: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="timezone_id" id="timezone_id" class="form-control m-select2">
                                    <option value="" disabled>Select Timezone</option>
                                    <?php echo selectBox('SELECT id, name FROM timezones', old('timezone_id', $row->timezone_id)); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">

                            @php
                                $val = [];
                            @endphp
                            @foreach (json_decode($row->holidays_id) as $capacity)
                                @php
                                    $val[] = $capacity;
                                @endphp
                            @endforeach

                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Holidays') }}:</label>
                            <div class="col-6">
                                <select name="holidays_id[]" id="holidays_id" class="form-control m-select2"
                                    multiple="multiple" data-placeholder="Select Holiday(s)">
                                    <option value="" disabled>Select Holidays</option>
                                    <?php echo selectBox("SELECT id, holiday_description FROM holiday_generals WHERE tenant_code='" . Auth::user()->userclient['login_code'] . "'", old('holidays_id', $val)); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">

                            <table class="table table-bordered table-head-bg table-sm">
                                <thead>
                                    <tr>
                                        <th scope="col">Days</th>
                                        <th scope="col">Working Days</th>
                                        <th scope="col">Office Hours (From)</th>
                                        <th scope="col">Office Hours (From)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($weekdays as $weekday)
                                        <tr>
                                            <td>{{ $weekday->day }}</td>
                                            <td>
                                                <span class="switch switch-lg switch-icon">
                                                    <label>
                                                        <input id="check_{{ $weekday->day }}" class="onChecks"
                                                            type="checkbox" name="working_days[]"
                                                            value="{{ $weekday->day }}">
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="form-group mb-0 row">
                                                    <div class="col-6">
                                                        <input class="form-control"
                                                            id="office_hour_from_{{ $weekday->day }}"
                                                            name="office_hours_from[]" disabled="disabled" type="time"
                                                            value="{{ old('office_hours_from', $row->office_hours_from) }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="col-6">
                                                    <input class="form-control"
                                                        id="office_hour_to_{{ $weekday->day }}" name="office_hours_to[]"
                                                        type="time" disabled="disabled"
                                                        value="{{ old('office_hours_to', $row->office_hours_to) }}">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
                {{-- Test --}}


                {{-- Collection Capacity Detail --}}
                <div class="card-custom card mt-10">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Site Collection Capacity </h3>
                    </div>
                    <div class="card-body">

                        @foreach ($sample_types as $sample_type)
                            <div class="form-group row">
                                <label for="title"
                                    class="col-3 col-form-label text-right">{{ $sample_type->name }}:</label>
                                <div class="col-6">

                                    @php
                                        $val = '';
                                    @endphp

                                    @foreach (json_decode($row->sample_type_collection_capacity) as $capacity)

                                        @foreach ($capacity as $key => $item)
                                            @if (strtolower(str_replace(' ', '_', $sample_type->name)) == $key)
                                                @php
                                                    $val = $item;
                                                @endphp
                                            @endif
                                        @endforeach

                                    @endforeach

                                    <input name="{{ strtolower(str_replace(' ', '_', $sample_type->name)) }}"
                                        value="{{ old('sample_type_collection_capacity', $val) }}"
                                        class="form-control" type="text" placeholder="Enter Collection Capacity">
                                </div>
                            </div>
                        @endforeach

                        <input type="hidden" id="working_days" value="{{ $row->working_days }}">
                        <input type="hidden" id="time_to" value="{{ $row->office_hours_to }}">
                        <input type="hidden" id="time_from" value="{{ $row->office_hours_from }}">


                        <div class="btn-group">
                            <button type="submit" class="btn btn-md btn-primary btn-sm">
                                <i class="la la-save"></i>Submit Now
                            </button>
                        </div>
                    </div>
                </div>
                {{-- Collection Capacity Detail --}}
            </div>
        </div>
        <!-- end:: Content -->
    </form>
    <style>
        .table-sm th,
        .table-sm td {
            padding: 0.7rem;
        }

    </style>
@endsection

{{-- Scripts --}}

@section('scripts')

    <script type="text/javascript">
        $(document).on('change', '.onChecks', function() {
            if (this.checked) {
                $('#office_hour_from_' + $(this).val()).removeAttr('disabled');
                $('#office_hour_to_' + $(this).val()).removeAttr('disabled');
            } else {
                $('#office_hour_from_' + $(this).val()).val('');
                $('#office_hour_to_' + $(this).val()).val('');
                $('#office_hour_from_' + $(this).val()).attr('disabled', 'disabled');
                $('#office_hour_to_' + $(this).val()).attr('disabled', 'disabled');
            }
        });


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

        $(document).ready(function($) {
            var submittedWeekdays = $('#working_days').attr("value");
            var submittedTimeFrom = $('#time_from').attr("value");
            var submittedTimeTo = $('#time_to').attr("value");

            // console.log(JSON.parse(submittedWeekdays));
            var arrWeekday = JSON.parse(submittedWeekdays);
            var arrTimeFrom = JSON.parse(submittedTimeFrom);
            var arrTimeTo = JSON.parse(submittedTimeTo);
            console.log(arrTimeFrom);
            arrWeekday.forEach(function(element, index) {
                // console.log(index);
                $('#check_' + element).attr('checked', true);
                $('#office_hour_from_' + element).val(arrTimeFrom[index]);
                $('#office_hour_to_' + element).val(arrTimeTo[index]);

                $('#office_hour_from_' + element).removeAttr('disabled');
                $('#office_hour_to_' + element).removeAttr('disabled');
            });




        });
    </script>

@endsection
