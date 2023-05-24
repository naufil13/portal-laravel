@php
$pass_data['form_buttons'] = ['back'];
@endphp
@extends('admin.layouts.admin')

@section('content')
    {{-- Content --}}

    @include('admin.layouts.inc.stickybar', $pass_data)

    <!-- begin:: Content -->
    <div class="row mt-10">

        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="card-header">
                    <div class="card-toolbar">
                        <ul class="nav nav-bold nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#ticket-form">Form</a>
                            </li>
                            @php
                                $types = ['Developer', 'Super-Admin', 'Admin', 'Site-Coordinator-Collector', 'Site-Coordinator', 'Sponsor', 'Site-Monitor'];
                            @endphp
                            @if (in_array(Auth::user()->usertype['user_type'], $types) && $row->id)
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#assign-ticket">Assign-Ticket</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#audit-log">Audit-Log</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#comment">Comments</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#files">Files</a>
                                </li>
                            @endif

                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="ticket-form" role="tabpanel"
                            aria-labelledby="ticket-form">
                            <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
                                <div class="row alert-text">
                                    <p class="font-weight-bold text-primary font-size-h3 ml-3">
                                        User-Info
                                    </p>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Ticket Source:
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-6">
                                        @if ($row->ticket_source)
                                            <input class="form-control" name="ticket_source"
                                                value="{{ $row->ticket_source }}" type="hidden">
                                            <input class="form-control" type="text" readonly
                                                placeholder="{{ $ticket_source[0]->source }}">
                                        @else
                                            <select name="ticket_source" id="ticket_source" class="form-control m-select2"
                                                required>
                                                <option value="">Select Ticket Source</option>
                                                <?php echo selectBox('SELECT id, source FROM helpdesk_sources', old('ticket_source', $row->ticket_source)); ?>
                                            </select>
                                        @endif

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Ticket Creator:
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" type="text" disabled
                                            placeholder="{{ Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey()) }}"
                                            value="{{ old(Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey()),Crypto::decryptData($row->ticket_creator, Crypto::getAwsEncryptionKey())) }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Ticket Owner: <span
                                            class="text-danger">*</span>
                                    </label>
                                    <div class="col-6" id="ticket_owner">
                                        @if ($row->ticket_owner)
                                            <input class="form-control" name="ticket_owner"
                                                value="{{ Crypto::decryptData($row->ticket_owner, Crypto::getAwsEncryptionKey()) }}"
                                                type="hidden">
                                            <input class="form-control" type="text" readonly
                                                placeholder="{{ Crypto::decryptData($ticket_owner[0]->ticket_owner, Crypto::getAwsEncryptionKey()) }}">
                                        @else
                                            <input class="form-control emailField" type="text" disabled
                                                placeholder="{{ Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey()) }}">
                                            <input class="form-control owner_user" type="hidden" name="ticket_owner"
                                                placeholder="{{ Crypto::decryptData(Auth::user()->email, Crypto::getAwsEncryptionKey()) }}">
                                            <div class="users_list">
                                                <select name="ticket_owner" id="sel_ticket_owner"
                                                    class="form-control owner_user m-select2">
                                                    <option value="">Select Ticket Owner</option>
                                                    <?php echo selectBox("SELECT id, email FROM users WHERE `status`='Active' AND `user_type_id` != '18'"); ?>
                                                </select>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">First Name:
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" id="first_name" type="text" name="first_name"
                                            placeholder="First Name" value="@if ($row->first_name){{ Crypto::decryptData($row->first_name, Crypto::getAwsEncryptionKey()) }} @else {{ Crypto::decryptData(Auth::user()->first_name, Crypto::getAwsEncryptionKey()) }} @endif" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Last Name:
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" id="last_name" type="text" name="last_name"
                                            value="@if ($row->last_name){{ Crypto::decryptData($row->last_name, Crypto::getAwsEncryptionKey()) }} @else {{ Crypto::decryptData(Auth::user()->last_name, Crypto::getAwsEncryptionKey()) }} @endif" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Tenant:
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" id="tenant_name" type="text" name="tenant_name"
                                            value="@if ($row->tenant_name){{ $row->tenant_name }}@else {{ Auth::user()->userclient['login_code'] }} @endif" readonly>
                                    </div>
                                </div>
                                <div class="row alert-text">
                                    <p class="font-weight-bold text-primary font-size-h3 ml-3">
                                        Ticket-Info
                                    </p>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Ticket #:
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" type="text" name="ticket_number"
                                            value="@if ($row->ticket_number){{ $row->ticket_number }}@else {{ 'TN-' . random_int(100000, 999999) }} @endif" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">
                                        Resolution Date:
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" type="date" name="resolution_date" value="{{ $row->resolution_date ?? "" }}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Ticket
                                        Title/Subject: <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-6">
                                        <input class="form-control" type="text" name="ticket_title"
                                            value="{{ old('ticket_title', $row->ticket_title) }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right required">Issue Description:
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-6">
                                        <textarea class="form-control description" name="ticket_description"
                                            maxlength="500">{{ old('ticket_description', $row->ticket_description) }}</textarea>
                                    </div>
                                    <label id="charNum_edit" class="text-danger"></label>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right">File Upload:
                                    </label>
                                    <div class="col-6">
                                        <input type="file" name="ticket_upload[]" class="form-control"
                                            accept="application/pdf" multiple>
                                    </div>
                                </div>
                                {{-- @if (count($ticket_files) > 0) --}}
                                {{-- <div class="form-group row"> --}}
                                {{-- <label for="module" class="col-3 col-form-label text-right">Uploaded File(s): --}}
                                {{-- </label> --}}
                                {{-- <div class="col-6"> --}}
                                {{-- <table class="table"> --}}
                                {{-- <thead> --}}
                                {{-- <tr> --}}
                                {{-- <th scope="col">Uploaded At</th> --}}
                                {{-- <th scope="col">File</th> --}}
                                {{-- </tr> --}}
                                {{-- </thead> --}}
                                {{-- <tbody> --}}
                                {{-- @foreach ($ticket_files as $ticket_file) --}}
                                {{-- <tr> --}}
                                {{-- <td> --}}
                                {{-- {{ CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_file->created) }} --}}
                                {{-- </td> --}}
                                {{-- <td> --}}
                                {{-- <a href="{{ asset_url('media/helpdesk/' . $ticket_file->filename, true) }}" --}}
                                {{-- download="download">{{ $ticket_file->filename }}</a> --}}
                                {{-- </td> --}}
                                {{-- </tr> --}}
                                {{-- @endforeach --}}
                                {{-- </tbody> --}}
                                {{-- </table> --}}
                                {{-- </div> --}}
                                {{-- </div> --}}
                                {{-- @endif --}}
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-md btn-primary btn-sm">
                                        <i class="la la-save"></i>Submit Now
                                    </button>
                                </div>
                            </form>
                        </div>
                        {{-- Form add/edit ends --}}
                        <div class="tab-pane fade" id="assign-ticket" role="tabpanel" aria-labelledby="assign-ticket">
                            <form action="{{ admin_url('assign', true) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
                                <input type="hidden" name="ticket_number"
                                    value="{{ old('ticket_number', $row->ticket_number) }}">
                                <div class="row alert-text">
                                    <p class="font-weight-bold text-primary font-size-h3 ml-3">
                                        Assign-Ticket
                                    </p>
                                </div>
                                <div class="form-group row">
                                    <label for="ticket_priority" class="col-3 col-form-label text-right">Ticket
                                        Priority: <span class="text-danger">*</span></label>
                                    <div class="col-6">
                                        <select name="ticket_priority" class="form-control m-select2" required>
                                            <option value="">Select Ticket Priority</option>
                                            <?php echo selectBox('SELECT id, priority FROM helpdesk_priorities', $row->ticket_priority); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ticket_priority" class="col-3 col-form-label text-right">Ticket
                                        Category: <span class="text-danger">*</span></label>
                                    <div class="col-6">
                                        <select name="ticket_category" class="form-control m-select2" required>
                                            <option value="">Select Ticket Category</option>
                                            <?php echo selectBox('SELECT id, category FROM helpdesk_categories', $row->ticket_category); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ticket_priority"
                                        class="col-3 col-form-label text-right">Application:</label>
                                    <div class="col-6">
                                        <select name="ticket_application" class="form-control m-select2">
                                            <option value="">Select Application</option>
                                            <?php echo selectBox('SELECT id, application_name FROM applications', $row->ticket_application); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ticket_priority" class="col-3 col-form-label text-right">Ticket
                                        Status: <span class="text-danger">*</span></label>
                                    <div class="col-6">
                                        <select name="ticket_status" class="form-control m-select2" required>
                                            <option value="">Select Status</option>
                                            @php
                                                $v = $ticket_status[0]->hierarchy;
                                            @endphp
                                            @if ($row->ticket_status)
                                                <option value="{{ $row->ticket_status }}" selected>
                                                    {{ $ticket_status[0]->status }}</option>
                                                <?php echo selectBox("SELECT id, status FROM helpdesk_ticket_statuses WHERE helpdesk_ticket_statuses.id IN ($v)", old('ticket_status', $row->ticket_status)); ?>
                                            @else
                                                <?php echo selectBox('SELECT id, status FROM helpdesk_ticket_statuses', old('ticket_status', $row->ticket_status)); ?>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ticket_priority" class="col-3 col-form-label text-right">Assign Ticket:
                                        <span class="text-danger">*</span></label>
                                    <div class="col-6">
                                        <select name="ticket_assignee" class="form-control m-select2" required>
                                            <option value="">Select Assignee</option>
                                            <?php echo selectBox('SELECT id, assignee FROM helpdesk_ticket_assignees', old('ticket_assignee', $row->ticket_assignee)); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-md btn-primary btn-sm">
                                        <i class="la la-save"></i>Submit Now
                                    </button>
                                </div>
                            </form>
                        </div>
                        {{-- Assign Tab ends here --}}
                        <div class="tab-pane fade" id="audit-log" role="tabpanel" aria-labelledby="audit-log">
                            <div class="row alert-text">
                                <p class="font-weight-bold text-primary font-size-h3 ml-3">
                                    Audit-Log
                                </p>
                            </div>
                            <div class="row alert-text">
                                <div class="timeline timeline-5">
                                    <div class="timeline-items">
                                        @foreach ($ticket_audit_logs as $ticket_audit_log)
                                            @if ($ticket_audit_log->activity === 'Create')
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Icon-->
                                                    <div class="timeline-media bg-light-primary">
                                                        <span class="svg-icon-primary svg-icon-md">

                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo3/dist/../src/media/svg/icons/Communication/Chat4.svg--><svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24" />
                                                                        <path
                                                                            d="M21.9999843,15.009808 L22.0249378,15 L22.0249378,19.5857864 C22.0249378,20.1380712 21.5772226,20.5857864 21.0249378,20.5857864 C20.7597213,20.5857864 20.5053674,20.4804296 20.317831,20.2928932 L18.0249378,18 L5,18 C3.34314575,18 2,16.6568542 2,15 L2,6 C2,4.34314575 3.34314575,3 5,3 L19,3 C20.6568542,3 22,4.34314575 22,6 L22,15 C22,15.0032706 21.9999948,15.0065399 21.9999843,15.009808 Z M6.16794971,10.5547002 C7.67758127,12.8191475 9.64566871,14 12,14 C14.3543313,14 16.3224187,12.8191475 17.8320503,10.5547002 C18.1384028,10.0951715 18.0142289,9.47430216 17.5547002,9.16794971 C17.0951715,8.86159725 16.4743022,8.98577112 16.1679497,9.4452998 C15.0109146,11.1808525 13.6456687,12 12,12 C10.3543313,12 8.9890854,11.1808525 7.83205029,9.4452998 C7.52569784,8.98577112 6.90482849,8.86159725 6.4452998,9.16794971 C5.98577112,9.47430216 5.86159725,10.0951715 6.16794971,10.5547002 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                                <!--end::Svg Icon-->
                                                            </span>

                                                        </span>
                                                    </div>
                                                    <!--end::Icon-->

                                                    <!--begin::Info-->
                                                    <div class="timeline-desc timeline-desc-light-primary">
                                                        <span
                                                            class="font-weight-bolder text-primary">{{ $ticket_audit_log->activity .', ' .CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_audit_log->created_at) }}</span>
                                                        <p class="font-weight-normal text-dark-50 pb-2">
                                                            {{ $ticket_audit_log->description }}
                                                        </p>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Item-->
                                            @elseif($ticket_audit_log->activity === 'Update')
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Icon-->
                                                    <div class="timeline-media bg-light-warning">
                                                        <span class="svg-icon-warning svg-icon-md">

                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo3/dist/../src/media/svg/icons/General/Update.svg--><svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24" />
                                                                        <path
                                                                            d="M8.43296491,7.17429118 L9.40782327,7.85689436 C9.49616631,7.91875282 9.56214077,8.00751728 9.5959027,8.10994332 C9.68235021,8.37220548 9.53982427,8.65489052 9.27756211,8.74133803 L5.89079566,9.85769242 C5.84469033,9.87288977 5.79661753,9.8812917 5.74809064,9.88263369 C5.4720538,9.8902674 5.24209339,9.67268366 5.23445968,9.39664682 L5.13610134,5.83998177 C5.13313425,5.73269078 5.16477113,5.62729274 5.22633424,5.53937151 C5.384723,5.31316892 5.69649589,5.25819495 5.92269848,5.4165837 L6.72910242,5.98123382 C8.16546398,4.72182424 10.0239806,4 12,4 C16.418278,4 20,7.581722 20,12 C20,16.418278 16.418278,20 12,20 C7.581722,20 4,16.418278 4,12 L6,12 C6,15.3137085 8.6862915,18 12,18 C15.3137085,18 18,15.3137085 18,12 C18,8.6862915 15.3137085,6 12,6 C10.6885336,6 9.44767246,6.42282109 8.43296491,7.17429118 Z"
                                                                            fill="#000000" fill-rule="nonzero" />
                                                                    </g>
                                                                </svg>
                                                                <!--end::Svg Icon-->
                                                            </span>

                                                        </span>
                                                    </div>
                                                    <!--end::Icon-->

                                                    <!--begin::Info-->
                                                    <div class="timeline-desc timeline-desc-light-warning">
                                                        <span
                                                            class="font-weight-bolder text-warning">{{ $ticket_audit_log->activity .', ' .CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_audit_log->created_at) }}</span>
                                                        <p class="font-weight-normal text-dark-50 pt-1 pb-2">
                                                            {{ $ticket_audit_log->description }}
                                                        </p>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Item-->
                                            @elseif($ticket_audit_log->activity === 'Assign and Change Status')
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Icon-->
                                                    <div class="timeline-media bg-light-success">
                                                        <span class="svg-icon-success svg-icon-md">

                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo3/dist/../src/media/svg/icons/Home/Library.svg--><svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24" />
                                                                        <path
                                                                            d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z"
                                                                            fill="#000000" />
                                                                        <rect fill="#000000" opacity="0.3"
                                                                            transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519) "
                                                                            x="16.3255682" y="2.94551858" width="3"
                                                                            height="18" rx="1" />
                                                                    </g>
                                                                </svg>
                                                                <!--end::Svg Icon-->
                                                            </span>

                                                        </span>
                                                    </div>
                                                    <!--end::Icon-->

                                                    <!--begin::Info-->
                                                    <div class="timeline-desc timeline-desc-light-success">
                                                        <span
                                                            class="font-weight-bolder text-success">{{ $ticket_audit_log->activity .', ' .CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_audit_log->created_at) }}</span>
                                                        <p class="font-weight-normal text-dark-50 pt-1 pb-2">
                                                            {{ $ticket_audit_log->description }}
                                                        </p>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Item-->
                                            @elseif($ticket_audit_log->activity === 'Comment')
                                                <!--begin::Item-->
                                                <div class="timeline-item">
                                                    <!--begin::Icon-->
                                                    <div class="timeline-media bg-light-danger">
                                                        <span class="svg-icon-danger svg-icon-md">

                                                            <span class="svg-icon svg-icon-primary svg-icon-2x">
                                                                <!--begin::Svg Icon | path:/var/www/preview.keenthemes.com/metronic/releases/2021-05-14-112058/theme/html/demo3/dist/../src/media/svg/icons/Communication/Chat-smile.svg--><svg
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                                    height="24px" viewBox="0 0 24 24" version="1.1">
                                                                    <g stroke="none" stroke-width="1" fill="none"
                                                                        fill-rule="evenodd">
                                                                        <rect x="0" y="0" width="24" height="24" />
                                                                        <polygon fill="#000000" opacity="0.3"
                                                                            points="5 15 3 21.5 9.5 19.5" />
                                                                        <path
                                                                            d="M13,2 C18.5228475,2 23,6.4771525 23,12 C23,17.5228475 18.5228475,22 13,22 C7.4771525,22 3,17.5228475 3,12 C3,6.4771525 7.4771525,2 13,2 Z M7.16794971,13.5547002 C8.67758127,15.8191475 10.6456687,17 13,17 C15.3543313,17 17.3224187,15.8191475 18.8320503,13.5547002 C19.1384028,13.0951715 19.0142289,12.4743022 18.5547002,12.1679497 C18.0951715,11.8615972 17.4743022,11.9857711 17.1679497,12.4452998 C16.0109146,14.1808525 14.6456687,15 13,15 C11.3543313,15 9.9890854,14.1808525 8.83205029,12.4452998 C8.52569784,11.9857711 7.90482849,11.8615972 7.4452998,12.1679497 C6.98577112,12.4743022 6.86159725,13.0951715 7.16794971,13.5547002 Z"
                                                                            fill="#000000" />
                                                                    </g>
                                                                </svg>
                                                                <!--end::Svg Icon-->
                                                            </span>

                                                        </span>
                                                    </div>
                                                    <!--end::Icon-->

                                                    <!--begin::Info-->
                                                    <div class="timeline-desc timeline-desc-light-danger">
                                                        <span
                                                            class="font-weight-bolder text-danger">{{ $ticket_audit_log->activity .', ' .CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_audit_log->created_at) }}</span>
                                                        <p class="font-weight-normal text-dark-50 pt-1">
                                                            {{ $ticket_audit_log->description }}
                                                        </p>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Item-->
                                            @endif

                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Audit Log ends here --}}
                        <div class="tab-pane fade" id="comment" role="tabpanel" aria-labelledby="comment">
                            <form action="{{ admin_url('comment', true) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
                                <input type="hidden" name="ticket_number"
                                    value="{{ old('ticket_number', $row->ticket_number) }}">
                                <div class="row alert-text">
                                    <p class="font-weight-bold text-primary font-size-h3 ml-3">
                                        Comments
                                    </p>
                                </div>
                                <div class="form-group row">
                                    <label for="ticket_priority" class="col-3 col-form-label text-right">Comments:
                                        <span class="text-danger">*</span></label>
                                    <div class="col-6">
                                        <textarea class="form-control" name="ticket_comment" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="module" class="col-3 col-form-label text-right">File Upload:
                                    </label>
                                    <div class="col-6">
                                        <input type="file" name="ticket_comments_upload[]" class="form-control"
                                            accept="application/pdf" multiple>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-md btn-primary btn-sm commentBtn">
                                        <i class="la la-save"></i>Submit Now
                                    </button>
                                </div>
                                <div class="form-group row mt-10">
                                    <div class="col-12">

                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Created</th>
                                                    <th scope="col">Comment</th>
                                                    <th scope="col">Uploads</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($ticket_comments as $ticket_comment)
                                                    <tr>
                                                        <td>{{ CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_comment->created_at) }}
                                                        </td>
                                                        <td>{{ $ticket_comment->ticket_comment }}</td>
                                                        <td>
                                                            @php
                                                                $specificCommentFiles = [];
                                                                foreach ($ticket_comment_files as $ticket_comment_file) {
                                                                    if ($ticket_comment->id == $ticket_comment_file->comment_activity_id) {
                                                                        $specificCommentFiles[] = $ticket_comment_file->filename;
                                                                    }
                                                                }
                                                                // dd($specificCommentFiles);
                                                            @endphp
                                                            @if (empty($specificCommentFiles) === false)
                                                                <div class="col-md-4">
                                                                    <div class="dropdown">
                                                                        <button class="btn btn-primary dropdown-toggle"
                                                                            type="button" data-toggle="dropdown">View-File
                                                                            <span class="caret"></span>
                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <?php foreach ($specificCommentFiles as $specificCommentFile): ?>
                                                                            <li class="dropdown-item">
                                                                                <a href="{{ asset_url('media/helpdesk/' . $specificCommentFile, true) }}"
                                                                                    download="download">{{ $specificCommentFile }}</a>
                                                                                </a>
                                                                            </li>
                                                                            <?php endforeach; ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </form>
                        </div>
                        {{-- Comment Tab ends here --}}
                        <div class="tab-pane fade" id="files" role="tabpanel" aria-labelledby="files">
                            <div class="row alert-text">
                                <p class="font-weight-bold text-primary font-size-h3 ml-3">
                                    Uploaded-Files
                                </p>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <table class="table">
                                        <caption class="font-weight-bolder">Ticket Form Uploads</caption>
                                        <thead>
                                            <tr>
                                                <th scope="col">Filename</th>
                                                <th scope="col">Submitted By</th>
                                                <th scope="col">Submitted At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ticket_files as $ticket_file)
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset_url('media/helpdesk/' . $ticket_file->filename, true) }}"
                                                            download="download">{{ $ticket_file->filename }}</a>
                                                        </a>
                                                    </td>
                                                    <td>{{ Crypto::decryptData($ticket_file->created_by, Crypto::getAwsEncryptionKey()) }}
                                                    </td>
                                                    <td>
                                                        {{ CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_file->created) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <table class="table">
                                        <caption class="font-weight-bolder">Comment Form Uploads</caption>
                                        <thead>
                                            <tr>
                                                <th scope="col">Filename</th>
                                                <th scope="col">Submitted By</th>
                                                <th scope="col">Submitted At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ticket_comment_files as $ticket_comment_file)
                                                <tr>
                                                    <td>
                                                        <a href="{{ asset_url('media/helpdesk/' . $ticket_comment_file->filename, true) }}"
                                                            download="download">{{ $ticket_comment_file->filename }}</a>
                                                        </a>
                                                    </td>
                                                    <td>{{ Crypto::decryptData($ticket_comment_file->created_by, Crypto::getAwsEncryptionKey()) }}
                                                    </td>
                                                    <td>
                                                        {{ CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_comment_file->created) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- Files section ends here --}}

                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card card-custom">
                <div class="card-header">
                    <h3 class="card-title"> Ticket-Uploads </h3>
                </div>
                <div class="card-body">
                    @foreach ($ticket_files as $ticket_file)

                        <div class="d-flex flex-column align-items-center mt-5">
                            {!! uploadedFilesIcons(explode('.', $ticket_file->filename)[1]) !!}
                            <a style="width: 100%; text-align:center;"
                                href="{{ asset_url('media/helpdesk/' . $ticket_file->filename, true) }}"
                                download="download">{{ $ticket_file->filename }}</a>
                            <p class="text-center">
                                {{ CarbonHelper::DateTimeFormatterToStringBasedFormat($ticket_file->created) }}</p>
                        </div>

                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <!-- end:: Content -->


