<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopifyProduct;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProductSearchController extends Controller
{
    /**
     * Search products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2|max:255',
            'filters' => 'sometimes|array',
            'sort' => 'sometimes|string|in:relevance,price_asc,price_desc,newest,oldest',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->input('query');
        $filters = $request->input('filters', []);
        $sort = $request->input('sort', 'relevance');
        $perPage = $request->input('per_page', 24);
        $page = $request->input('page', 1);

        // Build the search query
        $search = ShopifyProduct::search($query);

        // Apply filters if any
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    $search->whereIn($field, $value);
                } else {
                    $search->where($field, $value);
                }
            }
        }

        // Apply sorting if not using default relevance
        if ($sort !== 'relevance') {
            $search->orderBy($this->getSortField($sort), $this->getSortDirection($sort));
        }

        // Execute the search with pagination
        $results = $search->paginate($perPage, 'page', $page);

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'meta' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem(),
            ]
        ]);
    }

    /**
     * Get the sort field based on the sort option
     *
     * @param string $sort
     * @return string
     */
    protected function getSortField(string $sort): string
    {
        // This method is not called when sort is 'relevance'
        return match ($sort) {
            'price_asc', 'price_desc' => 'price',
            'newest', 'oldest' => 'created_at',
            default => 'created_at', // Should not happen due to validation
        };
    }

    /**
     * Get the sort direction based on the sort option
     *
     * @param string $sort
     * @return string
     */
    protected function getSortDirection(string $sort): string
    {
        // This method is not called when sort is 'relevance'
        return match ($sort) {
            'price_desc', 'newest' => 'desc',
            'price_asc', 'oldest' => 'asc',
            default => 'desc', // Should not happen due to validation
        };
    }
}
