@php
$pass_data['form_buttons'] = ['back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
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
                            <label for="module" class="col-3 col-form-label text-right required">Application Name<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="application_name"
                                    value="{{ old('application_name', $row->application_name) }}" class="form-control"
                                    type="text" placeholder="Enter Application Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Application URL:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="application_url" value="{{ old('application_url', $row->application_url) }}"
                                    class="form-control" type="text" placeholder="Enter Application URL">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Application
                                Description:<span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="application_description"
                                    value="{{ old('application_description', $row->application_description) }}"
                                    class="form-control" type="text" placeholder="Enter Application Description">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Application Code:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="application_code"
                                    value="{{ $row->application_code ? $row->application_code : Illuminate\Support\Str::random(6) }}"
                                    class="form-control" type="text" placeholder="Enter Application Code">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Status') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="application_status" id="application_status" class="form-control m-select2">
                                    <option value="">Select Application Status</option>
                                    <option {{ $row->application_status == 1 ? 'selected' : '' }} value="1">Active
                                    </option>
                                    <option {{ $row->application_status == 2 ? 'selected' : '' }} value="2">In-Active
                                    </option>
                                    <option {{ $row->application_status == 3 ? 'selected' : '' }} value="3">Depricated
                                    </option>
                                    <option {{ $row->application_status == 4 ? 'selected' : '' }} value="4">Un-Set
                                    </option>
                                    <option {{ $row->application_status == 5 ? 'selected' : '' }} value="5">Other
                                    </option>

                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="title" class="col-3 col-form-label text-right">GA Version:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="ga_version" value="{{ old('ga_version', $row->ga_version) }}"
                                    class="form-control" type="text" placeholder="GA Version" id="ga_version">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Tokenization Method') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="token_id" id="token_id" class="form-control m-select2">
                                    <option value="" disabled>Select Token Method</option>
                                    @foreach ($tokens as $token)
                                        <option {{ $row->token_id == $token->id ? 'selected' : '' }}
                                            value="{{ $token->id }}">
                                            {{ Crypto::decryptData($token->name, Crypto::getAwsEncryptionKey()) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Platform:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="platform" value="{{ old('platform', $row->platform) }}"
                                    class="form-control" type="text" placeholder="Enter Platform">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">GA Release Date:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="ga_release_date" value="{{ old('ga_release_date', $row->ga_release_date) }}"
                                    class="form-control" type="date" placeholder="Enter GA Release Date">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Technology Stack:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="technology_stack"
                                    value="{{ old('technology_stack', $row->technology_stack) }}" id="technology_stack" class="form-control"
                                    type="text" placeholder="Enter Technology Stack">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id"
                                class="col-3 col-form-label text-right required">{{ __('Allowed Roles') }}:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                @php
                                    $val = [];
                                @endphp
                                @foreach ($allw_roles as $allw_role)
                                    @php
                                        $val[] = $allw_role->allowed_role_id;
                                    @endphp
                                @endforeach
                                <select name="allowed_roles[]" data-placeholder="Select Allowed Roles"
                                    class="form-control m-select2" multiple="multiple" required>
                                    <?php echo selectBox('SELECT id, user_type FROM user_types', old('allowed_roles', $val)); ?>
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

            <!--======= begin::right sidebar -->
            <div class="col-lg-3">
                <div class="card card-custom">
                    <div class="card-header">
                        <h3 class="card-title"> Application Icon </h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group row mx-center">

                            <div class="image-input image-input-outline" id="kt_image_1">
                                <div class="image-input-wrapper"
                                    style="background-image: url({{ _img(asset_url('media/applications/' . $row->image, 1), 115, 115) }})">
                                </div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                    data-action="change" data-toggle="tooltip" title=""
                                    data-original-title="Change Module Image">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="image" accept=".png, .jpg, .jpeg">
                                    <input type="hidden" name="profile_avatar_remove" />
                                </label>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                    data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>




                            {{-- <div class="kt-avatar kt-avatar--outline kt-avatar--circle-" id="kt_apps_user_add_avatar fImg">
                                <a href="{{ asset_url('media/applications/' . $row->image, 1) }}" data-fancybox="image">
                                    <div class="kt-avatar__holder del-img"
                                        style="background-image: url({{ _img(asset_url('media/applications/' . $row->image, 1), 115, 115) }});">
                                    </div>
                                </a>
                                <label class="kt-avatar__upload" data-skin="dark" data-toggle="kt-tooltip"
                                    title="choose image">
                                    <i class="fa fa-pen"></i>
                                    <input type="file" name="image" accept=".png, .jpg, .jpeg">
                                </label>
                                <span class="kt-avatar__cancel" data-skin="dark" data-toggle="kt-tooltip"
                                    title="remove image" data-original-title="Cancel avatar">
                                    <i class="fa fa-times"></i>
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <!--======= end::right sidebar -->

        </div>
        <!-- end:: Content -->
    </form>

@endsection

{{-- Scripts --}}

@section('scripts')
<script>
    var input = document.getElementById('technology_stack');

// initialize Tagify on the above input node reference
new Tagify(input)
</script>
@endsection
