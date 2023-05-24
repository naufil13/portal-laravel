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

                        <h1 style="color:#434349;" class="col-4">Comments:</h1><br>
                        
                        <div class="form-group row text-right required">
                            <input name="ticket_no" value="{{$ticket}}" class="form-control" type="text" hidden>
                            <label for="module" class="col-3 col-form-label text-right required">Comments:</label>
                            <div class="col-lg-7">
                                <textarea  cols="" name="comments" id="comments" rows="5" class="form-control col-sm-12" placeholder="Comments"></textarea>
                            </div>
                        </div>

                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">File Upload: (Multiple files can be uploaded, Allowed File Types are [jpeg, jpg, png, pdf, docx])</label>
                            <div class="col-lg-7">
                                <input type="file" name="comments_filename[]" multiple class="form-control-file mt-3" id="exampleFormControlFile1">
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
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" href="#"><i class="la la-plus"></i> Save & New</a>
                                <a class="dropdown-item" href="#"><i class="la la-undo"></i> Save & Close</a>
                            </div>
                        </div><br><br><br><br>

                        <table class="table table-striped" style="text-align:center;">
                            <thead>
                                <tr>
                                    <th scope="col">FileName</th>
                                    <th scope="col">Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($comment_comments as $key=> $comment_comment)
                                    <tr>
                                        <td scope="col">{{$key}}</td>   
                                        @if(count($comment_comment) > 1)
                                            <td>
                                            <select class="form-control m-select2">
                                                <option>View Files</option>
                                                    @foreach($comment_comment as $files)
                                                        <option>{{$files->filename}}</option>
                                                    @endforeach  
                                            </select>
                                            </td>
                                        @else
                                            @foreach($comment_comment as $files)
                                                <td>{{$files->filename}}</td>     
                                            @endforeach 
                                        @endif                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:: Content -->
</form>

