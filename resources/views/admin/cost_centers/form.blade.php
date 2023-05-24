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
                            <label for="module" class="col-3 col-form-label text-right required">Cost Center Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="name"
                                    value="{{ old('name', Crypto::decryptData($row->name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Cost Center Name">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Clinical Trial: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="clinical_trial_id" id="clinical_trial_id" class="form-control m-select2">
                                    @php
                                        $login_code = Auth::user()->userclient['login_code'];
                                    @endphp
                                    <option value="" disabled>Select Clinical Trial</option>
                                    <?php echo selectBox("SELECT id, study_name FROM trials WHERE tenant_code='$login_code'", old('clinical_trial_id', $row->clinical_trial_id)); ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Start Date:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="start_date" value="{{ old('start_date', $row->start_date) }}"
                                    class="form-control" type="date" placeholder="Enter Start Date">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">End Date:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="end_date" value="{{ old('end_date', $row->end_date) }}"
                                    class="form-control" type="date" placeholder="Enter End Date">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Description:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="description"
                                    value="{{ old('description', Crypto::decryptData($row->description, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Description">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Long Description:</label>
                            <div class="col-6">
                                <textarea name="long_description" placeholder="Enter Long Description"
                                    class="form-control"
                                    rows="3">{{ old('long_description', Crypto::decryptData($row->long_description, Crypto::getAwsEncryptionKey())) }}</textarea>
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

@endsection
