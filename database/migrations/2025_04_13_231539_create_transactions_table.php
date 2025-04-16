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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->decimal('amount');
            $table->foreignId('account_id')
                ->constrained(
                    table: 'accounts',
                    column: 'id'
                )
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('transaction_code_id')
                ->constrained(
                    table: 'transaction_codes',
                    column: 'id'
                )
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->enum('status', ['PENDING', 'COMPLETED', 'CANCELED'])
                ->default('PENDING');

            $table->uuid('correlation_id');

            $table->index('correlation_id');
            $table->index(['account_id', 'transaction_code_id'], 'account_transaction_code_id');
            $table->index(['status', 'transaction_code_id'], 'status_transaction_code_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
