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
        Schema::create('notification_outbox', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained(
                    table: 'accounts',
                    column: 'id'
                )
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->boolean('was_sent');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_notification_outbox');
    }
};
