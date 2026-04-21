<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionTeacherSubject extends Model
{
    protected $table = 'division_teacher_subject';

    protected $fillable = [
        'teacher_id',
        'division_id',
        'subject_id',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}       