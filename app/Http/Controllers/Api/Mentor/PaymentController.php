<?php

namespace App\Http\Controllers\Api\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentFee;
use App\Models\Payment;

class PaymentController extends Controller
{
    /*
    |----------------------------
    | 1. ADD PAYMENT
    |----------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'student_fee_id' => 'required|exists:student_fees,id',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string'
        ]);

        $studentFee = StudentFee::findOrFail($request->student_fee_id);

        /*
        |----------------------------
        | 🔴 Prevent Overpayment
        |----------------------------
        */
        if ($studentFee->paid_amount + $request->amount > $studentFee->total_amount) {
            return response()->json([
                'message' => 'Payment exceeds remaining amount'
            ], 422);
        }

        /*
        |----------------------------
        | 🟢 CREATE PAYMENT
        |----------------------------
        */
        $payment = Payment::create([
            'student_fee_id' => $studentFee->id,
            'amount' => $request->amount,
            'payment_date' => now(),
            'notes' => $request->notes,
            'created_by' => auth()->user()->id
        ]);

        /*
        |----------------------------
        | 🔄 UPDATE STUDENT FEE
        |----------------------------
        */
        $newPaid = $studentFee->paid_amount + $request->amount;

        $status = 'pending';

        if ($newPaid == 0) {
            $status = 'pending';
        } elseif ($newPaid < $studentFee->total_amount) {
            $status = 'partial';
        } else {
            $status = 'paid';
        }

        $studentFee->update([
            'paid_amount' => $newPaid,
            'status' => $status
        ]);

        return response()->json([
            'message' => 'Payment added successfully',
            'payment' => $payment,
            'summary' => [
                'total' => $studentFee->total_amount,
                'paid' => $newPaid,
                'remaining' => $studentFee->total_amount - $newPaid,
                'status' => $status
            ]
        ]);
    }

    /*
    |----------------------------
    | 2. PAYMENT HISTORY (BY STUDENT)
    |----------------------------
    */
    public function history($studentId)
    {
        $fees = StudentFee::with(['payments'])
            ->where('student_id', $studentId)
            ->get();

        return response()->json([
            'data' => $fees->map(function ($fee) {
                return [
                    'student_fee_id' => $fee->id,
                    'academic_year_id' => $fee->academic_year_id,
                    'total' => $fee->total_amount,
                    'paid' => $fee->paid_amount,
                    'remaining' => $fee->total_amount - $fee->paid_amount,
                    'status' => $fee->status,
                    'payments' => $fee->payments->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'amount' => $p->amount,
                            'date' => $p->payment_date,
                            'notes' => $p->notes,
                        ];
                    })
                ];
            })
        ]);
    }

    /*
    |----------------------------
    | 3. SINGLE FEE DETAILS
    |----------------------------
    */
    public function show($studentFeeId)
    {
        $fee = StudentFee::with(['payments'])
            ->findOrFail($studentFeeId);

        return response()->json([
            'id' => $fee->id,
            'student_id' => $fee->student_id,
            'academic_year_id' => $fee->academic_year_id,
            'total' => $fee->total_amount,
            'paid' => $fee->paid_amount,
            'remaining' => $fee->total_amount - $fee->paid_amount,
            'status' => $fee->status,
            'payments' => $fee->payments->map(function ($p) {
                return [
                    'id' => $p->id,
                    'amount' => $p->amount,
                    'date' => $p->payment_date,
                    'notes' => $p->notes,
                ];
            })
        ]);
    }
}