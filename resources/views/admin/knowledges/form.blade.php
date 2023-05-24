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
                            <label for="module" class="col-3 col-form-label text-right required">Document Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="document_name"
                                    value="{{ old('document_name', Crypto::decryptData($row->document_name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Document Name" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Version: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="version"
                                    value="{{ old('version', Crypto::decryptData($row->version, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Version" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Published Date: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="published_date"
                                    value="{{ old('published_date', Crypto::decryptData($row->published_date, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="date" placeholder="Enter Published Date" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right">Publised By:</label>
                            <div class="col-6">
                                <input name="published_by" value="{{ old('published_by', Crypto::decryptData($row->published_by, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text"
                                    placeholder="{{ Crypto::decryptData(Auth::user()->first_name, Crypto::getAwsEncryptionKey()) . ', ' . Crypto::decryptData(Auth::user()->last_name, Crypto::getAwsEncryptionKey()) }}"
                                    disabled>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="application_name" class="col-3 col-form-label text-right required">Application
                                Name: <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <select name="application_name" class="form-control m-select2" required>
                                    <option value="">Select Application Name</option>
                                    <?php echo selectBox('SELECT id, application_name FROM applications', $row->application_name); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">Document (.PDF
                                allowed Only):</label>
                            <div class="col-6">
                                <input name="filename" class="form-control" type="file" accept="application/pdf" required>
                            </div>
                        </div>

                        @if ($row->filename)
                            <div class="form-group row">
                                <label for="user_type_id" class="col-3 col-form-label text-right required">Uploaded
                                    File:</label>
                                <div class="col-6">
                                    <a href="{{ asset_url('media/knowledge_hub/' . $row->filename, true) }}"
                                        download="download">{{ $row->filename }}</a>
                                </div>
                            </div>
                        @endif

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
