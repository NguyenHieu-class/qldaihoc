@extends('layouts.app')

@section('title', 'Tỷ lệ mở môn theo khoa/học kỳ')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Tỷ lệ mở môn</h2>
    <canvas id="openRateChart" height="100"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json(array_map(fn($d) => $d['faculty'].' - '.$d['semester'], $data));
    const data = @json(array_column($data, 'percent'));
    new Chart(document.getElementById('openRateChart').getContext('2d'), {
        type: 'bar',
        data: {labels: labels, datasets:[{label:'% môn mở', data: data, backgroundColor:'rgba(75,192,192,0.7)'}]},
    });
</script>
@endpush
