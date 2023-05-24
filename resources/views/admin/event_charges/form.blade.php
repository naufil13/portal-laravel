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
                            <label for="module" class="col-3 col-form-label text-right required">Event: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                @php
                                    $login_code = Auth::user()->userclient['login_code'];
                                @endphp
                                <select name="event_registration_id" id="event_registration_id"
                                    class="form-control m-select2">
                                    <option value="" disabled>Select Event</option>
                                    <?php echo selectBox("SELECT id, event_name FROM event_registrations WHERE tenant_code='" . $login_code . "'", Crypto::decryptData($row->event_registration_id, Crypto::getAwsEncryptionKey())); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Charges($): <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="charges"
                                    value="{{ old('charges', Crypto::decryptData($row->charges, Crypto::getAwsEncryptionKey())) }}"
                                    class="form-control" type="text" placeholder="Enter Charges">
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
