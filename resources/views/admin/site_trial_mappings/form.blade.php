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
            <div class="col-lg-9">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Tenant') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="tenant_id" id="tenants_id" class="form-control m-select2" required>
                                    <option value="" selected disabled>Select Tenant</option>
                                    @foreach ($tenants as $tenant)
                                        @if (old('tenant_id'))
                                            <option {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}
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
                            <label for="site_id" class="col-3 col-form-label text-right required">{{ __('Site') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="site_id" id="site_id" class="form-control m-select2" required>
                                    <option value="" disabled selected>Select Site</option>
                                    <?php
                                    if ($row->site_id) {
                                        echo selectBox("SELECT id, site_name FROM sites WHERE id=$row->site_id", old('site_id', $row->site_id));
                                    }
                                    if (old('site_id')) {
                                        echo selectBox('SELECT id, site_name FROM sites WHERE id=' . old('site_id'), old('site_id', $row->site_id));
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        @php
                            $val = [];
                        @endphp
                        @foreach (json_decode($row->trial_id) as $capacity)
                            @php
                                $val[] = $capacity;
                            @endphp
                        @endforeach

                        <div class="form-group row">
                            <label for="trial_id" class="col-3 col-form-label text-right required">{{ __('Trial') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="trial_id[]" id="trial_id" data-placeholder="Select Trials"
                                    class="form-control m-select2" multiple required>
                                    <?php
                                    if ($row->trial_id) {
                                        echo selectBox('SELECT id, study_name FROM trials', old('trial_id', $val));
                                    }
                                    if (old('trial_id')) {
                                        echo selectBox('SELECT id, study_name FROM trials', old('trial_id', $row->trial_id));
                                    }
                                    ?>
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
            $('#tenants_id').on('change', function(e) {
                var tenant_id = $(this).val();
                $("#site_id").empty();
                $('#site_id').append(
                    '<option value="">Select Site</option>');
                $.ajax({
                    type: "get",
                    url: `{{ admin_url('getSitesByTenantId', true) }}/` + tenant_id,
                    success: function(res) {
                        if (res.length > 0) {
                            console.log(res);
                            $("#site_id").empty();
                            $('#site_id').append(
                                '<option value="">Select Site</option>');
                            $.each(res, function(index, stateObj) {
                                $('#site_id').append('<option value="' + stateObj
                                    .id + '">' + stateObj.site_name +
                                    '</option>');
                            })
                        } else {
                            $("#site_id").empty();
                            $('#site_id').append(
                                '<option value="">No Record Found</option>');
                        }
                    }
                });

                $.ajax({
                    type: "get",
                    url: `{{ admin_url('getTrialsByTenantId', true) }}/` + tenant_id,
                    success: function(res) {
                        if (res.length > 0) {
                            console.log(res);
                            $("#trial_id").empty();
                            $('#trial_id').append(
                                '<option value="">Select Trials</option>');
                            $.each(res, function(index, stateObj) {
                                $('#trial_id').append('<option value="' + stateObj
                                    .id + '">' + stateObj.study_name +
                                    '</option>');
                            })
                        } else {
                            $("#trial_id").empty();
                            $('#trial_id').append(
                                '<option value="">No Record Found</option>');
                        }
                    }
                });

            });

        });
    </script>

@endsection
