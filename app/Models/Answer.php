<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $fillable = [
        'exam_student_result_id',
        'question_id',
        'selected_answer',
        'is_correct',
    ];

    public function result()
    {
        return $this->belongsTo(ExamStudentResult::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}