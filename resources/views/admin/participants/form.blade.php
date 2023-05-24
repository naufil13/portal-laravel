@php
$pass_data['form_buttons'] = ['save', 'back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}
    <form action="{{ admin_url('import', true) }}" method="post" enctype="multipart/form-data">
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
                            <label class="col-form-label text-right col-lg-3 col-sm-12">Download CSV-File template</label>
                            <div class="col-lg-4 col-md-9 col-sm-12">
                                <a href="{{ asset_url('media/participants/participants.csv', true) }}"
                                    class="btn btn-light-success font-weight-bold btn-sm" download>Download CSV File
                                    Template</a>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label text-lg-right">Upload CSV File:</label>
                            <div class="col-lg-6">
                                <div class="uppy" id="kt_uppy_5">
                                    <div class="uppy-wrapper">
                                        <div class="uppy-Root uppy-FileInput-container">
                                            <input class="uppy-FileInput-input uppy-input-control" type="file"
                                                name="clinical_protocol_document" multiple="" accept="application/csv"
                                                id="kt_uppy_5_input_control" style="">
                                            <label class="uppy-input-label btn btn-light-primary btn-sm btn-bold"
                                                for="kt_uppy_5_input_control">Attach CSV File</label>
                                        </div>
                                    </div>
                                    <div class="uppy-list"></div>
                                    <div class="uppy-status">
                                        <div class="uppy-Root uppy-StatusBar is-waiting" aria-hidden="true" dir="ltr">
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
                                <span class="form-text text-muted">Only .csv file is allowed</span>
                            </div>
                        </div>

                        <div class="btn-group">
                            <button type="submit" class="btn btn-md btn-primary btn-sm">
                                <i class="la la-save"></i>Submit Now
                            </button>
                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
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
