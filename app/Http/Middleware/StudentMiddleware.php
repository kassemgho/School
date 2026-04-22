<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Check role
        if ($user->role !== 'student') {
            return response()->json([
                'message' => 'Only students allowed'
            ], 403);
        }

        // Load student with relations
        $student = $user->student()->with([
            'division.class',
            'academicYear'
        ])->first();

        if (!$student) {
            return response()->json([
                'message' => 'Student record not found'
            ], 404);
        }

        // Attach to request
        $request->merge([
            'student' => $student
        ]);

        return $next($request);
    }
}   