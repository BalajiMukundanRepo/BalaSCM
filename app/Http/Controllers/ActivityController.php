<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Transformers\ActivityTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found for user', 404);
        }

        $query = Activity::where('company_id', $company->id);

        if ($request->has('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->input('invoice_id'));
        }

        if ($request->has('payment_id')) {
            $query->where('payment_id', $request->input('payment_id'));
        }

        $query->orderBy('created_at', 'desc');

        return $this->listResponse($query, new ActivityTransformer(), $request);
    }
}
