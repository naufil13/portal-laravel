<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://preview.keenthemes.com/metronic8/demo1/assets/plugins/global/plugins.bundle.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://preview.keenthemes.com/metronic8/demo1/assets/css/style.bundle.css">
    <title>Guest Support</title>
    <link rel="stylesheet" href="{{ asset('assets/admin/media/support_assets/style.css') }}">
</head>

<body>
    <div class="dashbaord-cont pt-15">
        <div class=" container">

            @if($errors->any()) <div class="alert alert-danger">
                <p><strong>Opps Something went wrong</strong></p>
                <ul> @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
            </div> @endif
            <section class="align-items-center d-flex row">
                <div class="left-cont col-lg-5 p-0">

                    <div class="ee-img mb-10"><img src="{{asset('assets/admin/media/support_assets/img.png')}}" alt="Need Assistance" class="img-fluid w-100"></div>
                    <p class="text-muted">
                        Need assistance? Submit a request below and we will get to work! </p>

                    <p class="text-muted">If you think the problem is a bug and you don't find it in system status or known issues, raise it here and we will collect full details to report it to development.
                    </p>
                    <a href="mailto:{{opt('company_email')}}" class="d-flex flex-wrap mb-4 mt-15 text-dark" style="">
                        <i class="fa fa-envelope me-3" style="font-size: 17px;"></i>
                        {{opt('company_email')}}
                    </a>

                    <a href="tel:+16128508005" class="text-dark">
                        <i class="fa fa-phone-alt me-3" style="font-size: 17px;">{{opt('company_phone')}}</i>

                    </a>

                </div><!-- left -->
                <div class="right-cont col-lg-7 p-0">
                    <div class="card p-5 rounded-0">
                        <div class="border-0 card-header">
                            <h3 class="card-title">{{opt('site_title')}} Support</h3>
                        </div>
                        <!--begin::Form-->
                        <form class="form" action="{{ url('guestSupport') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="card-body">

                                <div class="row">
                                    <div class="col-md-6 form-group mb-6">
                                        <label class="mb-2">Full Name:</label>
                                        <input type="text" name="name" class="form-control" placeholder="eg: John Smith">
                                    </div>

                                    <div class="col-md-6 form-group mb-6">
                                        <label class="mb-2">Company Name:</label>
                                        <input type="text" name="company_name" class="form-control" placeholder="eg: ABC Company">
                                    </div>

                                    <div class="col-md-6 form-group mb-6">
                                        <label class="mb-2">Company Email:</label>
                                        <input type="email" name="company_email" class="form-control" placeholder="name@company.com">
                                    </div>

                                    <div class="col-md-6 form-group mb-6">
                                        <label class="mb-2">Phone Number</label>
                                        <input type="tel" name="phone_no" class="form-control" placeholder="+1 654 55656">
                                    </div>


                                    <div class="col-md-12 form-group mb-6">
                                        <label class="mb-2">I am having trouble with?</label>
                                        <select name="issue" class="form-control">
                                            <?php echo selectBox('SELECT id, name FROM guest_support_issues', old('issue', $row->issue)); ?>
                                        </select>
                                    </div>


                                    <div class="col-md-12 form-group mb-6">
                                        <label class="mb-2">Issue Description * (Maximum 500 Characters are allowed)</label>
                                        <textarea name="issue_desc" id="" cols="4" rows="4" class="form-control"></textarea>
                                    </div>


                                    <div class="col-md-12 form-group mb-6">
                                        <label class="mb-2">File Upload</label>
                                        <input type="file" class="form-control" name="file[]" multiple>
                                        <span class="text-muted">Max file size is 1MB and max number of files is 5</span>
                                    </div>


                                    <div class="col-md-12">
                                        <button class="btn btn-success w-100">Submit a Request</button>
                                    </div>


                                </div>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>
                </div><!-- right -->
            </section>
        </div><!-- container -->
    </div><!-- dashboard -->
</body>

</html>
