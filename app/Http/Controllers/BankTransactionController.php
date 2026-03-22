<?php

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use App\Transformers\BankTransactionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankTransactionController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $query = BankTransaction::where('company_id', $company->id);

        return $this->listResponse($query, new BankTransactionTransformer(), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $bankTransaction = BankTransaction::create(array_merge(
            $request->only([
                'bank_integration_id', 'transaction_id', 'amount', 'currency_id',
                'category_id', 'category_type', 'date', 'bank_account_id',
                'description', 'invoice_ids', 'expense_id', 'vendor_id', 'status_id',
            ]),
            ['company_id' => $company->id, 'user_id' => $user->id]
        ));

        return $this->itemResponse($bankTransaction, new BankTransactionTransformer());
    }

    public function show(Request $request, BankTransaction $bankTransaction): JsonResponse
    {
        return $this->itemResponse($bankTransaction, new BankTransactionTransformer());
    }

    public function update(Request $request, BankTransaction $bankTransaction): JsonResponse
    {
        $bankTransaction->update($request->only([
            'bank_integration_id', 'transaction_id', 'amount', 'currency_id',
            'category_id', 'category_type', 'date', 'bank_account_id',
            'description', 'invoice_ids', 'expense_id', 'vendor_id', 'status_id',
        ]));

        return $this->itemResponse($bankTransaction->fresh(), new BankTransactionTransformer());
    }

    public function destroy(Request $request, BankTransaction $bankTransaction): JsonResponse
    {
        $bankTransaction->is_deleted = true;
        $bankTransaction->save();
        $bankTransaction->delete();

        return response()->json(['message' => 'Bank transaction deleted']);
    }
}
