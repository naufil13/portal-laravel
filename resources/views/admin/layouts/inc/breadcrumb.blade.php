<!--begin::Subheader-->
<div class="mb-3 bg-white shadow-sm subheader py-3 py-lg-8 subheader-transparent container-fluid" id="kt_subheader">
    <div class="w-100 d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
        <!--begin::Info-->
        <div class="d-flex align-items-center mr-1">
            <!--begin::Page Heading-->
            <div class="d-flex align-items-baseline flex-wrap mr-5">
                <!--begin::Page Title-->
                @php
                    // dd($form_button);
                @endphp
                @if (is_countable($form_button) && count($form_buttons) == 0)
                    <h2 class="d-flex align-items-center text-dark font-weight-bold my-1 mr-3">
                        {{-- {{ getModuleDetail()->title }} --}}
                    </h2>
                @endif
                <!--end::Page Title-->
            </div>
            <!--end::Page Heading-->
        </div>
        <!--end::Info-->

        <!--begin::Toolbar-->
        <div class="align-items-center d-flex">
            <a class="mr-3">
                <span href="#" class="align-items-end d-lg-flex d-none flex-column mr-2">
                    <span
                        class="text-dark-50 font-weight-bolder">{{ Crypto::decryptData(Auth::user()->first_name, Crypto::getAwsEncryptionKey()) }}</span>
                    <span class="font-italic text-muted">{{ Auth::user()['usertype']->user_type }}</span>

                </span>
            </a>

            <!-- <a href="#" class="navi-link">
                <span class="navi-text">
                    <span class="label cursor-pointer label-xl label-inline label-light-danger " href="{{ route('logout') }}" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                        {{ __('Sign Out') }} <i class="fa fa-sign-out"></i>
                    </span>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </span>
            </a> -->


            <!--begin::Languages-->
            <div class="dropdown">
                <!--begin::Toggle-->
                <div class="topbar-item bg-secondary-o-100" data-toggle="dropdown" data-offset="10px,0px">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
                <!--end::Toggle-->
                <!--begin::Dropdown-->
                <div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right">
                    <!--begin::Nav-->
                    <ul class="navi navi-hover py-4">
                        <!--begin::Item-->

                        @if (Auth::user()['usertype']->user_type != 'Participant')
                            <li class="navi-item">
                                <a href="{{ admin_url('user_info') }}" class="navi-link"><span
                                        class="navi-text">Profile</span></a>
                            </li>
                        @endif
			@if (Auth::user()['usertype']->user_type == 'Participant')
				<li class="navi-item">
                                <a href="https://beta-portal.evolutionrx.us/assets/admin/media/knowledge_hub/ePRO%20-%20Participant%20User%20Manual%20V_1.6%20_%2008-25-22.pdf" class="navi-link"><span
                                        class="navi-text">User Guide</span></a>
                            </li>
			@endif


                        <li class="navi-item">
                            <a class="navi-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                <span class="navi-text">
                                    <span>
                                        {{ __('Log Out') }}
                                    </span>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                </span>
                            </a>
                        </li>
                        <!--end::Item-->

                    </ul>
                    <!--end::Nav-->
                </div>
                <!--end::Dropdown-->
            </div>
            <!--end::Languages-->



        </div>
    </div>
    <!--end::Dropdown-->

</div>
<!--end::Toolbar-->
</div>
</div>
<!--end::Subheader-->
