@include('admin.layouts.inc.head')
@php
$user = Auth::user();
$full_name = trim($user->first_name . ' ', $user->last_name);
@endphp
<!-- begin:: Header Mobile -->


<div id="kt_header_mobile" class="header-mobile">
    <!--begin::Logo-->
    <a href="{{ admin_url('') }}">
        <img alt="Logo" src="{{ asset('assets/admin/images/'.opt('header_logo')) }}" class="logo-default max-h-30px" />
    </a>
    <!--end::Logo-->
    <!--begin::Toolbar-->
    <div class="d-flex align-items-center">
        <button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
            <span></span>
        </button>
    </div>
    <!--end::Toolbar-->
</div>

<!-- end:: Header Mobile -->

<div class="d-flex flex-column flex-root">
    <!--begin::Page-->

    <!--begin::Aside-->
    <div class="aside aside-left d-flex aside-fixed" id="">

        <!--begin::Secondary-->
        <div class="aside-secondary d-flex flex-row-fluid">
            <!--begin::Workspace-->
            <div class="aside-workspace scroll scroll-push my-2">
                <!--begin::Tab Content-->
                <div class="tab-content">

                    <!--begin::Tab Pane-->
                    <div class="tab-pane fade show active" id="kt_aside_tab_2">
                        @include('admin.layouts.inc.left_side')
                    </div>
                </div>
            </div>
        </div>
    </div>
