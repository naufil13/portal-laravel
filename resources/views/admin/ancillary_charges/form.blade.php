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
                            <label for="module" class="col-3 col-form-label text-right required">Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="name"
                                    value="{{ old('name', Crypto::decryptData($row->name, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Ancillary Charge Name"
                                    required="required">
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
    <script>
        $(document).ready(function() {
            $('.toShow').hide();
            if ($('input[name="type"]:checked').val() == 'Per Hour') {
                $('.toShow').show();
                $('.forRequired').prop('required', true);
            } else {
                $('.toShow').hide();
                $('.forRequired').removeAttr('required');
            }
        });
        $(document).on('change', '.onChecks', function() {
            if (this.checked) {
                if ($(this).val() === 'Per Hour') {
                    $('.toShow').show();
                    $('.forRequired').prop('required', true);
                } else {
                    $('.toShow').hide();
                    $('#rate').val('');
                    $('.forRequired').removeAttr('required');
                }
            }
        });
    </script>

@endsection
