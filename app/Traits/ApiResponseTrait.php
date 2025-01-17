<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

trait ApiResponseTrait
{
    protected function successResponse($data, string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }
    protected function noDatasuccessResponse(string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], $code);
    }

    protected function errorResponse(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $code);
    }

    protected function resourceResponse(JsonResource $resource, string $message = null, int $code = 200): JsonResponse
    {
        return $this->successResponse(
            $resource->response()->getData(true)['data'],
            $message,
            $code
        );
    }


    protected function resourceCollectionResponse(ResourceCollection $collection, string $message = null, int $code = 200, $key = 'collection'): JsonResponse
    {
        $data = $collection->response()->getData(true);

        // Include pagination data if it's paginated
        $pagination = [
            'total' => $collection->total(),
            'current_page' => $collection->currentPage(),
            'per_page' => $collection->perPage(),
            'last_page' => $collection->lastPage(),
            'next_page_url' => $collection->nextPageUrl(),
            'prev_page_url' => $collection->previousPageUrl(),
        ];

        return $this->successResponse([
            $key => $data['data'],
            'meta' => $pagination,
        ], $message, $code);
    }
}
