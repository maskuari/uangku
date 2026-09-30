<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('opening_balance')->default(0);
            $table->unsignedBigInteger('monthly_budget')->nullable();
        });
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->string('title', 150);
            $table->string('category', 60);
            $table->unsignedBigInteger('amount');
            $table->date('occurred_on');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'occurred_on']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('transactions');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['opening_balance', 'monthly_budget']));
    }
};
