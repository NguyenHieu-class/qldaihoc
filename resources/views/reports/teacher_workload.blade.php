@extends('layouts.app')

@section('title', 'Khối lượng giảng dạy theo học kỳ')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Khối lượng giảng dạy</h2>
    <canvas id="workloadChart" height="100"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const teacherData = @json($data);
    const semesters = @json($semesters->map(fn($s) => $s->name.' '.$s->academicYear->name));
    const datasets = semesters.map((sem, idx) => ({
        label: sem,
        data: teacherData.map(t => t.rows[idx].payment),
        backgroundColor: `rgba(${(idx+1)*40}, 99, 132, 0.7)`
    }));
    const ctx = document.getElementById('workloadChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: teacherData.map(t => t.teacher),
            datasets: datasets
        },
        options: {responsive: true, scales: {x: {stacked: true}, y: {stacked: true}}}
    });
</script>
@endpush
