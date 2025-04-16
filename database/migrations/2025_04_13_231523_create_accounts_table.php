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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->enum('name', ['PAYMENT']);

            $table->foreignId('user_id')
                ->constrained(
                    table: 'users',
                    column: 'id'
                )
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->unique(['user_id', 'name']);
            $table->index(['user_id', 'name']);

            $table->timestamps();
            $table->softDeletes('disabled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
