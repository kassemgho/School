<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_fee_id',
        'amount',
        'payment_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /*
    |----------------------------
    | RELATIONS
    |----------------------------
    */

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}