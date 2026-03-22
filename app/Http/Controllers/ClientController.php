<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Client;
use App\Transformers\ClientTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $query = Client::where('company_id', $company->id);

        return $this->listResponse($query, new ClientTransformer(), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $client = Client::create(array_merge(
            $request->only([
                'name', 'website', 'phone', 'address1', 'address2', 'city', 'state',
                'postal_code', 'country_id', 'industry_id', 'size_id', 'currency_id',
                'settings', 'group_settings_id', 'vat_number', 'id_number',
                'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
            ]),
            ['company_id' => $company->id, 'user_id' => $user->id]
        ));

        Activity::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'client_id' => $client->id,
            'activity_type_id' => Activity::CREATE_CLIENT,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($client, new ClientTransformer());
    }

    public function show(Request $request, Client $client): JsonResponse
    {
        return $this->itemResponse($client, new ClientTransformer());
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $client->update($request->only([
            'name', 'website', 'phone', 'address1', 'address2', 'city', 'state',
            'postal_code', 'country_id', 'industry_id', 'size_id', 'currency_id',
            'settings', 'group_settings_id', 'vat_number', 'id_number',
            'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
        ]));

        Activity::create([
            'company_id' => $client->company_id,
            'user_id' => $request->user()->id,
            'client_id' => $client->id,
            'activity_type_id' => Activity::UPDATE_CLIENT,
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($client->fresh(), new ClientTransformer());
    }

    public function destroy(Request $request, Client $client): JsonResponse
    {
        Activity::create([
            'company_id' => $client->company_id,
            'user_id' => $request->user()->id,
            'client_id' => $client->id,
            'activity_type_id' => Activity::DELETE_CLIENT,
            'ip' => $request->ip(),
        ]);

        $client->is_deleted = true;
        $client->save();
        $client->delete();

        return response()->json(['message' => 'Client deleted']);
    }
}
