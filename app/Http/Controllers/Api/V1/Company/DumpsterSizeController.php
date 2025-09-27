<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use App\Models\DumpsterSize;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\DumpsterSizeResource;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Company\DumpsterSizeRepository;
use App\Http\Requests\Company\DumpsterSizeCreateRequest;
use App\Http\Requests\Company\DumpsterSizeUpdateRequest;

class DumpsterSizeController extends Controller
{
    private $dumpsterSizeRepository;

    public function __construct(
        DumpsterSizeRepository $dumpsterSizeRepository
    ) {
        $this->dumpsterSizeRepository = $dumpsterSizeRepository;
    }

    /**
     * @OA\Get(
     *     path="/company/dumpster-sizes",
     *     summary="Get all dumpster sizes",
     *     description="Fetch all dumpster sizes for the authenticated company with optional filters, pagination, and sorting",
     *     tags={"Dumpster Sizes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="filters[id]",
     *         in="query",
     *         description="Filter by dumpster size ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filters[company_id]",
     *         in="query",
     *         description="Filter by company ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filters[name]",
     *         in="query",
     *         description="Filter by dumpster size name (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters[code]",
     *         in="query",
     *         description="Filter by dumpster size code (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters[description]",
     *         in="query",
     *         description="Filter by description (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters[min_rental_days]",
     *         in="query",
     *         description="Filter by minimum rental days",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filters[max_rental_days]",
     *         in="query",
     *         description="Filter by maximum rental days",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filters[base_rent]",
     *         in="query",
     *         description="Filter by base rent (>= value)",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="filters[extra_day_rent]",
     *         in="query",
     *         description="Filter by extra day rent (>= value)",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="filters[overdue_rent]",
     *         in="query",
     *         description="Filter by overdue rent (>= value)",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="filters[volume_cubic_yards]",
     *         in="query",
     *         description="Filter by volume (>= value)",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="filters[weight_limit_lbs]",
     *         in="query",
     *         description="Filter by weight limit (>= value)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filters[is_active]",
     *         in="query",
     *         description="Filter by active status (true/false)",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order: asc or desc (default: desc)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc","desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Whether to paginate the results (true/false, default: true)",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page when paginating",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dumpster sizes fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dumpster sizes fetched successfully"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/DumpsterSizeResource")),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="first", type="string"),
     *                     @OA\Property(property="last", type="string"),
     *                     @OA\Property(property="prev", type="string", nullable=true),
     *                     @OA\Property(property="next", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
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
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $dumpsterSizes = $this->dumpsterSizeRepository->getAllDumpsterSizes($company, $filters, $sort, $paginate, $perPage);

        return ApiHelper::successResponse(
            $paginate,
            DumpsterSizeResource::collection($dumpsterSizes),
            'Dumpster sizes fetched successfully'
        );
    }

    /**
     * @OA\Get(
     *     path="/company/dumpster-sizes/{id}",
     *     summary="Get a single dumpster size",
     *     description="Fetch a single dumpster size by its ID for the authenticated company",
     *     tags={"Dumpster Sizes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the dumpster size",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dumpster size fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dumpster size fetched successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/DumpsterSizeResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dumpster size not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dumpster size not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */

