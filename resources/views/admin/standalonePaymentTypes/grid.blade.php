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
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Created By</th>
                                <th>updated By</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                @foreach ($payment_types as $payment_type)
                                    <tr>
                                        <td>{{ $payment_type->payment_type }}</td>
                                        <td>{{ $payment_type->amount }}</td>
                                        <td>{{ $payment_type->created_by }}</td>
                                        <td>{{ $payment_type->updated_by }}</td>
                                        <td>
                                            <a data-skin="dark" data-toggle="kt-tooltip"
                                                class="btn btn-sm btn-clean btn-icon mr-2" action="edit" title="Edit"
                                                href="{{ url('admin/standalone_payment_types/form', $payment_type->id) }}"><i
                                                    class="flaticon2-pen text-primary"></i></a>
                                            @if (count($payment_type->PaymentDetails))
                                                <a data-skin="dark" data-toggle="kt-tooltip"
                                                    class="btn btn-sm btn-clean btn-icon mr-2" action="delete"
                                                    title="Delete"
                                                    href="{{ url('admin/standalone_payment_types/delete', $payment_type->id) }}"><i
                                                        class="flaticon2-trash text-danger"></i></a>
                                            @endif
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
                columnDefs: [{
                    targets: [2, 3],
                    className: "truncate"
                }],
            });
        });
    </script>
@endsection
