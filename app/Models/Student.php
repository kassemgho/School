<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'division_id',
        'academic_year_id',
        'enrollment_year',
        'status',
    ];

    /*
    |----------------------------
    | RELATIONS
    |----------------------------
    */

    // Student belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Student belongs to division
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    // Student belongs to academic year
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // Exam results
    public function examResults()
    {
        return $this->hasMany(ExamStudentResult::class);
    }

    // Attendance records
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

}