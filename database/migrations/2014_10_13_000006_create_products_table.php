<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id');
            $table->string('product_key')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('cost', 20, 6)->default(0);
            $table->decimal('price', 20, 6)->default(0);
            $table->decimal('quantity', 20, 6)->default(1);
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
            $table->boolean('is_deleted')->default(false);
            $table->integer('in_stock_quantity')->default(0);
            $table->boolean('stock_notification')->default(true);
            $table->integer('stock_notification_threshold')->default(0);
            $table->integer('max_quantity')->default(0);
            $table->string('product_image')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
