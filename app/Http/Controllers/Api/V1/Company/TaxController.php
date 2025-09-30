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

    /**
     * @OA\Get(
     *     path="/company/taxes",
     *     summary="Get all taxes for a company",
     *     description="Retrieve all taxes for the authenticated company with optional filters, sorting, and pagination",
     *     tags={"Taxes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="filters[id]",
     *         in="query",
     *         description="Filter by tax ID",
     *         required=false,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filters[name]",
     *         in="query",
     *         description="Filter by tax name",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filters[type]",
     *         in="query",
     *         description="Filter by tax type (percentage or fixed)",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filters[rate]",
     *         in="query",
     *         description="Filter by tax rate",
     *         required=false,
     *
     *         @OA\Schema(type="number", format="float")
     *     ),
     *
     *     @OA\Parameter(
     *         name="filters[is_active]",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *
     *         @OA\Schema(type="boolean")
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order (asc or desc), default desc",
     *         required=false,
     *
     *         @OA\Schema(type="string", example="desc")
     *     ),
     *
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Whether to paginate results (true/false)",
     *         required=false,
     *
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of results per page",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Taxes retrieved successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Taxes fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/TaxResource")
     *             ),
     *
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 nullable=true,
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=5),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="first", type="string", example="http://api.example.com/company/taxes?page=1"),
     *                     @OA\Property(property="last", type="string", example="http://api.example.com/company/taxes?page=1"),
     *                     @OA\Property(property="prev", type="string", nullable=true),
     *                     @OA\Property(property="next", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/company/taxes/{id}",
     *     summary="Get a single tax",
     *     description="Retrieve a tax by ID for the authenticated company",
     *     tags={"Taxes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the tax",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tax fetched successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tax fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/TaxResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Tax not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Tax not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/company/taxes",
     *     summary="Create a new tax",
     *     description="Create a tax for the authenticated company",
     *     tags={"Taxes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="name", type="string", example="VAT"),
     *             @OA\Property(property="type", type="string", enum={"percentage","fixed"}, example="percentage"),
     *             @OA\Property(property="rate", type="number", format="float", example=10),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Tax created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tax created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/TaxResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or tax already exists",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Tax already exists"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/company/taxes/{tax}",
     *     summary="Update an existing tax",
     *     description="Update a tax for the authenticated company",
     *     tags={"Taxes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="tax",
     *         in="path",
     *         description="ID of the tax to update",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="name", type="string", example="VAT"),
     *             @OA\Property(property="type", type="string", enum={"percentage","fixed"}, example="percentage"),
     *             @OA\Property(property="rate", type="number", format="float", example=10),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tax updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tax updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/TaxResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/company/taxes/{tax}",
     *     summary="Delete a tax",
     *     description="Delete a tax for the authenticated company",
     *     tags={"Taxes"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="tax",
     *         in="path",
     *         description="ID of the tax to delete",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tax deleted successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tax deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Tax not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Tax not found")
     *         )
     *     )
     * )
     */
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
