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
                            <label for="module" class="col-3 col-form-label text-right required">Payment Type: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="payment_type"
                                    value="{{ old('payment_type', Crypto::decryptData($row->payment_type, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Payment Type" @if(count($row->paymentDetails)) readonly="readonl" @endif />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Amount:<span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input type='number' name="amount"
                                    value="{{ old('amount', Crypto::decryptData($row->amount, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" placeholder="Enter Amount" />
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
