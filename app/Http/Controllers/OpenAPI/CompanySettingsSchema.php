<?php

namespace App\Http\Controllers\OpenAPI;

/**
 * @OA\Schema(
 *     schema="CompanySettings",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Acme Co"),
 *     @OA\Property(property="address1", type="string", example="123 Main St"),
 *     @OA\Property(property="address2", type="string", example="Suite 100"),
 *     @OA\Property(property="city", type="string", example="New York"),
 *     @OA\Property(property="state", type="string", example="NY"),
 *     @OA\Property(property="postal_code", type="string", example="10001"),
 *     @OA\Property(property="country_id", type="string", example="840"),
 *     @OA\Property(property="phone", type="string", example="555-1234"),
 *     @OA\Property(property="email", type="string", example="info@example.com"),
 *     @OA\Property(property="website", type="string", example="https://example.com"),
 *     @OA\Property(property="currency_id", type="string", example="1"),
 *     @OA\Property(property="language_id", type="string", example="1"),
 *     @OA\Property(property="timezone_id", type="string", example="15"),
 *     @OA\Property(property="date_format_id", type="string", example="1"),
 *     @OA\Property(property="payment_terms", type="string", example=""),
 *     @OA\Property(property="invoice_terms", type="string", example=""),
 *     @OA\Property(property="invoice_footer", type="string", example=""),
 *     @OA\Property(property="auto_email_invoice", type="boolean", example=true),
 *     @OA\Property(property="auto_archive_invoice", type="boolean", example=false),
 *     @OA\Property(property="lock_invoices", type="string", example="off"),
 *     @OA\Property(property="email_style", type="string", example="light"),
 * )
 */
class CompanySettingsSchema
{
}
