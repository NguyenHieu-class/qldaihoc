@extends('layouts.app')

@section('title', 'Báo cáo tổng hợp')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Báo cáo tổng hợp</h2>

    <div class="mb-5">
        <h4>Số lớp học phần theo học kỳ</h4>
        <canvas id="sectionsChart" height="100"></canvas>
        <div class="mt-2">
            <a href="{{ route('reports.sections') }}">Chi tiết</a>
        </div>
    </div>

    <div class="mb-5">
        <h4>Khối lượng giảng dạy</h4>
        <canvas id="workloadChart" height="100"></canvas>
        <div class="mt-2">
            <a href="{{ route('reports.workload') }}">Chi tiết</a>
        </div>
    </div>

    <div class="mb-5">
        <h4>Tỷ lệ mở môn</h4>
        <canvas id="openRateChart" height="100"></canvas>
        <div class="mt-2">
            <a href="{{ route('reports.open_rate') }}">Chi tiết</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const sectionsCtx = document.getElementById('sectionsChart').getContext('2d');
    new Chart(sectionsCtx, {
        type: 'bar',
        data: {
            labels: @json(array_column($sectionsData, 'name')),
            datasets: [{
                label: 'Số lớp học phần',
                data: @json(array_column($sectionsData, 'count')),
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        }
    });

    const teacherData = @json($workloadData);
    const semesters = @json($semesters->map(fn($s) => $s->name.' '.$s->academicYear->name));
    const workloadDatasets = semesters.map((sem, idx) => ({
        label: sem,
        data: teacherData.map(t => t.rows[idx].payment),
        backgroundColor: `rgba(${(idx+1)*40}, 99, 132, 0.7)`
    }));
    new Chart(document.getElementById('workloadChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: teacherData.map(t => t.teacher),
            datasets: workloadDatasets
        },
        options: {responsive: true, scales: {x: {stacked: true}, y: {stacked: true}}}
    });

    const openLabels = @json(array_map(fn($d) => $d['faculty'].' - '.$d['semester'], $openRateData));
    const openData = @json(array_column($openRateData, 'percent'));
    new Chart(document.getElementById('openRateChart').getContext('2d'), {
        type: 'bar',
        data: {labels: openLabels, datasets:[{label:'% môn mở', data: openData, backgroundColor:'rgba(75,192,192,0.7)'}]}
    });
</script>
@endpush
