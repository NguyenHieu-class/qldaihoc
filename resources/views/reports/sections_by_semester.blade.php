@extends('layouts.app')

@section('title', 'Thống kê lớp học phần theo học kỳ')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Số lớp học phần theo học kỳ</h2>
    <canvas id="sectionsChart" height="100"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('sectionsChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json(array_column($data, 'name')),
            datasets: [{
                label: 'Số lớp học phần',
                data: @json(array_column($data, 'count')),
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        }
    });
</script>
@endpush
