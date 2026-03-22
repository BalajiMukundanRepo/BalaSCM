<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Invoice;
use App\Transformers\InvoiceTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $query = Invoice::where('company_id', $company->id);

        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        return $this->listResponse($query, new InvoiceTransformer(), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $invoice = Invoice::create(array_merge(
            $request->only([
                'client_id', 'status_id', 'number', 'discount', 'is_amount_discount',
                'po_number', 'date', 'due_date', 'line_items', 'footer', 'public_notes',
                'private_notes', 'terms', 'tax_name1', 'tax_rate1', 'tax_name2', 'tax_rate2',
                'tax_name3', 'tax_rate3', 'custom_value1', 'custom_value2', 'custom_value3',
                'custom_value4', 'amount', 'balance', 'partial', 'partial_due_date',
                'exchange_rate', 'subscription_id',
            ]),
            ['company_id' => $company->id, 'user_id' => $user->id]
        ));

        Activity::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoice->id,
            'activity_type_id' => Activity::CREATE_INVOICE,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($invoice, new InvoiceTransformer());
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        return $this->itemResponse($invoice, new InvoiceTransformer());
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        $invoice->update($request->only([
            'client_id', 'status_id', 'number', 'discount', 'is_amount_discount',
            'po_number', 'date', 'due_date', 'line_items', 'footer', 'public_notes',
            'private_notes', 'terms', 'tax_name1', 'tax_rate1', 'tax_name2', 'tax_rate2',
            'tax_name3', 'tax_rate3', 'custom_value1', 'custom_value2', 'custom_value3',
            'custom_value4', 'amount', 'balance', 'partial', 'partial_due_date',
            'exchange_rate', 'subscription_id',
        ]));

        Activity::create([
            'company_id' => $invoice->company_id,
            'user_id' => $request->user()->id,
            'invoice_id' => $invoice->id,
            'activity_type_id' => Activity::UPDATE_INVOICE,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($invoice->fresh(), new InvoiceTransformer());
    }

    public function destroy(Request $request, Invoice $invoice): JsonResponse
    {
        Activity::create([
            'company_id' => $invoice->company_id,
            'user_id' => $request->user()->id,
            'invoice_id' => $invoice->id,
            'activity_type_id' => Activity::DELETE_INVOICE,
            'ip' => $request->ip(),
        ]);

        $invoice->is_deleted = true;
        $invoice->save();
        $invoice->delete();

        return response()->json(['message' => 'Invoice deleted']);
    }
}
