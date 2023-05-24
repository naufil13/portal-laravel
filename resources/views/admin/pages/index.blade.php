@extends('admin.layouts.admin')
@section('title', 'Pages')

@section('content')
    {{-- Content --}}
    <!-- begin:: Content -->
    <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
        <div class="row">
            <div class="col-lg-12">
                @php
                    $grid = new Grid();
                    $grid->init("SELECT * FROM pages WHERE 1");
                    echo $grid->showGrid();
                @endphp

                <div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <div class="kt-portlet__head-label">
                                <span class="kt-portlet__head-icon"> <i class="flaticon-list-2"></i> </span>
                                <h3 class="kt-portlet__head-title"> Information </h3>
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
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped icon-color">
                                <thead>
                                <tr>
                                    <th>
                                        <label class="kt-checkbox kt-checkbox--solid">
                                            <input type="checkbox" id="checkAll">
                                            <span></span>
                                        </label>
                                    </th>
                                    <th width="20">ID</th>
                                    <th>Image</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone #</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="120">Action</th>
                                </tr>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th><input type="search" name="" placeholder="Search..." class="form-control"></th>
                                    <th>
                                        <button class="btn btn-label-brand btn-bold">
                                            <i class="flaticon-search"></i> Search
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th>
                                        <label class="kt-checkbox kt-checkbox--solid">
                                            <input type="checkbox" id="checkItem">
                                            <span></span>
                                        </label>
                                    </th>
                                    <td>1</td>
                                    <td width="100">
                                        <a data-fancybox="gallery" href="/assets/images/100_13.jpg">
                                            <img src="/assets/images/100_13.jpg" class="img-fluid img_center kt-img-rounded shadow-sm" width="50" data-skin="dark" data-toggle="kt-tooltip" title="click to zoom"></a>
                                    </td>
                                    <td>john-son</td>
                                    <td>websigntist@gmail.com</td>
                                    <td>+923002563325</td>
                                    <td>
                                        <span class="kt-badge kt-badge--danger kt-badge--inline kt-badge--pill --kt-badge--rounded">INACTIVE</span>
                                    </td>
                                    <td>2019-08-23 20:17:15</td>
                                    <td>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Edit">
                                            <i class="la la-edit"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Delete">
                                            <i class="la la-trash"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Active">
                                            <i class="la la-check-circle kt-font-success"></i> </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label class="kt-checkbox kt-checkbox--solid">
                                            <input type="checkbox" id="checkItem">
                                            <span></span>
                                        </label>
                                    </th>
                                    <td>2</td>
                                    <td>
                                        <a data-fancybox="gallery" href="/assets/images/100_13.jpg">
                                            <img src="/assets/images/100_13.jpg" class="img-fluid img_center kt-img-rounded shadow-sm" width="50" data-skin="dark" data-toggle="kt-tooltip" title="click to zoom"></a>
                                    </td>
                                    <td>ali-javed</td>
                                    <td>websigntist@gmail.com</td>
                                    <td>+923002563325</td>
                                    <td>
                                        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill">ACTIVE</span>
                                    </td>
                                    <td>2019-08-23 20:17:15</td>
                                    <td>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Edit">
                                            <i class="la la-edit"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Delete">
                                            <i class="la la-trash"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Active">
                                            <i class="la la-check-circle kt-font-success"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Inactive">
                                            <i class="la la-times-circle kt-font-danger"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Download">
                                            <i class="la la-cloud-download kt-font-warning"></i> </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label class="kt-checkbox kt-checkbox--solid">
                                            <input type="checkbox" id="checkItem">
                                            <span></span>
                                        </label>
                                    </th>
                                    <td>3</td>
                                    <td>
                                        <a data-fancybox="gallery" href="/assets/images/100_13.jpg">
                                            <img src="/assets/images/100_13.jpg" class="img-fluid img_center kt-img-rounded shadow-sm" width="50" data-skin="dark" data-toggle="kt-tooltip" title="click to zoom"></a>
                                    </td>
                                    <td>John Doe</td>
                                    <td>john@gmail.com</td>
                                    <td>+923002522235</td>
                                    <td>
                                        <span class="kt-badge kt-badge--brand kt-badge--inline kt-badge--pill">PENDIND</span>
                                    </td>
                                    <td>2019-08-23 20:17:15</td>
                                    <td>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Edit">
                                            <i class="la la-edit"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Delete">
                                            <i class="la la-trash"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Active">
                                            <i class="la la-check-circle kt-font-success"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Inactive">
                                            <i class="la la-times-circle kt-font-danger"></i> </a>
                                        <a href="" data-skin="dark" data-toggle="kt-tooltip" title="Download">
                                            <i class="la la-cloud-download kt-font-warning"></i> </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="kt-portlet__foot">
                        <!--begin: Pagination(sm)-->
                        <div class="kt-pagination kt-pagination--sm kt-pagination--danger">
                            <ul class="kt-pagination__links">
                                <li class="kt-pagination__link--first">
                                    <a href="#"><i class="fa fa-angle-double-left kt-font-danger"></i></a>
                                </li>
                                <li class="kt-pagination__link--next">
                                    <a href="#"><i class="fa fa-angle-left kt-font-danger"></i></a>
                                </li>
                                <li><a href="#">1</a></li>
                                <li class="kt-pagination__link--active"><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">4</a></li>
                                <li><a href="#">5</a></li>
                                <li><a href="#">...</a></li>
                                <li><a href="#">25</a></li>
                                <li><a href="#">26</a></li>
                                <li class="kt-pagination__link--prev">
                                    <a href="#"><i class="fa fa-angle-right kt-font-danger"></i></a>
                                </li>
                                <li class="kt-pagination__link--last">
                                    <a href="#"><i class="fa fa-angle-double-right kt-font-danger"></i></a>
                                </li>
                            </ul>

                            <div class="kt-pagination__toolbar">
                                <select class="form-control kt-font-danger" style="width: 60px;">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="pagination__desc">
                                        Displaying 10 of 230 records
                                    </span>
                            </div>
                        </div>
                        <!--end: Pagination-->
                        &nbsp;&nbsp;
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- end:: Content -->

@endsection

{{-- Scripts --}}
@section('scripts')

@endsection
