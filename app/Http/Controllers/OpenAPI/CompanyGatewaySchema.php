<?php

namespace App\Http\Controllers\OpenAPI;

/**
 * @OA\Schema(
 *     schema="CompanyGateway",
 *     type="object",
 *     @OA\Property(property="id", type="string", example="Opnel5aKBz"),
 *     @OA\Property(property="company_id", type="string", example="Opnel5aKBz"),
 *     @OA\Property(property="gateway_key", type="string", example="3b6621f970ab18887c4f6dca78d3f8bb"),
 *     @OA\Property(property="accepted_credit_cards", type="integer", example=32),
 *     @OA\Property(property="require_cvv", type="boolean", example=true),
 *     @OA\Property(property="require_billing_address", type="boolean", example=false),
 *     @OA\Property(property="require_shipping_address", type="boolean", example=false),
 *     @OA\Property(property="config", type="string", example="{}"),
 *     @OA\Property(property="fees_and_limits", type="object"),
 *     @OA\Property(property="custom_value1", type="string", example=""),
 *     @OA\Property(property="custom_value2", type="string", example=""),
 *     @OA\Property(property="custom_value3", type="string", example=""),
 *     @OA\Property(property="custom_value4", type="string", example=""),
 *     @OA\Property(property="label", type="string", example="Credit Card"),
 *     @OA\Property(property="token_billing", type="string", example="always"),
 * )
 */
class CompanyGatewaySchema
{
}