@endsection

{{-- Scripts --}}

@section('scripts')
    <script>
        $("#ticket_source").on("change", function() {
            $(".users").val('').trigger('change');
            if (this.options[this.selectedIndex].text != 'Self') {
                $('.emailField').hide();
                $('.users_list').show();

                $('.owner_user').eq(0).attr('name', '');
                $('.owner_user').eq(1).attr('name', 'ticket_owner');
                $('.owner_user').eq(1).attr('required', 'required');

            } else {
                $('.emailField').show();
                $('.users_list').hide();

                $('.owner_user').eq(1).attr('name', '');
                $('.owner_user').eq(1).removeAttr('required');
                $('.owner_user').eq(0).attr('name', 'ticket_owner');
                $('.owner_user').eq(0).val("{{ Auth::user()->id }}");

                $("#first_name").val(
                    "{{ Crypto::decryptData(Auth::user()->first_name, Crypto::getAwsEncryptionKey()) }}");
                $("#last_name").val(
                    "{{ Crypto::decryptData(Auth::user()->last_name, Crypto::getAwsEncryptionKey()) }}");
                $("#tenant_name").val("{{ Auth::user()->userclient['login_code'] }}");
            }
        });

        $("#sel_ticket_owner").on("change", function() {
            var email_id = $(this).val();
            $.ajax({
                type: "get",
                url: `{{ admin_url('getUserInfoByEmailId', true) }}/` + email_id,
                success: function(res) {
                    if (res.length > 0) {
                        $("#first_name").val(res[0].first_name);
                        $("#last_name").val(res[0].last_name);
                        $("#tenant_name").val(res[0].login_code);
                    } else {
                        $("#first_name").val('No Data Found');
                        $("#last_name").val('No Data Found');
                        $("#tenant_name").val('No Data Found');
                    }
                }
            });
        });

        $(document).ready(function() {
            $('.users_list').hide();

            $('.description').keyup(function() {
                var max = 5000;
                var len = $(this).val().length;
                if (len >= max) {
                    $('#charNum_edit').text('Character Limit Reached');
                } else {
                    var char = max - len;
                    $('#charNum_edit').text(char + ' characters left');
                }
            });

            $(".commentBtn").click(function() {
                $(this).hide();
            })
        });
    </script>
@endsection
