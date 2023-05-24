<!-- begin:: alerts -->
@php
$alert_types = ['success', 'error' => 'danger', 'warning', 'primary', 'info', 'brand'];
@endphp


@foreach ($alert_types as $a_key => $alert_type)
    @php
        $key = is_int($a_key) ? $alert_type : $a_key;
    @endphp
    @if (session()->has($key))
        <div class="alert alert-{{ $alert_type }}">
            {{ session($key) }}
        </div>
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- end:: alerts -->
