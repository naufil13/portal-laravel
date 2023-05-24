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
                            <label for="module" class="col-3 col-form-label required">Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="name" value="{{ old('name', $row->name) }}" class="form-control"
                                    type="text" placeholder="Enter Sample Type">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label required">Measurement: <span
                                    class="text-danger">*</span></label>
                            <div class="col-9 col-form-label">
                                <div class="radio-inline">
                                    @foreach ($sample_type_units as $sample_type_unit)
                                        <label class="radio radio-outline radio-outline-2x radio-primary">
                                            <input type="radio" name="measurement" value="{{ $sample_type_unit->id }}" <?php echo $sample_type_unit->id == $row->sample_type_unit_id ? 'checked' : ''; ?>>
                                            <span></span>{{ $sample_type_unit->unit }}</label>
                                    @endforeach
                                    <a href="javascript:void(0)" style="text-decoration:underline" data-target=".addunit" data-toggle="modal">add more</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label required">No Compensation:</label>
                            <div class="col-9 col-form-label">
                                <div class="checkbox-inline">
                                    <label class="checkbox checkbox-lg">
                                        <input type="hidden" name="no_compensation" value="No">
                                        <input id="no_compensation" type="checkbox" class="onChecks"
                                            name="no_compensation" value="Yes" <?php echo $row->no_compensation == 'Yes' ? 'checked' : ''; ?>>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label required">No Collection:</label>
                            <div class="col-9 col-form-label">
                                <div class="checkbox-inline">
                                    <label class="checkbox checkbox-lg">
                                        <input type="hidden" name="no_collection" value="No">
                                        <input id="no_collection" type="checkbox" class="onChecks"
                                            name="no_collection" value="Yes" <?php echo $row->no_collection == 'Yes' ? 'checked' : ''; ?>>
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row hideOnCheck">
                            <div class="col-xl-3">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Minimum: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg forRequired" name="minimum[]"
                                        placeholder="Minimum" required="required"
                                        value="{{ old('minimum', $sample_types_rel[0]->minimum) }}">
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Maximum: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg forRequired" name="maximum[]"
                                        placeholder="Maximum" required="required"
                                        value="{{ old('maximum', $sample_types_rel[0]->maximum) }}">
                                </div>
                            </div>

                            <div class="col-xl-3">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Payment Rate: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg forRequired"
                                        name="payment_rate[]" placeholder="Payment Rate" required="required"
                                        value="{{ old('payment_rate', $sample_types_rel[0]->payment_rate) }}">
                                </div>
                            </div>

                            <div class="col-md-3 d-flex align-items-stretch add_button">
                                <div class="d-flex flex-grow-1 align-items-center bg-hover-light p-4 rounded">
                                    <div class="mr-4 flex-shrink-0 text-center" style="width: 40px;">
                                        <i class="icon-3x text-dark-50 flaticon-add-circular-button"></i>
                                    </div>
                                    <div class="text-muted">Add More Fields, only 4 more fields allowed</div>
                                </div>
                            </div>
                        </div>

                        <div class="row field_wrapper hideOnCheck">

                        </div>

                        <div class=" form-group
                            row">
                            <label for="module" class="col-3 col-form-label required">QLL Limit:</label>
                            <div class="col-6">
                                <input name="limit" value="{{ old('limit', $row->limit) }}" class="form-control"
                                    type="text" placeholder="Enter Sample Type Limit">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="module" class="col-3 col-form-label required">Duration (Days):</label>
                            <div class="col-6">
                                <input name="duration" value="{{ old('duration', $row->duration) }}"
                                    class="form-control" type="text" placeholder="Enter Sample Type Limit Duration">
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

    <div class="modal fade addunit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Unit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ admin_url('sample_types/unit_store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="unit">Unit</label>
                            <input type="text" name="unit" class="form-control" placeholder="Unit" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

{{-- Scripts --}}

