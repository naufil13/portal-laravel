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
                            <label for="module" class="col-3 col-form-label required">Test Name: <span
                                    class="text-danger">*</span></label>
                            <div class="col-6">
                                <input name="name" value="{{ old('name', $row->name) }}" class="form-control"
                                    type="text" placeholder="Enter Sample Type" required>
                            </div>
                        </div>

                        <div class="row hideOnCheck">
                            <div class="col-xl-2">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Test Type: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-lg forRequired" name="names[]"
                                        placeholder="Name" required="required"
                                        value="{{ old('name', $sample_types_rel[0]->name) }}">
                                </div>
                            </div>
                            <div class="col-xl-2">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Minimum: <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg forRequired" name="minimum[]"
                                        placeholder="Minimum" required="required"
                                        value="{{ old('minimum', $sample_types_rel[0]->min_range) }}">
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Maximum: <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control form-control-lg forRequired" name="maximum[]"
                                        placeholder="Maximum" required="required"
                                        value="{{ old('maximum', $sample_types_rel[0]->max_range) }}">
                                </div>
                            </div>

                            <div class="col-xl-2">
                                <div class="form-group fv-plugins-icon-container">
                                    <label>Operator: <span class="text-danger">*</span></label>
                                    <select name="operators[]" class="form-control form-control-lg forRequired">
                                        <option value="=" @if ($sample_types_rel[0]->operator === '=') selected @endif>=</option>
                                        <option value=">" @if ($sample_types_rel[0]->operator === '>') selected @endif>></option>
                                        <option value=">=" @if ($sample_types_rel[0]->operator === '>=') selected @endif>>=</option>
                                        <option value="<="@if ($sample_types_rel[0]->operator === '<=') selected @endif><=</option>
                                        <option value="<" @if ($sample_types_rel[0]->operator === '<') selected @endif><</option>
                                    </select>
                                    {{-- <input type="text" class="form-control form-control-lg forRequired"
                                        name="operators[]" placeholder="Operator" required="required"
                                        value="{{ old('operators', $sample_types_rel[0]->payment_rate) }}"> --}}
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
                        `
                        <div class="col-xl-2" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Name:</label>\
                                <input type="text" class="form-control form-control-lg" name="names[]" placeholder="Name">\
                            </div>\
                        </div>
                        <div class="col-xl-2" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Minimum:</label>\
                                <input type="number" class="form-control form-control-lg" name="minimum[]" placeholder="Minimum">\
                            </div>\
                        </div>\
                        <div class="col-xl-2" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Maximum:</label>\
                                <input type="number" class="form-control form-control-lg" name="maximum[]"
                                    placeholder="Maximum">\
                            </div>\
                        </div>\
                        <div class="col-xl-2" id="` + y + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Operator:</label>\
                                <select name="operators[]" class="form-control form-control-lg forRequired">
                                    <option value="=">=</option>
                                    <option value=">">></option>
                                    <option value=">=">>=</option>
                                    <option value="<="><=</option>
                                    <option value="<"><</option>
                                </select>
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
                console.log(sampleMixMax);
                for ($a = 1; $a < countOfSampleMinMax; $a++) {
                    var h = countOfSampleMinMax + $a + 1;
                    $(wrapper).append(
                        `
                        <div class="col-xl-2" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Name:</label>\
                                <input type="text" class="form-control form-control-lg" name="names[]"
                                value="` +
                                sampleMixMax[$a].name + `"  placeholder="Name">\
                            </div>\
                        </div>
                        <div class="col-xl-2" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Minimum:</label>\
                                <input type="number" class="form-control form-control-lg" name="minimum[]" value="` +
                        sampleMixMax[$a].min_range + `" placeholder="Minimum">\
                            </div>\
                        </div>\
                        <div class="col-xl-2" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Maximum:</label>\
                                <input type="number" class="form-control form-control-lg" name="maximum[]"
                                   value="` +
                        sampleMixMax[$a].max_range + `" placeholder="Maximum">\
                            </div>\
                        </div>\
                        <div class="col-xl-2" id="` + h + `">\
                            <div class="form-group fv-plugins-icon-container">\
                                <label>Operator:</label>\

                                <select name="operators[]" class="form-control form-control-lg forRequired">
                                    <option value="=" @if (sampleMixMax[$a].operator === '=') selected @endif>=</option>
                                    <option value=">" @if (sampleMixMax[$a].operator === '>') selected @endif>></option>
                                    <option value=">=" @if (sampleMixMax[$a].operator === '>=') selected @endif>>=</option>
                                    <option value="<="@if (sampleMixMax[$a].operator === '<=') selected @endif><=</option>
                                    <option value="<" @if (sampleMixMax[$a].operator === '<') selected @endif><</option>
                                </select>

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
