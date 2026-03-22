<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('industry_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->string('name')->nullable();
            $table->string('ip')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_large')->default(false);
            $table->boolean('is_disabled')->default(false);
            $table->integer('enable_modules')->default(0);
            $table->integer('default_password_timeout')->default(30);
            $table->integer('enabled_tax_rates')->default(0);
            $table->string('slack_webhook_url')->nullable();
            $table->string('google_analytics_key')->nullable();
            $table->string('portal_mode')->default('domain');
            $table->string('portal_domain')->nullable();
            $table->string('subdomain')->nullable();
            $table->string('expense_mailbox')->nullable();
            $table->boolean('expense_mailbox_active')->default(false);
            $table->text('e_invoice_certificate')->nullable();
            $table->string('e_invoice_certificate_passphrase')->nullable();
            $table->json('tax_data')->nullable();
            $table->json('origin_tax_data')->nullable();
            $table->json('custom_fields')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
