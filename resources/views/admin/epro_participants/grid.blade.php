@php
$status_column_data = DB_enumValues('users', 'status');

@endphp
@extends('admin.layouts.admin')

@section('content')

        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="table-responsive">
                            @if ($errors->any())
                                <label class="font-size-h6 font-weight-bolder text-danger">{{ $errors->first() }}</label>
                                <br>
                            @endif
                            <form  method="GET">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <select name="trial_id" class="form-control" required>
                                                <option value="">Select Trial</option>
                                                @foreach ($trials as $trial)
                                                    <option value="{{ $trial->id }}" @if(request()->query('trial_id') == $trial->id) selected @endif>{{ Crypto::decryptData($trial->study_name, Crypto::getAwsEncryptionKey()) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" type="submit">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table table-hover dt-table  icon-color grid-table kt-margin-0">
                                <thead>
                                    {{-- <th>User Type</th> --}}
                                    <th>ERX-ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    @foreach ($participants as $participant)
                                        <tr>
                                            {{-- <td>{{$participant->user_type_id}}</td> --}}
                                            <td>{{ Crypto::decryptData($participant->erx_id, Crypto::getAwsEncryptionKey()) }}</td>
                                            <td>{{ Crypto::decryptData($participant->first_name, Crypto::getAwsEncryptionKey()) }}</td>
                                            <td>{{ Crypto::decryptData($participant->last_name, Crypto::getAwsEncryptionKey()) }}</td>
                                            <td>{{ Crypto::decryptData($participant->email, Crypto::getAwsEncryptionKey()) }}</td>
                                            <td>
                                                <div class="btn-group dropup">
                                                    @php
                                                        $enum_vals = DB_enumValues('users', 'status');
                                                        if ($participant->status == 'Active') {
                                                            $cls = 'success';
                                                        } elseif ($participant->status == 'Inactive') {
                                                            $cls = 'danger';
                                                        } else {
                                                            $cls = 'warning';
                                                        }
                                                    @endphp

                                                    <button type="button" class="m-btn--pill m-btn--air btn btn-sm btn-{{ $cls }}">{{ $participant->status }}</button>
                                                    <button type="button" class="m-btn--pill m-btn--air btn btn-sm btn-{{ $cls }} dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="sr-only">Toggle Dropdown</span>
                                                    </button>
                                                    <div class="dropdown-menu" x-placement="bottom-start">
                                                        @foreach ($enum_vals as $enumval)
                                                            <a class="dropdown-item status_change" data-pid="{{ $participant->id }}">{{ $enumval }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="cursor:pointer;" data-toggle="modal" data-target="#exampleModalCenter_{{$participant->id}}">
                                                    <i class="flaticon2-pen text-primary"></i>
                                                </span>
                                                <form action="{{ admin_url('updateParticipantEmail', true) }}" method="post" enctype="multipart/form-data" class="emailChangeForm" data-id="{{ $participant->id }}">
                                                    @csrf
                                                    <div class="modal fade" id="exampleModalCenter_{{$participant->id}}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Update Participant Email</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <i aria-hidden="true" class="ki ki-close"></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div data-scroll="true" data-height="200">
                                                                    <div class="form-group">
                                                                        <label>Current Email:</label>
                                                                        <input type="email" disabled="disabled" class="form-control" placeholder="{{Crypto::decryptData($participant->email, Crypto::getAwsEncryptionKey())}}">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Updated Email:</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="email" class="form-control" name="updated_email" placeholder="Enter updated email" data-id="update-email-{{ $participant->id }}" required />
                                                                        <input type="hidden" name="user_id" value="{{$participant->id}}">
                                                                        <span class="form-text text-muted"></span>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Confirm Email:</label>
                                                                        <span class="text-danger">*</span>
                                                                        <input type="email" class="form-control" name="confirm_email" placeholder="Confirm email" data-id="confirm-email-{{ $participant->id }}" required />
                                                                        <span class="form-text text-muted"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary font-weight-bold">Save changes</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection

{{-- Scripts --}}
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            let isFormSubmit = false;
            $(".emailChangeForm").submit(function(e){
                var id = $(this).data('id');
                var updateEmail = $(`[data-id="update-email-${id}"]`).val();
                var confirmEmail = $(`[data-id="confirm-email-${id}"]`).val();
                const form = $(this);
                if(confirmEmail !== updateEmail) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Error",
                        text: "Email does not match",
                        icon: "error"
                    })
                    return
                }

                if (!isFormSubmit) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You want to change the email?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes!"
                    }).then(function(result) {
                        if (result.isConfirmed == true) {
                            isFormSubmit = true;
                            form.submit();
                        }
                    });
                }


            });
            $(document).on('click', '.status_change', function() {
                var participant_id = $(this).data('pid');
                var status = $(this).text();

                Swal.fire({
                    title: "Are you sure?",
                    text: "You want to change the Status?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes!"
                }).then(function(result) {
                    if (result.value) {
                        window.location.href = '{{ admin_url(getUri(2)) }}/status/' +
                            participant_id + '?status=' + status;
                    }
                });
            });
        });
    </script>
@endsection
