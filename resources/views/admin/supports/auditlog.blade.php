<form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data">
        @csrf
        @include('admin.layouts.inc.stickybar')
        <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
        <!-- begin:: Content -->
        <div class="row mt-10">
            <div class="col-lg-9">
                <div class="card-custom card">
                    <div class="card-header">
                        <h3 class="card-title"> {{ $_info->title }} Form </h3>
                    </div>
                    <div class="card-body">

                    <h1 style="color:#434349;" class="col-4">Audit Logs</h1><br><br>

                        <h2 style="color:#434349;" class="col-4">Ticket Preview:</h2><br>

                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Select Owner') }}:</label>
                            <div class="col-6">
                                <select name="owner_username" id="owner_username" class="form-control m-select2" disabled>
                                    <option value="">Select Owner</option>
                                    <?php echo selectBox('SELECT id, email FROM users', $row->owner_username); ?>
                                </select>
                            </div>
                        </div>
                                                
                        <div class="form-group row">
                            <label for="user_type_id" class="col-3 col-form-label text-right required">{{ __('Company Name') }}:</label>
                            <div class="col-6">
                                <select name="company_name" id="company_name" class="form-control m-select2" disabled>
                                    <option value="">Select Company Name</option>
                                    @foreach ($companies as $company)
                                        <option {{ $row->company_name == $company->id ? 'selected' : '' }} value="{{ $company->id }}">{{ Crypto::decryptData($company->client_name, Crypto::getAwsEncryptionKey()) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                      
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label text-right required">Ticket#:</label>
                            <div class="col-6">
                                <input value="{{$row->ticket_no}}" class="form-control" type="text" disabled>
                                <input name="ticket_no" value="{{$row->ticket_no}}" class="form-control" type="text" hidden>

                            </div>
                        </div>
                        
                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">Title/Subject:</label>
                            <div class="col-lg-7">
                                <input type="text" class="form-control"  value="{{ old('subject', $row->subject) }}" disabled>
                                <input type="text" name="subject" value="{{ old('subject', $row->subject) }}" class="form-control" placeholder="Subject" hidden>

                            </div>
                        </div>
                          
                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">Description:</label>
                            <div class="col-lg-7">
                                <input type="text" class="form-control"  value="{{ old('description', $row->description) }}" disabled>
                                <input type="text" name="description" value="{{ old('description', $row->description) }}" class="form-control" placeholder="description" hidden>
                            </div>
                        </div>

                        <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">Created:</label>
                            <div class="col-lg-7">
                                <input type="text" value="{{ old('created', $row->created) }}"  class="form-control" disabled>
                                <input type="text" name="created" value="{{ old('created', $row->created) }}" class="form-control" placeholder="description" hidden>

                            </div>
                        </div>
                        
                         <div class="form-group row text-right required">
                            <label for="module" class="col-3 col-form-label text-right required">Updated:</label>
                            <div class="col-lg-7">
                                <input type="text" value="{{ old('updated', $row->updated) }}" class="form-control" placeholder="description" disabled>
                                <input type="text" name="updated" value="{{ old('updated', $row->updated) }}" class="form-control" placeholder="description" hidden>

                            </div>
                        </div>
                    
                        <br><br><h1 style="color:#434349;" class="col-4">Acitivity Logs:</h1><br>

                        <table class="table table-striped">
                            <tbody>
                                @foreach($complete_logs as $log)
                                    <tr>
                                        <td scope="col">{{$log}}</td>                                                               
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

