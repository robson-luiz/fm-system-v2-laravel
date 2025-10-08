@if (session('success'))
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: "Pronto!",
                html: "{{ session('success') }}",
                icon: "success"
            });
        });
    </script> --}}
    <div class="alert-success">
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: "Erro!",
                html: "{{ session('error') }}",
                icon: "error"
            });
        });
    </script> --}}
    <div class="alert-danger">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    {{-- @php
        $message = '';
        foreach ($errors->all() as $error) {
            $message .= $error . '<br>';
        }
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                title: "Erro!",
                html: "{!! $message !!}",
                icon: "error"
            });
        });
    </script> --}}
    <div class="alert-danger">
        @foreach ($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif
