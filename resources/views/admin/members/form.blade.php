@php
    $form_buttons = ['save', 'view', '-delete', 'back'];
    if($row->id > 0) {
        $directories = \App\Directory::where(['association_id' => $row->association_id, 'member_id' => $row->id])->get();
    }
@endphp
@extends('admin.layouts.admin')

@section('content')
    <style>
        .clone_container .btn.btn-icon.btn-sm{
            height: 1.5rem;
            width: 1.5rem;
            float: left;
        }
    </style>
    <form action="{{ admin_url('store', true) }}" method="post" enctype="multipart/form-data" id="associations">
        @csrf
        @include('admin.layouts.inc.stickybar', compact('form_buttons'))
        <input type="hidden" name="id" value="{{ old('id', $row->id) }}">
        <!-- begin:: Content -->


        <div class="row">
            <div class="col-lg-9">
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon-file"></i> </span>
                                <h3 class="kt-portlet__head-title"> {{ $_info->title }} Form </h3>
                            </div>
                        </div>
                        @include('admin.layouts.inc.portlet')
                    </div>

                    <div class="kt-portlet__body">
                        <div class="mt10"></div>

                        <div class="form-group row">
                            <label for="name" class="col-2 col-form-label required">{{ __('Association') }}:</label>
                            <div class="col-6">
                                <select name="association_id" id="association_id" class="form-control m-select2">
                                    <option value="">- Select -</option>
                                    <?php echo selectBox("SELECT id, name FROM associations", $row->association_id) ?>
                                </select>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="name" class="col-2 col-form-label required">{{ __('Name') }}:</label>
                            <div class="col-6">
                                <input type="text" name="name" id="name" class="form-control" placeholder="{{ __('Name') }}" value="{{ old('name', $row->name) }}"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="company" class="col-2 col-form-label required">{{ __('Company') }}:</label>
                            <div class="col-6">
                                <input type="text" name="company" id="company" class="form-control" placeholder="{{ __('Company') }}" value="{{ old('company', $row->company) }}"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="joining_date" class="col-2 col-form-label required">{{ __('Joining Date') }}:</label>
                            <div class="col-2">
                                <input type="text" name="joining_date" id="joining_date" readonly class="form-control datepicker" placeholder="{{ __('Joining Date') }}" value="{{ old('joining_date', $row->joining_date) }}"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="website" class="col-2 col-form-label -required">{{ __('Website') }}:</label>
                            <div class="col-6">
                                <input type="text" name="website" id="website" class="form-control" placeholder="{{ __('Website') }}" value="{{ old('website', $row->website) }}"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="address" class="col-2 col-form-label required">{{ __('Address') }}:</label>
                            <div class="col-6">
                                <textarea name="address" id="address" class="form-control" placeholder="{{ __('Address') }}" cols="30" rows="5">{{ old('address', $row->address) }}</textarea>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="city" class="col-2 col-form-label text-right">{{ __('City') }}:</label>
                            <div class="col-6">
                                <input type="text" name="city" id="city" class="form-control" placeholder="{{ __('City') }}" value="{{ old('city', $row->city) }}"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row">
                            <label for="country" class="col-2 col-form-label text-right">{{ __('Country') }}:</label>
                            <div class="col-6">
                                <input type="text" name="country" id="country" class="form-control" placeholder="{{ __('Country') }}" value="{{ old('country', $row->country) }}"/>
                            </div>
                        </div>

                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row my-repeater">
                            <label for="email" class="col-2 col-form-label required">{{ __('Email') }}:</label>
                            <div class="col-10" data-repeater-list="">
                                @php
                                    if(empty($row->emails)){
                                        $emails = json_decode("[{}]");
                                    } else{
                                        $emails = json_decode($row->emails);
                                    }
                                @endphp
                                @foreach($emails as $email)
                                    <div data-repeater-item>
                                        <div class="form-group row">
                                            <div class="col-7">
                                                <input type="text" name="email" id="emails" class="form-control" placeholder="{{ __('Email') }}" value="{{ $email }}"/>
                                            </div>
                                            <div class="col-5">
                                                <a href="javascript:void(0);" data-repeater-delete="" class="btn-sm btn btn-label-danger -btn-icon"><i class="la la-trash-o"></i> Delete</a>
                                                @if($loop->first)
                                                    <a href="javascript:void(0);" data-repeater-create="" class="btn-sm btn btn-label-success -btn-icon"><i class="la la-plus"></i> Add More</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md" style="margin: 10px 0;"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>
                        <div class="form-group row my-repeater">
                            <label for="phone" class="col-2 col-form-label required">{{ __('Phone') }}:</label>
                            <div class="col-10 " data-repeater-list="phone">
                                @php
                                    if(empty($row->emails)){
                                        $phones = json_decode("[{}]");
                                    } else{
                                        $phones = json_decode($row->phones);
                                    }
                                @endphp
                                @foreach($phones as $phone)
                                    <div data-repeater-item data-callback="my_repeat">
                                        <div class="form-group row">
                                            <div class="col-7">
                                                <input type="text" name="phones" id="phones" class="form-control" placeholder="{{ __('Phone') }}" value="{{ $phone }}"/>
                                            </div>
                                            <div class="col-5">
                                                <a href="javascript:void(0);" data-repeater-delete="" class="btn-sm btn btn-label-danger -btn-icon"><i class="la la-trash-o"></i> Delete</a>
                                                @if($loop->first)
                                                    <a href="javascript:void(0);" data-repeater-create="" class="btn-sm btn btn-label-success -btn-icon"><i class="la la-plus"></i> Add More</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md" style="margin: 10px 0;"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    <div class="kt-portlet__foot">
                        @php
                            $Form_btn = new Form_btn();
                            echo $Form_btn->buttons($form_buttons);
                        @endphp
                    </div>

                </div>
            </div>
            <div class="col-lg-3">

                <div class="kt-portlet" data-ktportlet="true">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon-file "></i> </span>
                                <h3 class="kt-portlet__head-title"> {{ __('Logo') }} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="mt10"></div>

                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <input disabled type="hidden" name="logo--rm" value="{{ old('logo', $row->logo) }}">
                                @php
                                    $file_input = '<input type="file" name="logo" accept=".png, .jpg, .jpeg">';
                                    $thumb_url = asset_url("{$_info->module}/" . $row->logo);
                                    $delete_img_url = admin_url('ajax/delete_img/' . $row->id . '/logo', true);
                                    echo thumb_box($file_input, $thumb_url, $delete_img_url);
                                @endphp
                                <br>
                                <span class="form-text text-muted">"jpg, png, bmp, gif" file extension's</span>
                            </div>
                        </div>

                    </div>
                </div>


                @php
                    $data = json_decode($row->data);
                @endphp
                <div class="kt-portlet" data-ktportlet="true">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon-computer "></i> </span>
                                <h3 class="kt-portlet__head-title"> {{ __('Stats') }} </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_license" class="col-form-label">{{ __('License') }}</label>
                                <select name="data[license]" id="data_license" class="form-control m_selectpicker">
                                    <?php
                                    $OP = ['Trial', 'Standard', 'Premium'];
                                    echo selectBox(array_combine($OP, $OP), old('data.license', $data->license)); ?>
                                </select>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_days" class="col-form-label">{{ __('Days') }}</label>
                                <input type="number" min="0" name="data[days]" id="data_days" value="{{ old('data.days', $data->days) }}" placeholder="{{ __('Days') }}" class="form-control"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_screening" class="col-form-label">{{ __('Screening') }}</label>
                                <div class="input-group">
                                    <input type="text" name="data[screening]" id="data_screening" value="{{ old('data.screening', $data->screening) }}" placeholder="{{ __('Screening') }}" class="form-control"/>
                                    <div class="input-group-append"><span class="input-group-text" id="basic-addon1">%</span></div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_login" class="col-form-label">{{ __('Login Stats Monthly') }}</label>
                                <div class="input-group">
                                    <input type="text" name="data[login]" id="data_login" value="{{ old('data.login', $data->login) }}" placeholder="{{ __('Login Stats Monthly') }}" class="form-control"/>
                                    <div class="input-group-append"><span class="input-group-text"><i class="la la-mouse-pointer la-2x"></i></span></div>
                                </div>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_date_transfer" class="col-form-label">{{ __('Date of Transfer') }}</label>
                                <input type="text" name="data[date_transfer]" id="data_date_transfer" value="{{ old('data.date_transfer', $data->date_transfer) }}"  placeholder="{{ __('Date of Transfer') }}" class="form-control datepicker"/>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_database" class="col-form-label">{{ __('Database') }}</label>
                                {{--<input type="text" name="data[database]" id="date_database" value="{{ old('data.database', $data->database) }}" placeholder="{{ __('Database') }}" class="form-control"/>--}}
                                <select name="data[database]" id="data_database" class="form-control m_selectpicker">
                                    <?php
                                    $OP = ['Yes', 'No'];
                                    echo selectBox(array_combine($OP, $OP), old('data.database', $data->database)); ?>
                                </select>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-sm"></div>
                        <div class="form-group row">
                            <div class="col-12">
                                <label for="data_flag" class="col-form-label">{{ __('Flag') }}</label>
                                <input type="text" name="data[flag]" id="date_flag" value="{{ old('data.flag', $data->flag) }}" placeholder="{{ __('Flag') }}" class="form-control"/>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </form>
    <!--end::Form-->
@endsection

{{-- Scripts --}}
@section('scripts')
    <script>

        $("form#associations").validate({
            // define validation rules
            rules: {
                'name': {
                    required: true,
                },
                'joining_date': {
                    required: true,
                },
                //'website': {required: true,},
                'address': {
                    required: true,
                },
                'email': {
                    required: true, email: true,
                },
                'phone': {
                    required: true,
                },
            },
            /*messages: {
            'name' : {required: 'Name is required',},'joining_date' : {required: 'Joining Date is required',},'website' : {required: 'Website is required',},'address' : {required: 'Address is required',},'email' : {required: 'Email is required',email: 'Email is not valid',},'phone' : {required: 'Phone is required',},    },*/
            //display error alert on form submit
            invalidHandler: function (event, validator) {
                KTUtil.scrollTop();
                //validator.errorList[0].element.focus();
            },

            submitHandler: function (form) {
                form.submit();
            }

        });
    </script>@endsection
