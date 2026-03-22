<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('status_id')->default(1);
            $table->string('number')->nullable();
            $table->decimal('discount', 20, 6)->default(0);
            $table->boolean('is_amount_discount')->default(false);
            $table->string('po_number')->nullable();
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->json('line_items')->nullable();
            $table->json('backup')->nullable();
            $table->text('footer')->nullable();
            $table->text('public_notes')->nullable();
            $table->text('private_notes')->nullable();
            $table->text('terms')->nullable();
            $table->string('tax_name1')->nullable();
            $table->decimal('tax_rate1', 20, 6)->default(0);
            $table->string('tax_name2')->nullable();
            $table->decimal('tax_rate2', 20, 6)->default(0);
            $table->string('tax_name3')->nullable();
            $table->decimal('tax_rate3', 20, 6)->default(0);
            $table->string('custom_value1')->nullable();
            $table->string('custom_value2')->nullable();
            $table->string('custom_value3')->nullable();
            $table->string('custom_value4')->nullable();
            $table->decimal('amount', 20, 6)->default(0);
            $table->decimal('balance', 20, 6)->default(0);
            $table->decimal('partial', 20, 6)->default(0);
            $table->date('partial_due_date')->nullable();
            $table->decimal('exchange_rate', 20, 6)->default(1);
            $table->date('last_sent_date')->nullable();
            $table->timestamp('last_viewed')->nullable();
            $table->date('next_send_date')->nullable();
            $table->date('reminder1_sent')->nullable();
            $table->date('reminder2_sent')->nullable();
            $table->date('reminder3_sent')->nullable();
            $table->date('reminder_last_sent')->nullable();
            $table->decimal('paid_to_date', 20, 6)->default(0);
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
