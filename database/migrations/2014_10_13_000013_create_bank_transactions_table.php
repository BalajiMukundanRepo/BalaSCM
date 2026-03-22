<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bank_integration_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('amount', 20, 6)->default(0);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('category_type')->nullable();
            $table->date('date')->nullable();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->text('description')->nullable();
            $table->string('invoice_ids')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->unsignedSmallInteger('status_id')->default(1);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
