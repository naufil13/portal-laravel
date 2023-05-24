<!-- begin:: breadcrumb -->
<div class="subheader grid__item" id="subheader">

    <div class="w-100 ">


        @if (count($form_buttons) > 0)
            <div class="card card-custom">

                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">{{ getUri(2) == "user_info" ? "Profile":getModuleDetail()->title }}</h3>
                    </div>
                    <div class="card-toolbar">

                        @php
                            $Form_btn = new Form_btn();
                            echo $Form_btn->buttons($form_buttons);
                        @endphp


                    </div>
                </div>

            </div>



        @endif





    </div>
</div>
@include('admin.layouts.inc.alerts')
<!-- end:: breadcrumb -->
