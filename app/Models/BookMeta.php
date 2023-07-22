<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookMeta extends Model
{
    use HasFactory;

    protected $fillable = ['book_id', 'issued_user_ids', 'genre', 'publication_year'];
}
