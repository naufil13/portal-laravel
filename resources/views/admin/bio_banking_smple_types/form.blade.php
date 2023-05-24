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
                            <label for="module" class="col-3 col-form-label required">Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="name" value="{{ old('name', $row->name) }}" class="form-control"
                                    type="text" placeholder="Enter Sample Type" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label required">Color: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="color" value="{{ old('color', $row->color) }}" class="form-control"
                                    type="color" placeholder="Enter Sample Type" required>
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
