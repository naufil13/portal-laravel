<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

@extends('admin.layouts.admin')


@section('content')
    {{-- Content --}}
    
@if(isset($row->id))    
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#ticketform" role="tab" aria-controls="ticketform" aria-selected="true">Ticket Form</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#assignticket" role="tab" aria-controls="assignticket" aria-selected="assignticket">Assign Ticket</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#auditlog" role="tab" aria-controls="auditlog" aria-selected="false">Audit Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#comment" role="tab" aria-controls="comment" aria-selected="false">Comments</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#file" role="tab" aria-controls="file" aria-selected="false">Files</a>
        </li>
    </ul>
    
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="ticketform" role="tabpanel" aria-labelledby="ticketform-tab">
            @include('admin.supports.ticket')
        </div>
        <div class="tab-pane fade" id="assignticket" role="tabpanel" aria-labelledby="assignticket-tab">
            @include('admin.supports.assign')
        </div>
        <div class="tab-pane fade" id="auditlog" role="tabpanel" aria-labelledby="auditlog-tab">
            @include('admin.supports.auditlog')
        </div>
        <div class="tab-pane fade" id="comment" role="tabpanel" aria-labelledby="comment-tab">
            @include('admin.supports.comment')
        </div>
        <div class="tab-pane fade" id="file" role="tabpanel" aria-labelledby="file-tab">
            @include('admin.supports.file')
        </div>
    </div>

    @else
        @include('admin.supports.ticket')
    @endif

    {{-- Scripts --}}

    @section('scripts')
        <script type="text/javascript">
            $(document).ready(function($) {
                $('#owner_username').on('change', function(e) {
                    var owner_id =  $(this).val();     
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getFirstNameByUserName', true) }}/`+ owner_id,
                        success: function(res) {
                            if (res) {
                                console.log(res);
                                $(".first_name").empty();
                                $.each(res, function(index, stateObj) {
                                    $('.first_name').val(stateObj.first_name);
                                })
                            }
                        }
                    });
                });
                $('#owner_username').on('change', function(e) {
                    var owner_id =  $(this).val();     
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getLastNameByUserName', true) }}/`+ owner_id,
                        success: function(res) {
                            if (res) {
                                console.log(res);
                                $(".last_name").empty();
                                $.each(res, function(index, stateObj) {
                                    $('.last_name').val(stateObj.last_name);

                                })
                            }
                        }
                    });
                });
                $('#ticket_source_id').on('change', function(e) {
                    var ticket =  $(this).val();     
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('getsource', true) }}/`+ ticket,
                        success: function(res) {
                            if (res) {
                                console.log(res);
                            }
                        }
                    });
                });
                $('#status_id').on('change', function(e) {
                    var status_id =  $(this).val(); 
                    var id = $('#session_id').val();    
                    var ticket =$('#ticket_no').val();  
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('updatestatus', true) }}?status=`+ status_id+'&'+'id='+id+'&'+'ticket='+ticket,
                        success: function(res) {
                            if (res == 'done') {
                                location.reload();
                            }
                        }
                    });
                });
                $('#assignee').on('change', function(e) {
                    var assignee =  $(this).val(); 
                    var id = $('#session_id').val(); 
                    var ticket =$('#ticket_no').val();     
                    $.ajax({
                        type: "get",
                        url: `{{ admin_url('updateassignee', true) }}?assignee=`+ assignee+'&'+'id='+id+'&'+'ticket='+ticket,
                        success: function(res) {
                            if (res == 'done') {
                                location.reload();
                            }
                        }
                    });
                });
            });
        </script>
    @endsection

@endsection