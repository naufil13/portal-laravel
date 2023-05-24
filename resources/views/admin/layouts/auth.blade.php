<!DOCTYPE html>

<html lang="en">
<!--begin::Head-->

<head>

    <meta charset="utf-8" />
    <title>{{opt('site_title')}} | Login</title>
    <meta name="description" content="Singin page example" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="canonical" href="https://keenthemes.com/metronic" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Page Custom Styles(used by this page)-->
    <link href="{{ asset('theme/assets/css/pages/login/login-3.css') }}" rel="stylesheet" type="text/css" />
    <!--end::Page Custom Styles-->
    <!--begin::Global Theme Styles(used by all pages)-->
    <link href="{{ asset('theme/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('theme/assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('theme/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/admin/media/support_assets/style.css') }}" rel="stylesheet" type="text/css" />

    <!--end::Global Theme Styles-->
    <!--begin::Layout Themes(used by all pages)-->
    <!--end::Layout Themes-->
    {{-- <link rel="shortcut icon" href="assets/media/logos/favicon.ico" /> --}}
</head>
<!--end::Head-->
<!--begin::Body-->

<body id="kt_body"
    class="header-mobile-fixed subheader-enabled aside-enabled aside-fixed aside-secondary-enabled page-loading">
    <!--begin::Main-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Login-->
        <div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid">
            <!--begin::Aside-->
            <div class="login-aside d-flex flex-column flex-row-auto"
                style="background-image: url('{{ asset_url('media/bg/bg-3.jpg', 1) }}');">
                <!--begin::Aside Top-->
                <div class="d-flex flex-column-auto flex-column h-100 justify-content-around">
                    <div>
                        <!--begin::Aside header-->
                        <a href="#" class="login-logo pt-lg-25 pb-10 pr-10 pl-4">
                            <img src="{{ asset_url('media/logos/admin_logo.png', 1) }}" class="max-h-70px w-40 ml-7"
                                alt="" />
                        </a>

                        <!--end::Aside header-->
                        <!--begin::Aside Title-->
                        <h3 class="font-weight-bolder pl-10 pr-10 line-height-lg mt-10 mb-4"
                            style="font-size: 24px; color:#82849f">Comprehensive Technology platform
                        </h3>
                        <p class=" pl-10 pr-10" style="font-size: 14px; color: #525152;">
                            Our Unified Platform with SSO capabilities is your gateway to decentralized trials.
                        </p>
                    </div>
                    <div>

                        <p class=" pl-10 pr-10"><a href="mailto:{{opt('company_email')}}"
                                class="d-flex align-items-center"><i class="far fa-envelope"
                                    style="color: #82849f;"></i> <span class="pl-2 font-weight-bold"
                                    style="color: #222;">{{opt('company_email')}}</span></a></p>
                        <p class=" pl-10 pr-10"><a href="tel:+16128508005" class="d-flex align-items-center"><i
                                    class="fa fa-phone-alt"></i style="color: #82849f;">{{opt('company_phone')}}<span
                                    class="pl-2 font-weight-bold" style="color: #222;"></span></a></p>

                        {{-- <div class="pl-10 pr-10 mt-8"><a href="{{ url('guestSupport') }}"
                                class="border border-1 border-light-dark d-inline-flex font-size-sm justify-content-center px-3 py-2 rounded"
                                style="color: #82849f;"><i class="fas fa-circle mr-2"></i>Need assistance?</a></div> --}}
                        <!--end::Aside Title-->
                    </div>
                </div>
            </div>

            @yield('content')

        </div>
        <!--end::Login-->
    </div>
    <!--end::Main-->

    <script>
        var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";
    </script>
    <!--begin::Global Config(global config for global JS scripts)-->
    <script>
        var KTAppSettings = {
            "breakpoints": {
                "sm": 576,
                "md": 768,
                "lg": 992,
                "xl": 1200,
                "xxl": 1200
            },
            "colors": {
                "theme": {
                    "base": {
                        "white": "#ffffff",
                        "primary": "#1BC5BD",
                        "secondary": "#E5EAEE",
                        "success": "#1BC5BD",
                        "info": "#6993FF",
                        "warning": "#FFA800",
                        "danger": "#F64E60",
                        "light": "#F3F6F9",
                        "dark": "#212121"
                    },
                    "light": {
                        "white": "#ffffff",
                        "primary": "#1BC5BD",
                        "secondary": "#ECF0F3",
                        "success": "#C9F7F5",
                        "info": "#E1E9FF",
                        "warning": "#FFF4DE",
                        "danger": "#FFE2E5",
                        "light": "#F3F6F9",
                        "dark": "#D6D6E0"
                    },
                    "inverse": {
                        "white": "#ffffff",
                        "primary": "#ffffff",
                        "secondary": "#212121",
                        "success": "#ffffff",
                        "info": "#ffffff",
                        "warning": "#ffffff",
                        "danger": "#ffffff",
                        "light": "#464E5F",
                        "dark": "#ffffff"
                    }
                },
                "gray": {
                    "gray-100": "#F3F6F9",
                    "gray-200": "#ECF0F3",
                    "gray-300": "#E5EAEE",
                    "gray-400": "#D6D6E0",
                    "gray-500": "#B5B5C3",
                    "gray-600": "#80808F",
                    "gray-700": "#464E5F",
                    "gray-800": "#1B283F",
                    "gray-900": "#212121"
                }
            },
            "font-family": "Poppins"
        };
    </script>
    <!--end::Global Config-->
    <!--begin::Global Theme Bundle(used by all pages)-->
    <script src="{{ asset('theme/assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('theme/assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
    <script src="{{ asset('theme/assets/js/scripts.bundle.js') }}"></script>
    <!--end::Global Theme Bundle-->
    <!--begin::Page Scripts(used by this page)-->

    <script src="{{ asset('assets/support_assets/js/widgets7a50.js?v=7.2.7') }}"></script>

    @yield('scripts')
    <!--end::Page Scripts-->
</body>
<!--end::Body-->

</html>
