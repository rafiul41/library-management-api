<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Book;
use App\Models\BookMeta;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Book::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'author' => 'required',
            'price' => 'required',
            'total_copies' => 'required'
        ]);

        try {
            $book = Book::create([
                'name' => $request->input('name'),
                'author' => $request->input('author'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
                'total_copies' => $request->input('total_copies'),
            ]);
    
            $bookMeta = BookMeta::create([
                'book_id' => $book->id,
                'issued_user_ids' => '',
                'genre' => $request->input('genre'),
                'publication_year' => $request->input('publication_year'),
            ]);
    
            return response()->json([
                'message' => 'Book created successfully.',
                'book' => $book,
                'book_meta' => $bookMeta,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to create book.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Book::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);
        $book->update($request->all());
        return $book;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Book::destroy($id);
    }
}
