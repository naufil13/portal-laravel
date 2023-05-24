@php
$params = [
'title' => 'Refresh',
'class' => 'btn btn-label-danger btn-md btn-sm',
'href' => admin_url('{_module}'),
'icon_cls' => 'la la-refresh',
];
Form_btn::add_button('refresh', $params, true);
$form_buttons = ['new', 'delete', 'import', 'export', 'refresh'];
@endphp
@extends('admin.layouts.admin')

@section('content')

<style type="text/css">
.truncate {
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
<form action="{{ admin_url('', true) }}" method="post" enctype="multipart/form-data" id="kt_form_1">
    @csrf
    @include('admin.layouts.inc.stickybar', compact('form_buttons'))
    <div class="row mt-10">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="card-body">
                    <table class="table table-hover icon-color grid-table kt-margin-0" id="zero-confg">
                        <thead>
                            <th>Id</th>
                            <th>Activity</th>
                            <th>User</th>
                            <th>Table</th>
                            <th>Response</th>
                            <th>Actions</th>
                        </thead>
                        <tbody>
                            @foreach ($Response as $log)
                            <tr>
                                <td>{{$log->id}}</td>
                                <td>{{$log->activity}}</td>
                                <td>{{$log->email}}</td>
                                <td>{{$log->table}}</td>
                                <td>{{$log->response}}</td>
                                <td>
                                    <a href="{{ url('admin/portal_logs/view',$log->id) }}">
                                        <i class=" la la-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

{{-- Scripts --}}
@section('scripts')

<script type="text/javascript">
$(document).ready(function() {
    table = $("#zero-confg");
    table.dataTable({
        order: [],
        columnDefs: [{
            targets: [2, 3, 4],
            className: "truncate"
        }],
    });
});
</script>


@endsection