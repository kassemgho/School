<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'exam_id',
        'classification_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'mark',
    ];
    // protected $hidden = ['correct_answer']; //comment_test - remove
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function classification()
    {
        return $this->belongsTo(SubjectClassification::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}