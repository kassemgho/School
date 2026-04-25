<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'date',
        'status',
        'type',
        'division_id'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function division()
    {
        return $this->belongsTo(Division::class);
    }
}
