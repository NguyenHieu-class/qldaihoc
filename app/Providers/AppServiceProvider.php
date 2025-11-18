<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\ClassSection;
use App\Models\ClassSizeCoefficient;
use App\Models\Classes;
use App\Models\CourseOffering;
use App\Models\Degree;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Major;
use App\Models\PasswordResetRequest;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingRate;
use App\Models\Tuition;
use App\Models\TuitionSetting;
use App\Models\User;
use App\Observers\ModelChangeObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.custom');
        Paginator::defaultSimpleView('vendor.pagination.simple-bootstrap-5');

        $models = [
            AcademicYear::class,
            ClassSection::class,
            ClassSizeCoefficient::class,
            Classes::class,
            CourseOffering::class,
            Degree::class,
            Enrollment::class,
            Faculty::class,
            Grade::class,
            Major::class,
            PasswordResetRequest::class,
            Semester::class,
            Student::class,
            Subject::class,
            Teacher::class,
            TeachingRate::class,
            Tuition::class,
            TuitionSetting::class,
            User::class,
        ];

        foreach ($models as $model) {
            $model::observe(ModelChangeObserver::class);
        }
    }
}
