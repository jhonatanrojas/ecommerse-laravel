<?php

namespace App\Http\Controllers\Api\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\ProductQuestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductQuestionController extends Controller
{
    public function index(int $productId): JsonResponse
    {
        $questions = ProductQuestion::query()
            ->with(['user:id,name', 'vendor:id,business_name'])
            ->where('product_id', $productId)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $questions->map(fn (ProductQuestion $q) => [
                'uuid' => $q->uuid,
                'question' => $q->question,
                'answer' => $q->answer,
                'asked_by' => $q->user?->name,
                'answered_by' => $q->vendor?->business_name,
                'asked_at' => $q->created_at?->toIso8601String(),
                'answered_at' => $q->answered_at?->toIso8601String(),
            ])->values(),
            'meta' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'total' => $questions->total(),
            ],
        ]);
    }

    public function store(Request $request, int $productId): JsonResponse
    {
        $validated = $request->validate([
            'text' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        $question = ProductQuestion::query()->create([
            'product_id' => $productId,
            'user_id' => $request->user()?->id,
            'question' => $validated['text'],
        ])->fresh(['user:id,name']);

        return response()->json([
            'success' => true,
            'message' => 'Pregunta publicada.',
            'data' => [
                'uuid' => $question->uuid,
                'question' => $question->question,
                'answer' => $question->answer,
                'asked_by' => $question->user?->name,
                'asked_at' => $question->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