    public function show(DumpsterSize $dumpsterSize)
    {
        $company = Auth::guard('company')->user();
        $dumpsterSize = $this->dumpsterSizeRepository->getDumpsterSizeById($company, $dumpsterSize->id);

        if (!$dumpsterSize) {
            return $this->sendErrorResponse('Dumpster size not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dumpster size fetched successfully',
            'data' => new DumpsterSizeResource($dumpsterSize),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/company/dumpster-sizes",
     *     summary="Create a new dumpster size",
     *     description="Create a new dumpster size for the authenticated company",
     *     tags={"Dumpster Sizes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Large Dumpster"),
     *             @OA\Property(property="code", type="string", example="LD-10YD"),
     *             @OA\Property(property="description", type="string", example="10 cubic yard dumpster for large projects"),
     *             @OA\Property(property="min_rental_days", type="integer", example=2),
     *             @OA\Property(property="max_rental_days", type="integer", example=14),
     *             @OA\Property(property="base_rent", type="number", format="float", example=250.00),
     *             @OA\Property(property="extra_day_rent", type="number", format="float", example=25.00),
     *             @OA\Property(property="overdue_rent", type="number", format="float", example=50.00),
     *             @OA\Property(property="volume_cubic_yards", type="number", format="float", example=10.00),
     *             @OA\Property(property="weight_limit_lbs", type="integer", example=2000),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="taxes", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dumpster size created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dumpster size created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/DumpsterSizeResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dumpster size already exists or validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dumpster size already exists"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dumpster size not created")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function store(DumpsterSizeCreateRequest $request)
    {
        $company = Auth::guard('company')->user();

        if ($company->dumpsterSizes()->where('code', $request->code)->exists()) {
            return $this->sendErrorResponse('Dumpster size already exists', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();
            $dumpsterSize = $this->dumpsterSizeRepository->createDumpsterSize($company, $request->validated());
            if (!$dumpsterSize) {
                return $this->sendErrorResponse('Dumpster size not created', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dumpster size created successfully',
                'data' => new DumpsterSizeResource($dumpsterSize),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Dumpster size creation failed: ' . $th->getMessage());
            return $this->sendErrorResponse('Dumpster size not created' . $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/company/dumpster-sizes/{id}",
     *     summary="Update a dumpster size",
     *     description="Update an existing dumpster size for the authenticated company",
     *     tags={"Dumpster Sizes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Dumpster size ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Large Dumpster"),
     *             @OA\Property(property="code", type="string", example="LD-10YD"),
     *             @OA\Property(property="description", type="string", example="10 cubic yard dumpster for large projects"),
     *             @OA\Property(property="min_rental_days", type="integer", example=2),
     *             @OA\Property(property="max_rental_days", type="integer", example=14),
     *             @OA\Property(property="base_rent", type="number", format="float", example=250.00),
     *             @OA\Property(property="extra_day_rent", type="number", format="float", example=25.00),
     *             @OA\Property(property="overdue_rent", type="number", format="float", example=50.00),
     *             @OA\Property(property="volume_cubic_yards", type="number", format="float", example=10.00),
     *             @OA\Property(property="weight_limit_lbs", type="integer", example=2000),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="taxes", type="array", @OA\Items(type="integer"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dumpster size updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dumpster size updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/DumpsterSizeResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dumpster size not updated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function update(DumpsterSizeUpdateRequest $request, DumpsterSize $dumpsterSize)
    {
        $company = Auth::guard('company')->user();
        try {
            DB::beginTransaction();

            $dumpsterSize = $this->dumpsterSizeRepository->updateDumpsterSize($company, $request->all(), $dumpsterSize->id);
            if (!$dumpsterSize) {
                return $this->sendErrorResponse('Dumpster size not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dumpster size updated successfully',
                'data' => new DumpsterSizeResource($dumpsterSize),
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Dumpster size update failed: ' . $th->getMessage());
            return $this->sendErrorResponse('Dumpster size not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Delete(
     *     path="/company/dumpster-sizes/{id}",
     *     summary="Delete a dumpster size",
     *     description="Delete an existing dumpster size for the authenticated company",
     *     tags={"Dumpster Sizes"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Dumpster size ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dumpster size deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dumpster size deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Dumpster size not deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(DumpsterSize $dumpsterSize)
    {
        $company = Auth::guard('company')->user();
        $this->dumpsterSizeRepository->deleteDumpsterSize($company, $dumpsterSize->id);
        if (!$dumpsterSize) {
            return $this->sendErrorResponse('Dumpster size not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'success' => true,
            'message' => 'Dumpster size deleted successfully',
            'data' => null,
        ], Response::HTTP_OK);
    }
}
