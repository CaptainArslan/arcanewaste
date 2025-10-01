<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Models\Promotion;
use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\Company\PromotionResource;
use App\Repositories\Company\PromotionRepository;
use App\Http\Requests\Company\PromotionCreateRequest;
use App\Http\Requests\Company\PromotionUpdateRequest;

class PromotionController extends Controller
{
    private $promotionRepository;

    public function __construct(PromotionRepository $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();

        $filters = $request->filters ?? [];
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $promotions = $this->promotionRepository->getAllPromotions($company, $filters, $sort, $paginate, $perPage);

        return ApiHelper::successResponse(
            $paginate,
            PromotionResource::collection($promotions),
            'Promotions fetched successfully'
        );
    }

    public function show(Promotion $promotion)
    {
        $company = Auth::guard('company')->user();
        $promotion = $this->promotionRepository->getPromotionById($company, $promotion->id);

        if (! $promotion) {
            return $this->sendErrorResponse('Promotion not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion fetched successfully',
            'data' => PromotionResource::make($promotion),
        ]);
    }

    public function store(PromotionCreateRequest $request)
    {
        $company = Auth::guard('company')->user();
        $promotion = $this->promotionRepository->createPromotion($company, $request->all());

        if (! $promotion) {
            return $this->sendErrorResponse('Promotion not created', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion created successfully',
            'data' => PromotionResource::make($promotion),
        ]);
    }

    public function update(PromotionUpdateRequest $request, Promotion $promotion)
    {
        $company = Auth::guard('company')->user();
        $promotion = $this->promotionRepository->updatePromotion($company, $request->all(), $promotion->id);

        if (! $promotion) {
            return $this->sendErrorResponse('Promotion not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion updated successfully',
            'data' => PromotionResource::make($promotion),
        ]);
    }

    public function destroy(Promotion $promotion)
    {
        $company = Auth::guard('company')->user();
        $this->promotionRepository->deletePromotion($company, $promotion->id);

        if (! $promotion) {
            return $this->sendErrorResponse('Promotion not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion deleted successfully',
            'data' => null,
        ]);
    }
}
