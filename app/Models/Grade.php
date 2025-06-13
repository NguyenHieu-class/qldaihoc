<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'midterm_score',
        'final_score',
        'assignment_score',
        'total_score',
        'semester',
        'academic_year',
    ];

    /**
     * Lấy sinh viên có điểm số này
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Lấy môn học có điểm số này
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Tính điểm tổng kết
     */
    public function calculateTotalScore(): float
    {
        // Giả sử trọng số: 30% điểm giữa kỳ, 60% điểm cuối kỳ, 10% điểm bài tập
        $midtermWeight = 0.3;
        $finalWeight = 0.6;
        $assignmentWeight = 0.1;

        $total = 0;
        $weightSum = 0;

        if ($this->midterm_score !== null) {
            $total += $this->midterm_score * $midtermWeight;
            $weightSum += $midtermWeight;
        }

        if ($this->final_score !== null) {
            $total += $this->final_score * $finalWeight;
            $weightSum += $finalWeight;
        }

        if ($this->assignment_score !== null) {
            $total += $this->assignment_score * $assignmentWeight;
            $weightSum += $assignmentWeight;
        }

        // Nếu không có điểm nào, trả về 0
        if ($weightSum === 0) {
            return 0;
        }

        // Điều chỉnh tổng điểm dựa trên trọng số đã tính
        return $total / $weightSum;
    }

    /**
     * Cập nhật điểm tổng kết
     */
    public function updateTotalScore(): void
    {
        $this->total_score = $this->calculateTotalScore();
        $this->save();
    }
} 