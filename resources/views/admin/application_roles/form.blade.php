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
                            <label for="role" class="col-3 col-form-label text-right">Role:
                                <span class="required">*</span></label>
                            <div class="col-6">
                                <input name="role" value="{{ old('role', $row->role) }}" class="form-control"
                                    type="text" placeholder="Please enter Role" id="role">
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
