@php
$pass_data['form_buttons'] = ['save', 'back'];
@endphp

    <form action="{{ admin_url('store_comment', true) }}" method="post" enctype="multipart/form-data">
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

                    <h1 style="color:#434349;" class="col-4">Files</h1><br><br>

                    <h4 style="color:#434349;" class="col-4">Ticket Uploads: </h4>
                        <table class="table table-striped" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th scope="col">FileName</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Published Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                @if($file->submitted_from == "ticket_form")
                                <tr>
                                    <td scope="col">{{$file->filename}}</td> 
                                    <td scope="col">{{$file->created_by}}</td>                       
                                    <td scope="col">{{$file->created}}</td>                                             
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                        
                        <br><br><br>
                    
                    <h4 style="color:#434349;" class="col-4">Assign Uploads: </h4>
                        <table class="table table-striped" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th scope="col">FileName</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Published Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                @if($file->submitted_from == "assign_form")
                                <tr>
                                    <td scope="col">{{$file->filename}}</td> 
                                    <td scope="col">{{$file->created_by}}</td>                       
                                    <td scope="col">{{$file->created}}</td>                                             
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>

                        <br><br><br>
                    
                    <h4 style="color:#434349;" class="col-4">Comments Uploads: </h4>
                        <table class="table table-striped" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th scope="col">FileName</th>
                                    <th scope="col">Created By</th>
                                    <th scope="col">Published Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($files as $file)
                                @if($file->submitted_from == "comment_form")
                                <tr>
                                    <td scope="col">{{$file->filename}}</td> 
                                    <td scope="col">{{$file->created_by}}</td>                       
                                    <td scope="col">{{$file->created}}</td>                                             
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:: Content -->
</form>

