@extends('layouts.admin_app')

@section('content')
<h1>Statistics</h1>

<pre id="stats"></pre>

<script>
    fetch('/api/admin/statistics')
        .then(res => res.json())
        .then(data => {
            document.getElementById('stats').textContent = JSON.stringify(data, null, 2);
        });
</script>
@endsection