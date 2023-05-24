<div class="kt-portlet" data-ktportlet="true" id="kt_portlet_tools_1">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                   <i class="flaticon-graphic"></i>
                </span>
                <h3 class="kt-portlet__head-title"> {{ __('Statistics') }} </h3>
            </div>
        </div>
        @include('admin.layouts.inc.portlet')
    </div>

    <div class="kt-portlet__body kt-padding-0">

        <table class="table table-bordered table-striped kt-margin-0">
            <thead>
            <tr>
                <th>NOA</th>
                <th>Member</th>
                <th colspan="2">Database</th>
                <th colspan="2">Registration</th>
                <th colspan="2">Screening/Completion</th>
                <th colspan="2">License</th>
            </tr>
            </thead>
            <tbody class="-table-info">
            @php
            $association_id = getVar('association_id');
            if($association_id > 0) {
                $_associations = App\Association::where('id', $association_id)->get();
            } else {
                $_associations = App\Association::all();
            }
            @endphp
            @foreach($_associations as $_association)
                @php
                    $member = App\Member::where('association_id', $_association->id)->count();
                @endphp
            <tr>
                <td><b>{{ $_association->name }}</b></td>
                <td>{{ number_format($member) }}</td>

                <td>{{ number_format($member) }}</td>
                <td>{{ number_format($member) }}</td>

                <td>80</td>
                <td>20</td>

                <td>60</td>
                <td>20</td>

                <td>50</td>
                <td>30</td>
            </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>
