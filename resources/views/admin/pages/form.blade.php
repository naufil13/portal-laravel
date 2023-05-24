@extends('admin.layouts.admin')
@section('title', 'Pages')

@section('content')
{{-- Content --}}
<form action="" method="post" enctype="multipart/form-data" id="kt_form_1">
    <!-- begin:: breadcrumb -->
    <div class="kt-subheader kt-grid__item" id="kt_subheader">

        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">Page Form</h3>
                <span class="kt-subheader__separator kt-hidden"></span>
                <div class="kt-subheader__breadcrumbs">
                    <a href="" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="#" class="kt-subheader__breadcrumbs-link"> Page Form </a>
                </div>
            </div>

            <div class="kt-subheader__toolbar">
                <div class="btn-group">
                    <button type="submit" class="btn btn-md btn-danger btn-sm">
                        <i class="la la-save"></i> Submit Now </button>
                    <button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#"><i class="la la-plus"></i> Save & New</a>
                        <a class="dropdown-item" href="#"><i class="la la-undo"></i> Save & Close</a>
                    </div>
                </div>
                &nbsp;&nbsp;
                <a href="/page_grid.php" class="btn btn-secondary btn-sm"><i class="la la-undo"></i> Back</a>
            </div>
        </div>
    </div>
    <!-- end:: breadcrumb -->
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-lg-9">
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon-file"></i> </span>
                                <h3 class="kt-portlet__head-title"> Fill Out Below Form </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group">
                                <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-angle-down"></i></a>
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__body">
                        <div class="mt10"></div>
                        <div class="form-group row">
                            <label for="title" class="col-2 col-form-label text-right">Page Title: <span class="required">*</span></label>
                            <div class="col-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <label class="kt-checkbox kt-checkbox--single" data-skin="dark" data-toggle="kt-tooltip" title="check to show page title">
                                                <input name="show_hide_title" type="checkbox"> <span></span>
                                            </label>
                                        </span>
                                    </div>
                                    <input name="title" type="text" value="" class="form-control form-control-lg" placeholder="Enter page title" id="title">
                                </div>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="friendly_url" class="col-2 col-form-label text-right">Friendly URL: <span class="required">*</span></label>
                            <div class="col-10">
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">http://www.websigntist.com/</span></div>
                                    <input name="friendly_url" type="text" value="" class="form-control" placeholder="Enter friendly url" id="friendly_url">
                                </div>
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <label for="sub_heading" class="col-2 col-form-label text-right">Sub Heading:</label>
                            <div class="col-10">
                                <input name="sub_heading" type="text" value="" class="form-control" placeholder="Enter sub heading" id="sub_heading">
                            </div>
                        </div>
                        <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                        <div class="form-group row">
                            <div class="col-12">
                                <textarea name="address" class="editor"></textarea>
                            </div>
                        </div>
                        <div class="mb-2"></div>
                    </div>
                    <!--<div class="kt-portlet__foot">
                        <div class="btn-group">
                            <button type="button" class="btn btn-md btn-brand btn-sm"><i class="la la-save"></i>Submit Now</button>
                            <button type="button" class="btn btn-sm btn-brand dropdown-toggle dropdown-toggle-split"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-left">
                                <a class="dropdown-item" href="#"><i class="la la-plus"></i> Save & New</a>
                                <a class="dropdown-item" href="#"><i class="la la-undo"></i> Save & Close</a>
                            </div>
                        </div>
                    &nbsp;&nbsp;
                    <button type="reset" class="btn btn-secondary btn-sm"><i class="la la-undo"></i> Back</button>
                    </div>-->
                </div>

                <div class="kt-portlet --kt-portlet--collapsed" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon2-rocket-1"></i> </span>
                                <h3 class="kt-portlet__head-title"> meta informaton </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group">
                                <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-angle-down"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet__content">
                            <div class="form-group row">
                                <label for="meta_title" class="col-2 col-form-label text-right">meta title: <span class="required">*</span></label>
                                <div class="col-10">
                                    <input name="meta_title" class="form-control" type="text" value="" placeholder="Enter meta title" id="meta_title">
                                </div>
                            </div>
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                            <div class="form-group row">
                                <label for="meta_keywords" class="col-2 col-form-label text-right">meta keywords:</label>
                                <div class="col-10">
                                    <textarea name="meta_keywords" class="form-control kt_autosize_1" rows="2" placeholder="Write meta keywords"></textarea>
                                </div>
                            </div>
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                            <div class="form-group row">
                                <label for="meta_description" class="col-2 col-form-label text-right">meta description:</label>
                                <div class="col-10">
                                    <textarea name="meta_description" class="form-control kt_autosize_1" rows="5" placeholder="Write meta description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!--col-9-->

            <!--======= begin::right sidebar -->
            <div class="col-lg-3">
                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon2-protected"></i> </span>
                                <h3 class="kt-portlet__head-title"> User Status </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet__content">
                            <div class="form-group">
                                <select class="custom-select form-control">
                                    <option value="Published"> Published </option>
                                    <option value="Unpublished"> Unpublished </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon2-image-file"></i> </span>
                                <h3 class="kt-portlet__head-title"> Page Top Image </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="form-group topimg">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">Choose file</label>
                            </div>
                            <div class="mt-3">
                                <a data-fancybox="gallery" href="/assets/images/bg-2.jpg">
                                    <img src="/assets/images/bg-2.jpg" class="img-fluid img_center img-thumbnail" data-skin="dark" data-toggle="kt-tooltip" title="click to zoom">
                                </a>
                                <button type="button" class="img_delete btn-danger" data-skin="dark" data-toggle="kt-tooltip" title="remove image">
                                    <i class="flaticon-delete"></i> Delete Image
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon2-layers-1"></i> </span>
                                <h3 class="kt-portlet__head-title"> page attributes </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet__content">
                            <div class="form-group">
                                <select class="custom-select form-control">
                                    <option value=""> /Parent Page</option>
                                    <option value=""> About Us </option>
                                </select>
                            </div>
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                            <div class="form-group">
                                <select class="custom-select form-control">
                                    <option value="">-- Choose Template --</option>
                                    <option value=""> Detault</option>
                                    <option value=""> Full Width</option>
                                    <option value=""> Container With</option>
                                </select>
                            </div>
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                            <div class="form-group">
                                <select class="custom-select form-control">
                                    <option value="">-- Show in Menu --</option>
                                    <option value=""> Yes </option>
                                    <option value=""> No </option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon2-dashboard"></i> </span>
                                <h3 class="kt-portlet__head-title"> Ordering </h3>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet__content">
                            <input value="" name="ordering" placeholder="odering 1 - 9" type="text" id="kt_touchspin_5" type="text" class="form-control bootstrap-touchspin-vertical-btn">
                        </div>
                    </div>
                </div>

                <div class="kt-portlet kt-portlet--collapsed" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon2-hourglass-1"></i> </span>
                                <h3 class="kt-portlet__head-title"> Coming Soon </h3>
                            </div>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="kt-portlet__head-group">
                                <a href="#" data-ktportlet-tool="toggle" class="btn btn-sm btn-icon btn-clean btn-icon-md"><i class="la la-angle-down"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="kt-portlet__content">
                            <div class="form-group">
                                <label>Inline Radioes</label>
                                <div class="kt-radio-inline">
                                    <label class="kt-radio kt-radio--bold kt-radio--brand">
                                        <input type="radio" name="s_status" value="active"> Active
                                        <span></span>
                                    </label>

                                    <label class="kt-radio kt-radio--bold kt-radio--danger">
                                        <input type="radio" name="s_status" value="inactive" checked> Inctive
                                        <span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                            <input name="soon" class="form-control" type="text" value="" placeholder="Enter title" id="soon">
                            <div class="kt-separator kt-separator--border-dashed kt-separator--space-md"></div>

                            <div class="form-group topimg">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>

                                <div class="mt-3">
                                    <img src="/assets/images/no_image.jpg" class="img-fluid img-thumbnail img_center" alt="img">
                                    <button type="button" class="img_delete btn-danger" data-skin="dark" data-toggle="kt-tooltip" title="remove image">
                                        <i class="flaticon-delete"></i> Delete Image
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--======= end::right sidebar -->
        </div>
    </div>
    <!-- end:: Content -->
</form>

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection
