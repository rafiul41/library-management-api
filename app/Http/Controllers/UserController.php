<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);

        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'issued_user' => ''
            ]);

            return response()->json([
                'message' => 'User created successfully.',
                'user' => $user
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to create user.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return User::find($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return User::destroy($id);
    }

    public function listIssuedBooks(string $user_id)
    {
        try {
            $user = User::find($user_id);
            $commaSeparatedBookIds = $user->issued_book_ids;

            if (!$commaSeparatedBookIds) {
                return response()->json([
                    'message' => 'No books issued to this user.',
                    'issued_books' => [],
                ], Response::HTTP_OK);
            }

            // Split the comma-separated issued book IDs into an array
            $arrayOfBookIds = explode(',', $commaSeparatedBookIds);

            // Fetch the books with the book IDs from the array
            $issuedBooks = Book::whereIn('id', $arrayOfBookIds)->get();

            // Return the list of issued books along with the user information
            return response()->json([
                'message' => 'List of books issued to the user.',
                'issued_books' => $issuedBooks,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'message' => 'Failed to fetch issued books.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
