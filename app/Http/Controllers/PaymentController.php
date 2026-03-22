<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Payment;
use App\Transformers\PaymentTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $query = Payment::where('company_id', $company->id);

        return $this->listResponse($query, new PaymentTransformer(), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric',
        ]);

        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $payment = Payment::create(array_merge(
            $request->only([
                'client_id', 'status_id', 'type_id', 'amount', 'refunded', 'applied',
                'date', 'transaction_reference', 'number', 'private_notes',
                'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
                'is_manual', 'exchange_rate', 'currency_id', 'exchange_currency_id',
            ]),
            ['company_id' => $company->id, 'user_id' => $user->id]
        ));

        Activity::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'client_id' => $payment->client_id,
            'payment_id' => $payment->id,
            'activity_type_id' => Activity::CREATE_PAYMENT,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($payment, new PaymentTransformer());
    }

    public function show(Request $request, Payment $payment): JsonResponse
    {
        return $this->itemResponse($payment, new PaymentTransformer());
    }

    public function update(Request $request, Payment $payment): JsonResponse
    {
        $payment->update($request->only([
            'status_id', 'type_id', 'amount', 'refunded', 'applied', 'date',
            'transaction_reference', 'number', 'private_notes',
            'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
            'is_manual', 'exchange_rate', 'currency_id', 'exchange_currency_id',
        ]));

        Activity::create([
            'company_id' => $payment->company_id,
            'user_id' => $request->user()->id,
            'payment_id' => $payment->id,
            'activity_type_id' => Activity::UPDATE_PAYMENT,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($payment->fresh(), new PaymentTransformer());
    }

    public function destroy(Request $request, Payment $payment): JsonResponse
    {
        Activity::create([
            'company_id' => $payment->company_id,
            'user_id' => $request->user()->id,
            'payment_id' => $payment->id,
            'activity_type_id' => Activity::DELETE_PAYMENT,
            'ip' => $request->ip(),
        ]);

        $payment->is_deleted = true;
        $payment->save();
        $payment->delete();

        return response()->json(['message' => 'Payment deleted']);
    }
}
