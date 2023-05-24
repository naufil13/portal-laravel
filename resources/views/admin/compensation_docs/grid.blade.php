@php
$status_column_data = DB_enumValues('users', 'status');

@endphp
@extends('admin.layouts.admin')

@section('content')

        @csrf
        <div class="subheader grid__item" id="subheader">

            <div class="w-100 ">


                            <div class="card card-custom">

                        <div class="card-header">
                            <div class="card-title">
                                <h3 class="card-label">Compensation Documents</h3>
                            </div>
                            <div class="card-toolbar">

                                @if(in_array("add",$user_actions))
                                <a  class="btn btn-light-info font-weight-bold btn-sm ml-2" data-toggle="modal" data-target="#exampleModalCenter"><span><i class="la la-file"></i><span>Create New</span></span></a>
                                @endif
                            </div>
                        </div>

                    </div>


            </div>
        </div>
        @include('admin.layouts.inc.alerts')
        <div class="row mt-10">
            <div class="col-lg-12">
                <div class="card card-custom">
                    <div class="card-body">
                        <div class="table-responsive">
                            @if ($errors->any())
                                <label class="font-size-h6 font-weight-bolder text-danger">{{ $errors->first() }}</label>
                                <br>
                            @endif

                            <table class="table table-hover dt-table  icon-color grid-table kt-margin-0">
                                <thead>
                                    {{-- <th>User Type</th> --}}
                                    <th>File Name</th>
                                    <th>Date</th>
                                    <th>Document</th>
                                    <th>Actions</th>

                                </thead>
                                <tbody>
                                    @foreach ($compensation_docs as $compensation_doc)
                                        <tr>
                                            <td>{{ $compensation_doc->name }}</td>
                                            <td>{{ $compensation_doc->date }}</td>
                                            <td><a href="{{ asset_url('media/compansation_document/'.$compensation_doc->document,true) }}" target="_blank">View File</a></td>
                                            <td>
                                              @if($compensation_doc->completion==null)
                                              <label for="completion" data-title="completion" data-id={{ $compensation_doc->id }} class="btn-sm btn btn-outline-primary mr-3 file_label">
                                                <i class="flaticon-attachment"></i>
                                                Completion
                                              </label>
                                              @else
                                              <a class="btn-sm btn btn-outline-primary mr-3" download href="{{ asset_url('media/compansation_document/'.$compensation_doc->completion,true) }}">
                                                <i class="flaticon-download"></i>
                                                Completion
                                              </a>
                                              @endif

                                              @if($compensation_doc->exception==null)
                                              <label for="exception" data-title="exception" data-id={{ $compensation_doc->id }} class="btn-sm btn btn-outline-primary mr-3 file_label">
                                                <i class="flaticon-attachment"></i>
                                                Exception
                                              </label>
                                              @else
                                              <a class="btn-sm btn btn-outline-primary mr-3" download href="{{ asset_url('media/compansation_document/'.$compensation_doc->exception,true) }}">
                                                <i class="flaticon-download"></i>
                                                Exception
                                              </a>
                                              @endif

                                              @if($compensation_doc->check_no==null)
                                              <label for="check_no" data-title="check_no" data-id={{ $compensation_doc->id }} class="btn-sm btn btn-outline-primary mr-3 file_label">
                                                <i class="flaticon-attachment"></i>
                                                Check no
                                              </label>
                                              @else
                                              <a class="btn-sm btn btn-outline-primary mr-3" download href="{{ asset_url('media/compansation_document/'.$compensation_doc->check_no,true) }}">
                                                <i class="flaticon-download"></i>
                                                Check no
                                              </a>
                                              @endif

                                            </td>
                                            {{-- <td>{{ Crypto::decryptData($participant->first_name, Crypto::getAwsEncryptionKey()) }}</td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" class="emailChangeForm">
            @csrf
            <div class="modal fade" id="exampleModalCenter" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create Compensation Document</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div data-scroll="true" data-height="250">
                            <div class="form-group">
                                <label>File Name</label>
                                <input type="text" name="file_name" required class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" required class="form-control">
                            </div>

                            <div class="form-group">
                                <label>Attach Document</label>
                               <input type="file" name="document" required accept=".pdf">

                        </div>
                    </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary font-weight-bold">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form id="file_upload" action="{{ admin_url('documentUpload', true) }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="file" name="completion" id="completion" class="file_select" accept=".pdf">
            <input type="file" name="exception" id="exception" class="file_select" accept=".pdf">
            <input type="file" name="check_no" id="check_no" class="file_select" accept=".pdf">
            <input type="hidden" name="title">
            <input type="hidden" name="id">
        </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('.file_label').click(function(){
                $('input[name="title"]').val($(this).data('title'));
                $('input[name="id"]').val($(this).data('id'));
            });
            $('.file_select').change(function(){
                $('#file_upload').submit();
            });

        })
    </script>
@endsection
