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
                            <label for="role" class="col-3 col-form-label text-right">Holiday Description:
                                <span class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="holiday_description"
                                    value="{{ old('holiday_description', Crypto::decryptData($row->holiday_description, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Please Holiday Description">
                            </div>
                        </div>

                        {{-- Stores in mm-dd-yyyy --}}
                        <div class="form-group row">
                            <label class="col-form-label text-right col-lg-3 col-sm-12">Holiday Date: <span
                                    class="text-danger">*</span></label>
                            <div class="col-lg-4 col-md-9 col-sm-12">
                                <input type="date" class="form-control" name="holiday_date"
                                    value="{{ old('holiday_date', $row->holiday_date) }}" placeholder="Select date">
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

    <script src="{{ asset('theme/assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js?v=7.2.8') }}"></script>

@endsection
