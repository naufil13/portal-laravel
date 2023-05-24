@php
$pass_data['form_buttons'] = ['back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}
    <form action="{{ admin_url('store', true) }}" id="trial-form" method="post" enctype="multipart/form-data">
        @csrf
        @include('admin.layouts.inc.stickybar', $pass_data)
        <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
        <input type="hidden" name="clinical_trial_number"
            value="{{ $row->clinical_trial_number ? $row->clinical_trial_number : Illuminate\Support\Str::random(6) }}">
        <!-- begin:: Content -->
        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
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
                                            <option {{ $row->tenants_id == $tenant->id ? 'selected' : '' }}
                                                value="{{ $tenant->id }}">
                                                {{ Crypto::decryptData($tenant->tenant_name, Crypto::getAwsEncryptionKey()) }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Clinical Trial
                                Name: <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="study_name"
                                    value="{{ old('study_name', Crypto::decryptData($row->study_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Clinical Trial Name">
                            </div>
                        </div>

                        <div class="form-group row">

                            @php
                                $val = [];
                            @endphp
                            @foreach ($trial_sites as $trial_site)
                                @php
                                    $val[] = $trial_site->site_id;
                                @endphp
                            @endforeach

                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Sites') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="sites_id[]" data-placeholder="Select Site" id="sites_id"
                                    class="form-control m-select2" multiple>
                                    <option value="" disabled>Select Sites</option>
                                    @if ($row->id)
                                        <?php echo selectBox('SELECT id, site_name FROM sites', old('sites_id', $val)); ?>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">

                            @php
                                $val = [];
                            @endphp
                            @foreach ($trial_samples as $trial_sample)
                                @php
                                    $val[] = $trial_sample->sample_types_id;
                                @endphp
                            @endforeach

                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Sample Type') }}: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="sample_types[]" data-placeholder="Select Sample Type(s)" id="sample_types"
                                    class="form-control m-select2" multiple="multiple">
                                    <option value="" disabled>Select Sample Type</option>
                                    <?php echo selectBox("SELECT id, name FROM sample_types WHERE tenant_code='" . Auth::user()->userclient['login_code'] . "'", old('sample_types', $val)); ?>
                                </select>
                            </div>

                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Start Date: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="start_date" value="{{ old('start_date', $row->start_date) }}"
                                    class="form-control start-date" type="date" placeholder="Enter Start Date">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">End Date: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="end_date" value="{{ old('end_date', $row->end_date) }}"
                                    class="form-control end-date" type="date" placeholder="Enter End Date">
                                <small class="text-danger date-error-msg d-none">End date should be greater than start
                                    Date!</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Principal Investigator
                                Name: <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="principal_investigator_name"
                                    value="{{ old('principal_investigator_name', Crypto::decryptData($row->principal_investigator_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Principal Investigator Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Principal Investigator
                                Contact: <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="principal_investigator_contact"
                                    value="{{ old('principal_investigator_contact', Crypto::decryptData($row->principal_investigator_contact, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Principal Investigator Contact">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Principal Investigator
                                Email: <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="principal_investigator_email"
                                    value="{{ old('principal_investigator_email', Crypto::decryptData($row->principal_investigator_email, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Principal Investigator Email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-lg-right">Upload Protocol Document:</label>
                            <div class="col-lg-6">
                                <div class="uppy" id="kt_uppy_5">
                                    <div class="uppy-wrapper">
                                        <div class="uppy-Root uppy-FileInput-container">
                                            <input class="uppy-FileInput-input uppy-input-control" type="file"
                                                name="clinical_protocol_document" multiple="" accept="application/pdf"
                                                id="kt_uppy_5_input_control" style="">
                                            <label class="uppy-input-label btn btn-light-primary btn-sm btn-bold"
                                                for="kt_uppy_5_input_control">Attach Protocol Document</label>
                                        </div>
                                    </div>
                                    <div class="uppy-list"></div>
                                    <div class="uppy-status">
                                        <div class="uppy-Root uppy-StatusBar is-waiting" aria-hidden="true"
                                            dir="ltr">
                                            <div class="uppy-StatusBar-progress" role="progressbar" aria-valuemin="0"
                                                aria-valuemax="100" aria-valuenow="0" style="width: 0%;"></div>
                                            <div class="uppy-StatusBar-actions"></div>
                                        </div>
                                    </div>
                                    <div class="uppy-informer uppy-informer-min">
                                        <div class="uppy uppy-Informer" aria-hidden="true">
                                            <p role="alert"> </p>
                                        </div>
                                    </div>
                                </div>
                                <span class="form-text text-muted">Only .pdf file is allowed</span>
                            </div>
                        </div>

                        <div class="form-group row">

                            @php
                                $val = [];
                            @endphp
                            @foreach ($tokens as $token)
                                @php
                                    $val[] = $token->token_id;
                                @endphp
                            @endforeach

                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Payment Gateways') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="tokens_id[]" data-placeholder="Select Payment Gateway(s)" id="tokens_id"
                                    class="form-control m-select2" multiple>
                                    <option value="" disabled>Select Payment Gateways</option>
                                    <?php echo selectBox('SELECT id, name FROM tokens where status = "Active" AND tenant_code="' . Auth::user()->userclient['login_code'] . '"', old('tokens_id', $val)); ?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('ICF') }}:
                                <span class="text-danger">*</span></label>
                            @php
                                $icf = Crypto::decryptData($row->icf, Crypto::getAwsEncryptionKey());
                            @endphp
                            <div class="col-6">
                                <select name="icf" class="form-control">
                                    <option value="Yes" @if ($icf === 'Yes') selected @endif>Yes</option>
                                    <option value="No" @if ($icf === 'No') selected @endif>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Age Verification    ') }}:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                @php
                                    $age_verification = Crypto::decryptData($row->age_verification, Crypto::getAwsEncryptionKey());
                                @endphp
                                <select name="age_verification" class="form-control age_verification">
                                    <option value="Yes" @if ($age_verification === 'Yes') selected @endif>Yes</option>
                                    <option value="No" @if ($age_verification === 'No') selected @endif>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row participant_age_requirement">
                            <label for="module" class="col-3 col-form-label text-right">Participant's Min. Age: <span
                                    class="text-danger">*</span></label>
                            <div class="col-2">
                                <select name="participant_age_requirement" data-placeholder="Month/Year/Day"
                                    id="participant_age_requirement" class="form-control m-select2">
                                    <option value="Year" <?php echo $row->participant_age_requirement == 'Year' ? 'selected' : ''; ?>>Year</option>
                                    <option value="Month" <?php echo $row->participant_age_requirement == 'Month' ? 'selected' : ''; ?>>Month</option>
                                    <option value="Day" <?php echo $row->participant_age_requirement == 'Day' ? 'selected' : ''; ?>>Day</option>
                                </select>
                                <span class="form-text text-muted">Min Age</span>
                            </div>
                            <div class="col-2">
                                <select name="participant_age_operator" data-placeholder="Operator"
                                    id="participant_age_operator" class="form-control m-select2">
                                    <option value="above" <?php echo $row->participant_age_operator == 'above' ? 'selected' : ''; ?>>Above</option>
                                    <option value="below" <?php echo $row->participant_age_operator == 'below' ? 'selected' : ''; ?>>Below</option>
                                    <option value="equal" <?php echo $row->participant_age_operator == 'equal' ? 'selected' : ''; ?>>Equal</option>
                                </select>
                                <span class="form-text text-muted">Operator</span>
                            </div>
                            <div class="col-2">
                                <input class="form-control" name="participant_min_age" type="number"
                                    id="participant_min_age" min="0" max="70"
                                    oninput="this.value =
                                    !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null"
                                    value="{{ old('participant_min_age', $row->participant_min_age) }}"
                                    placeholder="# of Months/Year/Age">
                                <span class="form-text text-muted"># of Months/Year/Age</span>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right required">{{ __('Profile') }}:
                                <span class="text-danger">*</span></label>
                            @php
                                $profile = Crypto::decryptData($row->profile, Crypto::getAwsEncryptionKey());
                            @endphp
                            <div class="col-6">
                                <select name="profile" class="form-control">
                                    <option value="Yes" @if ($profile === 'Yes') selected @endif>Yes</option>
                                    <option value="No" @if ($profile === 'No') selected @endif>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right required">{{ __('Survey Documents') }}:
                                <span class="text-danger">*</span></label>

                            @php
                                $survey_documents = Crypto::decryptData($row->survey_documents, Crypto::getAwsEncryptionKey());
                            @endphp

                            <div class="col-6">
                                <select name="survey_documents" class="form-control">
                                    <option value="Yes" @if ($survey_documents === 'Yes') selected @endif>Yes</option>
                                    <option value="No" @if ($survey_documents === 'No') selected @endif>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label text-right required">{{ __('Bill Of Rights') }}:
                                <span class="text-danger">*</span></label>

                            @php
                                $bill_of_rights = Crypto::decryptData($row->bill_of_rights, Crypto::getAwsEncryptionKey());
                            @endphp

                            <div class="col-6">
                                <select name="bill_of_rights" class="form-control">
                                    <option value="Yes" @if ($bill_of_rights === 'Yes') selected @endif>Yes</option>
                                    <option value="No" @if ($bill_of_rights === 'No') selected @endif>No</option>
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
        $('#tenants_id').on('change', function(e) {
            var tenant_id = $(this).val();
            $.ajax({
                type: "get",
                url: `{{ admin_url('getSitesByTenantsId', true) }}/` + tenant_id,
                success: function(res) {
                    if (res.length > 0) {
                        console.log(res);
                        $("#sites_id").empty();
                        $('#sites_id').append(
                            '<option value="" disabled>Select Sites</option>');
                        $.each(res, function(index, stateObj) {
                            $('#sites_id').append('<option value="' + stateObj
                                .id + '">' + stateObj.site_name +
                                '</option>');
                        })
                    } else {
                        $("#sites_id").empty();
                        $('#sites_id').append(
                            '<option value="">No Record Found</option>');
                    }
                }
            });
        });

        let formHasError = false;
        $('.end-date').on('change', function(e) {
            const startDate = $(".start-date").val();

            const endDate = $(".end-date").val();

            if (!startDate) return;

            if (new Date(startDate) > new Date(endDate)) {
                this.classList.add("is-invalid")
                $(".date-error-msg").addClass("d-block")
                formHasError = true
            } else {
                this.classList.remove("is-invalid")
                $(".date-error-msg").removeClass("d-block")
                formHasError = false
            }

        });

        $(".start-date").on('change', function(e) {
            const startDate = e.target.value
            const endDate = $(".end-date").val();

            if (!endDate) return;

            if (new Date(startDate) > new Date(endDate)) {
                $(".end-date").addClass("is-invalid")
                $(".date-error-msg").addClass("d-block")
                formHasError = true
            } else {
                $(".end-date").removeClass("is-invalid")
                $(".date-error-msg").removeClass("d-block")
                formHasError = false
            }
        })


        $("#trial-form").submit(function(e) {
            if (formHasError) {
                e.preventDefault();
                Swal.fire("Please clear the error", "", "error");
            } else {
                this.submit();
            }
        })

        $(".age_verification").change(toggle_age_verification)

        function toggle_age_verification(e) {
            var val = $('.age_verification').val();

            console.log(val);
            if (val === 'Yes') {
                $('.participant_age_requirement').show()
            } else {
                $('.participant_age_requirement').hide()
            }
        }

        toggle_age_verification()
    </script>
@endsection
