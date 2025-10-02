<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\TaxCreateRequest;
use App\Http\Requests\Company\TaxUpdateRequest;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use App\Repositories\Company\TaxRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TaxController extends Controller
{
    private $taxRepository;

    public function __construct(
        TaxRepository $taxRepository
    ) {
        $this->taxRepository = $taxRepository;
    }

    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();
        $filters = $request->filters ?? [];
        $filters['company_id'] = $company->id;
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $taxes = $this->taxRepository->getAllTaxes($company, $filters, $sort, $paginate, $perPage);
        if ($paginate) {
            return response()->json([
                'success' => true,
                'message' => 'Taxes fetched successfully',
                'data' => TaxResource::collection($taxes),
                'meta' => [
                    'current_page' => $taxes->currentPage(),
                    'last_page' => $taxes->lastPage(),
                    'per_page' => $taxes->perPage(),
                    'total' => $taxes->total(),
                    'links' => [
                        'first' => $taxes->url(1),
                        'last' => $taxes->url($taxes->lastPage()),
                        'prev' => $taxes->previousPageUrl(),
                        'next' => $taxes->nextPageUrl(),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Taxes fetched successfully',
            'data' => TaxResource::collection($taxes),
        ]);
    }


    public function show(Tax $tax)
    {
        $company = Auth::guard('company')->user();
        $tax = $this->taxRepository->getTaxById($company, $tax->id);

        return response()->json([
            'success' => true,
            'message' => 'Tax fetched successfully',
            'data' => new TaxResource($tax),
        ]);
    }

    
    public function store(TaxCreateRequest $request)
    {
        $company = Auth::guard('company')->user();

        if ($company->taxes()->where('name', $request->name)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tax already exists',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tax = $this->taxRepository->createTax($company, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tax created successfully',
            'data' => new TaxResource($tax),
        ]);
    }

    
    public function update(TaxUpdateRequest $request, Tax $tax)
    {
        $company = Auth::guard('company')->user();
        $tax = $this->taxRepository->updateTax($company, $request->all(), $tax->id);

        return response()->json([
            'success' => true,
            'message' => 'Tax updated successfully',
            'data' => new TaxResource($tax),
        ]);
    }

    public function destroy(Tax $tax)
    {
        $company = Auth::guard('company')->user();
        $tax = $this->taxRepository->deleteTax($company, $tax->id);

        return response()->json([
            'success' => true,
            'message' => 'Tax deleted successfully',
            'data' => null,
        ]);
    }
}