@section('scripts')

    <script>
        $(document).on('change', '.onChecks', function() {
            if (this.checked) {
                // checkbox is checked
                $('.forRequired').removeAttr('required');
                $('.forRequired').val('');
                $('.hideOnCheck').hide();
            } else {
                $('.hideOnCheck').show();
                $('.forRequired').prop('required', true);
            }
        });

        $(document).ready(function() {
            if ($('#no_compensation').attr('checked')) {
                $('.forRequired').removeAttr('required');
                $('.forRequired').val('');
                $('.hideOnCheck').hide();
            }
        });

        var y = 1;
        $(document).ready(function() {
            var maxField = 5;
            var addButton = $('.add_button');
            var wrapper = $('.field_wrapper');
            var x = 1;
            $(addButton).click(function() {
                if (x < maxField) {
                    x++;
                    y = y + 1;
                    $(wrapper).append(
                        `<div class="col-xl-3" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Minimum:</label>\
                                <input type="text" class="form-control form-control-lg" name="minimum[]" placeholder="Minimum">\
                            </div>\
                        </div>\
                        <div class="col-xl-3" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Maximum:</label>\
                                <input type="text" class="form-control form-control-lg" name="maximum[]"
                                    placeholder="Maximum">\
                            </div>\
                        </div>\
                        <div class="col-xl-3" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Payment Rate:</label>\
                                <input type="text" class="form-control form-control-lg" name="payment_rate[]"
                                    placeholder="Payment Rate">\
                            </div>\
                        </div>\
                        <div class="col-md-3 d-flex align-items-stretch remove_button" id="` + y + `">\
                            <div class="d-flex flex-grow-1 align-items-center bg-hover-light p-4 rounded">\
                                <div class="mr-4 flex-shrink-0 text-center" style="width: 40px;">\
                                    <i class="icon-3x text-dark-50 flaticon-cancel"></i>\
                                </div>\
                                <div class="text-muted">Delete Fields</div>\
                            </div>\
                        </div>\
                        `
                    );
                }
            });

            $(wrapper).on('click', '.remove_button', function(e) {
                e.preventDefault();
                var div_ids = $(this).attr('id');
                $('div[id=' + div_ids + ']').remove();
                x--;
            });

            // For Edit
            $(document).ready(function() {
                var countOfSampleMinMax = <?php echo count($sample_types_rel); ?>;
                var sampleMixMax = <?php echo $sample_types_rel; ?>;
                for ($a = 1; $a < countOfSampleMinMax; $a++) {
                    var h = countOfSampleMinMax + $a + 1;
                    $(wrapper).append(
                        `<div class="col-xl-3" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Minimum:</label>\
                                <input type="text" class="form-control form-control-lg" name="minimum[]" value="` +
                        sampleMixMax[$a].minimum + `" placeholder="Minimum">\
                            </div>\
                        </div>\
                        <div class="col-xl-3" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Maximum:</label>\
                                <input type="text" class="form-control form-control-lg" name="maximum[]"
                                   value="` +
                        sampleMixMax[$a].maximum + `" placeholder="Maximum">\
                            </div>\
                        </div>\
                        <div class="col-xl-3" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Payment Rate:</label>\
                                <input type="text" class="form-control form-control-lg" name="payment_rate[]"
                                   value="` +
                        sampleMixMax[$a].payment_rate + `" placeholder="Payment Rate">\
                            </div>\
                        </div>\
                        <div class="col-md-3 d-flex align-items-stretch remove_button" id="` + h + `">\
                            <div class="d-flex flex-grow-1 align-items-center bg-hover-light p-4 rounded">\
                                <div class="mr-4 flex-shrink-0 text-center" style="width: 40px;">\
                                    <i class="icon-3x text-dark-50 flaticon-cancel"></i>\
                                </div>\
                                <div class="text-muted">Delete Fields</div>\
                            </div>\
                        </div>\
                        `
                    );
                }
            });

        });
    </script>

@endsection
