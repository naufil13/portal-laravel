@include('admin.layouts.inc.head')
<style>
    .kt-error-v1{background-position:center;background-repeat:no-repeat;background-attachment:fixed;background-size:cover}.kt-error-v1 .kt-error-v1__container .kt-error-v1__number{font-size:150px;margin-left:80px;margin-top:9rem;font-weight:700;color:#595d6e}.kt-error-v1 .kt-error-v1__container .kt-error-v1__desc{font-size:1.5rem;margin-left:80px;color:#74788d}@media (max-width:768px){.kt-error-v1 .kt-error-v1__container .kt-error-v1__number{margin:120px 0 0 3rem;font-size:8rem}.kt-error-v1 .kt-error-v1__container .kt-error-v1__desc{margin-left:3rem;padding-right:.5rem}}
</style>
<!-- begin:: Page -->
<div class="kt-grid kt-grid--ver kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid  kt-error-v1" style="background-image: url({{ asset_url('media/error/bg1.jpg', true) }});">
        <div class="kt-error-v1__container">
            <h1 class="kt-error-v1__number">404</h1>
            <p class="kt-error-v1__desc">
                OOPS! Something went wrong here
            </p>
        </div>
    </div>
</div>
