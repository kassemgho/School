<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamStudentResult extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'total_mark',
        'status',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}