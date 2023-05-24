<div class="footer py-4 d-flex flex-lg-column text-center" id="kt_footer">
    <!--begin::Container-->
    <div class="">
        <!--begin::Copyright-->
        <div class=" text-dark order-2 order-md-1">
            <span class="text-muted font-weight-bold mr-2"> Copyright {{ date('Y') }} {{opt('site_title')}} - All Rights
                Reserved.</span>
            {{-- <span class="text-muted font-weight-bold mr-2">Powered by <img
                    src="{{ asset_url('media/logos/admin_logo.png', 1) }}"
                    style="width: 74px;"></span> --}}
            {{-- <a href="#" target="_blank" class="text-dark-75 text-hover-primary">{{ get_option('developer') }}</a> --}}
        </div>
        <!--end::Copyright-->

    </div>
    <!--end::Container-->
</div>
<!--end::Footer-->


<!-- begin::Global Config(global config for global JS sciprts) -->
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
<!-- end::Global Config -->

<script src="{{ asset('theme/assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('theme/assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
<script src="{{ asset('theme/assets/js/scripts.bundle.js') }}"></script>
<script src="{{ asset('theme/assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Page Vendors-->
<!--begin::Page Scripts(used by this page)-->
<script src="{{ asset('theme/assets/js/pages/crud/datatables/basic/scrollable.js') }}"></script>
<!--end::Global Theme Bundle-->
<script src="{{ asset_url('js/jquery.checkboxes.js', true) }}" type="text/javascript"></script>

<script src="{{ asset('theme/assets/plugins/custom/tinymce/tinymce.bundle.js') }}"></script>
<script src="{{ asset('theme/assets/js/pages/crud/forms/editors/tinymce.js') }}"></script>

{{-- <script src="{{ asset('theme/assets/plugins/custom/jquery.validate.min.js') }}"></script> --}}
<script src="{{ asset('theme/assets/plugins/custom/jstree/jstree.bundle.js?v=7.2.8') }}"></script>
<script src="{{ asset('theme/assets/js/pages/features/miscellaneous/treeview.js?v=7.2.8') }}"></script>
<script src="{{ asset('theme/assets/js/pages/crud/file-upload/image-input.js?v=7.2.8') }}"></script>

<script src="{{ asset_url('js/custom.js', true) }}" type="text/javascript"></script>
<script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>

<script>
    // $(document).ready(function() {
    //     $('.dt-table').find('.search-tr').remove();
    //     $('.dt-table').dataTable({
    //         "order": [],
    //     });
    //     $(window).scroll(function() {
    //         var subheader_top = $('#subheader').offset().top;
    //         var scroll = $(window).scrollTop();
    //         if (subheader_top > scroll) {
    //             $('#subheader').removeClass('sticky_header');
    //         } else {
    //             $('#subheader').addClass('sticky_header');
    //         }
    //     });

    //     CKEDITOR.replace('message');
    // });

    $(window).on("load", function() {
        $('.spin-loader').fadeOut();
    });
</script>


@yield('scripts')

</body>

</html>
