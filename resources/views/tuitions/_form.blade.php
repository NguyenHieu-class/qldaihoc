@php
    $selectedStudent = old('student_id', optional($tuition)->student_id);
    $selectedSemester = old('semester_id', optional($tuition)->semester_id);
    $selectedStatus = old('status', optional($tuition)->status ?? \App\Models\Tuition::STATUS_PENDING);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="student_id" class="form-label">Sinh viên</label>
        <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
            <option value="">-- Chọn sinh viên --</option>
            @foreach($students as $student)
                <option value="{{ $student->id }}" {{ (string) $selectedStudent === (string) $student->id ? 'selected' : '' }}>
                    {{ $student->student_id }} - {{ $student->full_name }}
                    @if($student->class)
                        ({{ $student->class->name }})
                    @endif
                </option>
            @endforeach
        </select>
        @error('student_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="semester_id" class="form-label">Học kỳ</label>
        <select name="semester_id" id="semester_id" class="form-select @error('semester_id') is-invalid @enderror">
            <option value="">-- Không chọn --</option>
            @foreach($semesters as $semester)
                <option value="{{ $semester->id }}" {{ (string) $selectedSemester === (string) $semester->id ? 'selected' : '' }}>
                    {{ $semester->name }}
                    @if($semester->academicYear)
                        ({{ $semester->academicYear->name }})
                    @endif
                </option>
            @endforeach
        </select>
        @error('semester_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="amount" class="form-label">Số tiền (VNĐ)</label>
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
            <input
                type="number"
                name="amount"
                id="amount"
                class="form-control @error('amount') is-invalid @enderror"
                value="{{ old('amount', optional($tuition)->amount) }}"
                min="0"
                step="0.01"
                required
            >
            <span class="input-group-text">VNĐ</span>
            @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <label for="due_date" class="form-label">Hạn thanh toán</label>
        <input
            type="date"
            name="due_date"
            id="due_date"
            class="form-control @error('due_date') is-invalid @enderror"
            value="{{ old('due_date', optional(optional($tuition)->due_date)->format('Y-m-d')) }}"
        >
        @error('due_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Trạng thái</label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach($statuses as $value => $label)
                <option value="{{ $value }}" {{ $selectedStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="paid_at" class="form-label">Ngày thanh toán</label>
        <input
            type="datetime-local"
            name="paid_at"
            id="paid_at"
            class="form-control @error('paid_at') is-invalid @enderror"
            value="{{ old('paid_at', optional(optional($tuition)->paid_at)->format('Y-m-d\TH:i')) }}"
        >
        @error('paid_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="form-text text-muted">Chỉ cần nhập khi trạng thái là "Đã thanh toán".</small>
    </div>

    <div class="col-md-8">
        <label for="notes" class="form-label">Ghi chú</label>
        <textarea
            name="notes"
            id="notes"
            rows="3"
            class="form-control @error('notes') is-invalid @enderror"
        >{{ old('notes', optional($tuition)->notes) }}</textarea>
        @error('notes')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
