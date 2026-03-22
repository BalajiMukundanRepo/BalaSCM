<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\ArraySerializer;
use League\Fractal\TransformerAbstract;

abstract class BaseController extends Controller
{
    protected array $forced_includes = [];

    protected array $forced_index = [];

    protected Manager $manager;

    public function __construct()
    {
        $this->manager = new Manager();
        $this->manager->setSerializer(new ArraySerializer());
    }

    protected function includeParser(Request $request): void
    {
        $includes = $request->input('include', '');

        if (is_string($includes) && ! empty($includes)) {
            $includes = array_merge(explode(',', $includes), $this->forced_includes);
        } else {
            $includes = $this->forced_includes;
        }

        $this->manager->parseIncludes($includes);
    }

    protected function listResponse($query, TransformerAbstract $transformer, Request $request): JsonResponse
    {
        $this->includeParser($request);

        $paginator = $query->paginate((int) $request->input('per_page', 20));
        $collection = $paginator->getCollection();

        $resource = new Collection($collection, $transformer, 'data');
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        $data = $this->manager->createData($resource)->toArray();

        return response()->json($data);
    }

    protected function itemResponse($item, TransformerAbstract $transformer): JsonResponse
    {
        $resource = new Item($item, $transformer, 'data');
        $data = $this->manager->createData($resource)->toArray();

        return response()->json($data);
    }

    protected function errorResponse(string $message, int $code): JsonResponse
    {
        return response()->json([
            'error' => true,
            'message' => $message,
        ], $code);
    }
}
