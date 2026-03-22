<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('status_id')->default(1);
            $table->unsignedSmallInteger('type_id')->nullable();
            $table->decimal('amount', 20, 6)->default(0);
            $table->decimal('refunded', 20, 6)->default(0);
            $table->decimal('applied', 20, 6)->default(0);
            $table->date('date')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('number')->nullable();
            $table->text('private_notes')->nullable();
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_manual')->default(false);
            $table->decimal('exchange_rate', 20, 6)->default(1);
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('exchange_currency_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
