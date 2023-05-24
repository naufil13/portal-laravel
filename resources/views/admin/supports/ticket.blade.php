@php
$pass_data['form_buttons'] = ['save', 'back'];
@endphp

    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data">
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
                        <h1 style="color:#434349;" class="col-4">User Info:</h1><br>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Ticket Source:</label>
                            <div class="col-6">
                                @if(isset($row->id))
                                    <input type="text" value="{{$sources_edit}}" class="form-control" disabled>
                                    <input type="text" name="ticket_source_id" value="{{$row->ticket_source_id}}" class="form-control" hidden>
                                @else
                                    <select name="ticket_source_id" id="ticket_source_id" class="form-control m-select2">
                                        <option value="">Select Source</option>
                                        <?php echo selectBox('SELECT id, src_name FROM helpdesk_support_source', $row->ticket_source_id); ?>
                                    </select>
                                  @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Creator's Name:</label>
                            <div class="col-6">
                                @if(isset($row->id))
                                    <input value="{{$row->creator_username}}" class="form-control" type="text" disabled>
                                    <input name="creator_username" id="creator_username" value="{{$login_user}}" class="form-control" type="text" hidden>
                                @else
                                    <input value="{{$login_user}}" class="form-control" type="text" disabled>
                                    <input name="creator_username" id="creator_username" value="{{$login_user}}" class="form-control" type="text" hidden>
                                @endif
                                
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Select Owner') }}:</label>
                            <div class="col-6">
                            @if(isset($row->id))    
                                <input type="text" name="owner_username" value="{{$owners_edit}}" class="form-control" disabled >
                                <input type="text" name="owner_username" value="{{$row->owner_username}}" class="form-control" hidden >
                            @else
                                <select name="owner_username" id="owner_username" class="form-control m-select2">
                                    <option value="">Select Owner</option>
                                    <?php echo selectBox('SELECT id, email FROM users', $row->owner_username); ?>
                                </select>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">First Name:</label>
                            <div class="col-6">
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $row->first_name) }}" class="form-control first_name" placeholder="Enter First Name" disabled>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $row->first_name) }}" class="form-control first_name" placeholder="Enter First Name" hidden>
                            </div>
                        </div>

                          <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Last Name:</label>
                            <div class="col-6">
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $row->last_name) }}" class="form-control last_name" placeholder="Enter Last Name" disabled>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $row->last_name) }}" class="form-control last_name" placeholder="Enter Last Name" hidden>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Company Name') }}:</label>
                            <div class="col-6">
                            @if(isset($row->id))    
                                <input type="text" name="company_name" value="{{ Crypto::decryptData($companies_edit, Crypto::getAwsEncryptionKey()) }}" class="form-control" disabled >
                                <input type="text" name="company_name" value="{{$row->company_name}}" class="form-control" hidden >
                            @else
                            <select name="company_name" id="company_name" class="form-control m-select2">
                                    <option value="">Select Company Name</option>
                                    @foreach ($companies as $company)
                                        <option {{ $row->company_name == $company->id ? 'selected' : '' }} value="{{ $company->id }}">{{ Crypto::decryptData($company->client_name, Crypto::getAwsEncryptionKey()) }}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Division Name') }}:</label>
                            <div class="col-6">
                            @if(isset($row->id))    
                                <input type="text" name="division_name" value="{{$divisions_edit}}" class="form-control" disabled >
                                <input type="text" name="division_name" value="{{$row->division_name}}" class="form-control" hidden >
                            @else
                                <select name="division_name" id="division_name" class="form-control m-select2">
                                    <option value="">Select Division Name</option>
                                    <?php echo selectBox('SELECT id, division_name FROM divisions', $row->division_name); ?>
                                </select>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Department Name') }}:</label>
                            <div class="col-6">
                            @if(isset($row->id))    
                                <input type="text" name="department_name" value="{{$departments_edit}}" class="form-control" disabled >
                                <input type="text" name="department_name" value="{{$row->department_name}}" class="form-control" hidden >
                            @else
                                <select name="department_name" id="department_name" class="form-control m-select2">
                                    <option value="">Select Department Name</option>
                                    <?php echo selectBox('SELECT id, department_name FROM departments', $row->department_name); ?>
                                </select>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Select Clinical Study') }}:</label>
                            <div class="col-6">
                            @if(isset($row->id))    
                                <input type="text" name="clinical_study" value="{{$studies_edit}}" class="form-control" disabled>
                                <input type="text" name="clinical_study" value="{{$row->clinical_study}}" class="form-control" hidden >
                            @else
                            <select name="clinical_study" id="clinical_study" class="form-control m-select2">
                                    <option value="">Select Clinical Study</option>
                                    <?php echo selectBox('SELECT id, study_name FROM trials', $row->clinical_study); ?>
                                </select>
                            @endif
                            </div>                            
                        </div>

                        <br><h1 style="color:#434349;" class="col-4">Ticket Info:</h1><br>
                        
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Ticket#:</label>
                            <div class="col-6">
                            @if(isset($row->id))
                                <input value="{{$row->ticket_no}}" class="form-control" type="text" disabled >    
                                <input name="ticket_no" value="{{$row->ticket_no}}" class="form-control" type="text" hidden>    
                            @else
                                <input value="{{$ticket}}" class="form-control" type="text" readonly>
                                <input name="ticket_no" value="{{$ticket}}" class="form-control" type="text" hidden>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Select Priority') }}:</label>
                            <div class="col-6">
                                <select name="priorities_id" id="priorities_id" class="form-control m-select2">
                                    <option value="">Select Priority</option>
                                    <?php echo selectBox('SELECT id, priority FROM helpdesk_support_priorities', $row->priorities_id); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Select Category') }}:</label>
                            <div class="col-6">
                                <select name="general_issues_id" id="general_issues_id" class="form-control m-select2">
                                    <option value="">Select Category</option>
                                    <?php echo selectBox('SELECT id, issue FROM helpdesk_general_issues', $row->general_issues_id); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Ticket Status:</label>
                            <div class="col-6">
                            @if(isset($row->id))
                                <input value="{{$statuses_edit}}" class="form-control" type="text" disabled>
                                <input value="{{$statuses_edit}}" class="form-control" type="text" hidden>
                            @else
                                <input value="Open" class="form-control" type="text" readonly >
                            @endif                           
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Assinged To:</label>
                            <div class="col-6">
                            @if(isset($row->id))
                                <input value="{{$assignto}}" class="form-control" type="text" disabled >
                                <input value="{{$assignto}}" class="form-control" type="text" hidden>
                            @else
                                <input value="Unassigned" class="form-control" type="text" readonly>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">Title/Subject:</label>
                            <div class="col-lg-7">
                                <input type="text" name="subject" value="{{ old('subject', $row->subject) }}" class="form-control" placeholder="Subject">
                            </div>
                        </div>

                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">Issue Description:</label>
                                <div class="col-lg-7">
                                    <textarea  cols="" name="description" value="{{ old('description', $row->description) }}" rows="5" class="form-control col-sm-12" placeholder="Issue Description">{{$row->description}}</textarea>
                                </div>
                        </div>
           
                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">File Upload: (Multiple files can be uploaded, Allowed File Types are [jpeg, jpg, png, pdf, docx])</label>
                            <div class="col-lg-7">
                                <input type="file" multiple name="support_file[]" value="$row->support_file" class="form-control-file mt-3" id="exampleFormControlFile1">
                            </div>
                        </div>
                        
                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">File Description:</label>
                                <div class="col-lg-7">
                                    <textarea  cols="" rows="3" name="files_description" value="{{ old('files_description', $row->files_description) }}" class="form-control col-sm-12" placeholder="File Description"></textarea>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end:: Content -->
</form>

