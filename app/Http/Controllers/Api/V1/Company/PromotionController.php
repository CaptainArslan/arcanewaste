<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Company\PromotionRepository;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;

class PromotionController extends Controller
{
    private $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function index(Request $request)
    {
        $promotions = $this->promotionRepository->getAllPromotions($request->company, $request->filters, $request->sort, $request->paginate, $request->perPage);
        return PromotionResource::collection($promotions);
    }

    public function show(Promotion $promotion)
    {
        return new PromotionResource($promotion);
    }

    public function store(Request $request)
    {
        $promotion = $this->promotionRepository->createPromotion($request->company, $request->all());
        return new PromotionResource($promotion);
    }

    public function update(Request $request, Promotion $promotion)
    {
        $promotion = $this->promotionRepository->updatePromotion($request->company, $request->all(), $promotion);
        return new PromotionResource($promotion);
    }

    public function destroy(Promotion $promotion)
    {
        $this->promotionRepository->deletePromotion($request->company, $promotion);
        return response()->json([
            'message' => 'Promotion deleted successfully'
        ]);
    }
}
