<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Book;
use App\Models\BookMeta;
use App\Models\User;

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
        try {
            $book = Book::find($id);
            $book->name = $request->input('name');
            $book->author = $request->input('author');
            $book->description = $request->input('description');
            $book->price = $request->input('price');
            $book->total_copies = $request->input('total_copies');
            $book->save();

            $bookMeta = BookMeta::where('book_id', '=', $id)->firstOrFail();
            $bookMeta->genre = $request->input('genre');
            $bookMeta->publication_year = $request->input('publication_year');
            $bookMeta->save();

            return response()->json([
                'message' => 'Book updated successfully.',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to update book.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            BookMeta::where('book_id', '=', $id)->delete();
            Book::destroy($id);
            return response()->json([
                'message' => 'Book deleted successfully.',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to delete book.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function issueBook(Request $request) {
        $request->validate([
            'book_id' => 'required',
            'user_id' => 'required'
        ]);

        try {
            $bookMeta = BookMeta::where('book_id', '=', $request->input('book_id'))->firstOrFail();
            $user = User::find($request->input('user_id'));
            
            $issued_user_ids = $bookMeta->issued_user_ids ? "{$bookMeta->issued_user_ids},{$user->id}" : "{$user->id}";
            $issued_user_ids = explode(',', $issued_user_ids);
            $issued_user_ids = array_unique($issued_user_ids);
            $issued_user_ids = implode(',', $issued_user_ids);

            $bookMeta->issued_user_ids = $issued_user_ids;
            $bookMeta->save();

            $issued_book_ids = $user->issued_book_ids ? "{$user->issued_book_ids},{$bookMeta->book_id}" : "{$bookMeta->book_id}";
            $issued_book_ids = explode(',', $issued_book_ids);
            $issued_book_ids = array_unique($issued_book_ids);
            $issued_book_ids = implode(',', $issued_book_ids);

            $user->issued_book_ids = $issued_book_ids;
            $user->save();

            return response()->json([
                'message' => 'Book issued successfully.',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to issue book. book_id or user_id doesn\'t exist',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function submitBook(Request $request) {
        $request->validate([
            'book_id' => 'required',
            'user_id' => 'required'
        ]);

        try {
            $bookMeta = BookMeta::where('book_id', '=', $request->input('book_id'))->firstOrFail();
            $user = User::find($request->input('user_id'));
            
            $issued_user_ids = $bookMeta->issued_user_ids ? $bookMeta->issued_user_ids : "";
            $issued_user_ids = explode(',', $issued_user_ids);
            $key = array_search($user->id, $issued_user_ids);
            if ($key !== false) {
                unset($issued_user_ids[$key]);
            }
            $issued_user_ids = implode(',', $issued_user_ids);

            $bookMeta->issued_user_ids = $issued_user_ids;
            $bookMeta->save();

            $issued_book_ids = $user->issued_book_ids ? $user->issued_book_ids : "";
            $issued_book_ids = explode(',', $issued_book_ids);
            $key = array_search($bookMeta->book_id, $issued_book_ids);
            if ($key !== false) {
                unset($issued_book_ids[$key]);
            }
            $issued_book_ids = implode(',', $issued_book_ids);

            $user->issued_book_ids = $issued_book_ids;
            $user->save();

            return response()->json([
                'message' => 'Book submitted successfully.',
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to submit book. book_id or user_id doesn\'t exist',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
