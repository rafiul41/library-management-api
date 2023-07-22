<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_metas', function (Blueprint $table) {
            $table->id();
            $table->string('book_id');
            $table->string('issued_user_ids')->nullable();
            $table->string('genre')->nullable();
            $table->year('publication_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_metas');
    }
};
