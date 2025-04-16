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
        Schema::create('transaction_codes', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('description')->nullable();
            $table->enum('type', ['DEBIT', 'CREDIT']);

            $table->index('type');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_codes');
    }
};
