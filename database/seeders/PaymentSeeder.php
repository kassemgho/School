<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\StudentFee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $fees = StudentFee::all();

        foreach ($fees as $fee) {

            $payments = Payment::factory()
                ->count(rand(1, 3))
                ->make();

            $paidTotal = 0;

            foreach ($payments as $payment) {

                if ($paidTotal >= $fee->total_amount) break;

                $amount = min(
                    $payment->amount,
                    $fee->total_amount - $paidTotal
                );

                $payment->amount = $amount;
                $payment->student_fee_id = $fee->id;
                $payment->save();

                $paidTotal += $amount;
            }

            /*
            |----------------------------
            | UPDATE FEE STATUS
            |----------------------------
            */
            $status = 'pending';

            if ($paidTotal > 0 && $paidTotal < $fee->total_amount) {
                $status = 'partial';
            } elseif ($paidTotal >= $fee->total_amount) {
                $status = 'paid';
            }

            $fee->update([
                'paid_amount' => $paidTotal,
                'status' => $status
            ]);
        }
    }
}
