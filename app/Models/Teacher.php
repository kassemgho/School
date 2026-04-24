<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'certificate',
        'specialization',
        'hire_date',
    ];

    // Teacher belongs to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Teacher teaches many schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Teacher creates many exams
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    // Pivot relation (teaching assignments)
    public function divisionSubjects()
    {
        return $this->hasMany(DivisionTeacherSubject::class);
    }
    
}