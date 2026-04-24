<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class StudentBookController extends Controller
{
    /*
    |----------------------------
    | LIST / SEARCH BOOKS
    |----------------------------
    */
    public function index(Request $request)
    {
        $search = $request->search;
        $subjectId = $request->subject_id;

        $books = Book::with(['subject', 'uploader'])
            ->when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            })
            ->when($subjectId, function ($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->get();

        return response()->json([
            'data' => $books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'subject' => $book->subject->name ?? null,
                    'uploaded_by' => $book->uploader->name ?? null,
                    'file' => $book->file_path,
                    'file_url' => asset('storage/' . $book->file_path),
                ];
            })
        ]);
    }

    /*
    |----------------------------
    | DOWNLOAD BOOK
    |----------------------------
    */
    public function download($id)
    {
        $book = Book::findOrFail($id);

        $path = storage_path('app/public/' . $book->file_path);

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        return response()->download($path);
    }
}
