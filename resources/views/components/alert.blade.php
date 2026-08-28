@if ($errors->any())
    <div class="container alert alert-error">
        <strong>Vui lòng kiểm tra lại:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="container alert alert-success">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="container alert alert-error">{{ session('error') }}</div>
@endif
