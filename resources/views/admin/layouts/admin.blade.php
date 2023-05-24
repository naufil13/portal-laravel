@include('admin.layouts.inc.header')



@include('admin.layouts.inc.breadcrumb')


<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
    <!--begin::Content-->
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">


        <div class="container">


            @yield('content')

        </div>

    </div>
</div>




@include('admin.layouts.inc.footer')
@include('admin.layouts.inc.modal')
