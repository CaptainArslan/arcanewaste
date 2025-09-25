<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WarehouseResource;
use App\Repositories\WarehouseRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Company\WareHouseCreateRequest;
use App\Http\Requests\Company\WareHouseUpdateRequest;

class WarehouseController extends Controller
{
    private $warehouseRepository;

    public function __construct(
        WarehouseRepository $warehouseRepository
    ) {
        $this->warehouseRepository = $warehouseRepository;
    }

    /**
     * @OA\Get(
     *     path="/company/warehouses",
     *     summary="List warehouses",
     *     description="Fetches a list of warehouses for the authenticated company with filtering, sorting, and pagination support. All filters default to null.",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="filters[name]",
     *         in="query",
     *         required=false,
     *         description="Filter by warehouse name (partial match). Default: null",
     *         @OA\Schema(type="string", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[code]",
     *         in="query",
     *         required=false,
     *         description="Filter by warehouse code (partial match). Default: null",
     *         @OA\Schema(type="string", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[type]",
     *         in="query",
     *         required=false,
     *         description="Filter by warehouse type (partial match). Default: null",
     *         @OA\Schema(type="string", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[capacity]",
     *         in="query",
     *         required=false,
     *         description="Filter by warehouse capacity (partial match). Default: null",
     *         @OA\Schema(type="integer", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[is_active]",
     *         in="query",
     *         required=false,
     *         description="Filter by active status (0 = inactive, 1 = active). Default: null",
     *         @OA\Schema(type="integer", enum={0,1}, example=null)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort order (asc or desc). Default: desc",
     *         @OA\Schema(type="string", enum={"asc","desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         description="Whether to paginate results (true/false). Default: true",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of results per page (if paginate=true). Default: 15",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Warehouses fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Warehouses fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/WarehouseResource")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=72),
     *                 @OA\Property(property="links", type="object",
     *                     @OA\Property(property="first", type="string", example="http://api.example.com/company/warehouses?page=1"),
     *                     @OA\Property(property="last", type="string", example="http://api.example.com/company/warehouses?page=5"),
     *                     @OA\Property(property="prev", type="string", example=null),
     *                     @OA\Property(property="next", type="string", example="http://api.example.com/company/warehouses?page=2")
     *                 )
     *             )
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

        $warehouses = $this->warehouseRepository->getAllWarehouses($company, $filters, $sort, $paginate, $perPage);

        if ($paginate) {
            return response()->json([
                'success' => true,
                'message' => 'Warehouses fetched successfully',
                'data'    => WarehouseResource::collection($warehouses),
                'meta'    => [
                    'current_page' => $warehouses->currentPage(),
                    'last_page'    => $warehouses->lastPage(),
                    'per_page'     => $warehouses->perPage(),
                    'total'        => $warehouses->total(),
                    'links'        => [
                        'first' => $warehouses->url(1),
                        'last'  => $warehouses->url($warehouses->lastPage()),
                        'prev'  => $warehouses->previousPageUrl(),
                        'next'  => $warehouses->nextPageUrl(),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Warehouses fetched successfully',
            'data'    => WarehouseResource::collection($warehouses)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/company/warehouses/{id}",
     *     summary="Get a single warehouse",
     *     description="Fetches a single warehouse by its ID for the authenticated company.",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the warehouse",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Warehouse fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Warehouse fetched successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warehouse not found")
     *         )
     *     )
     * )
     */
    public function show(Warehouse $warehouse)
    {
        $company = Auth::guard('company')->user();
        $warehouse = $this->warehouseRepository->getWarehouseById($company, $warehouse->id);
        if (!$warehouse) {
            return $this->sendErrorResponse('Warehouse not found', Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'success' => true,
            'message' => 'Warehouse fetched successfully',
            'data'    => new WarehouseResource($warehouse)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/company/warehouses",
     *     summary="Create a warehouse",
     *     description="Creates a new warehouse for the authenticated company",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Main Warehouse"),
     *             @OA\Property(property="code", type="string", example="WH001"),
     *             @OA\Property(property="type", type="string", example="Dumpster"),
     *             @OA\Property(property="capacity", type="integer", example=50),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="address", type="object",
     *                 @OA\Property(property="address_line1", type="string", example="123 Street"),
     *                 @OA\Property(property="address_line2", type="string", example="Apt 4B"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="state", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="USA"),
     *                 @OA\Property(property="latitude", type="number", format="float", example=40.7128),
     *                 @OA\Property(property="longitude", type="number", format="float", example=-74.0060),
     *                 @OA\Property(property="is_primary", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Warehouse created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Warehouse created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The name field is required."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function store(WareHouseCreateRequest $request)
    {
        $company = Auth::guard('company')->user();
        try {
            DB::beginTransaction();
            $warehouse = $this->warehouseRepository->createWarehouse($company, $request->validated());

            if (!$warehouse) {
                return $this->sendErrorResponse('Warehouse not created', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Warehouse created successfully',
                'data'    => new WarehouseResource($warehouse)
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Warehouse creation failed: ' . $th->getMessage());
            return $this->sendErrorResponse('Warehouse not created', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     *     path="/company/warehouses/{warehouse}",
     *     summary="Update a warehouse",
     *     description="Updates an existing warehouse for the authenticated company. Only provided fields will be updated.",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         required=true,
     *         description="ID of the warehouse to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Main Warehouse"),
     *             @OA\Property(property="code", type="string", example="WH001"),
     *             @OA\Property(property="type", type="string", example="Dumpster"),
     *             @OA\Property(property="capacity", type="integer", example=50),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *             @OA\Property(property="address", type="object",
     *                 @OA\Property(property="address_line1", type="string", example="123 Street"),
     *                 @OA\Property(property="address_line2", type="string", example="Apt 4B"),
     *                 @OA\Property(property="city", type="string", example="New York"),
     *                 @OA\Property(property="state", type="string", example="NY"),
     *                 @OA\Property(property="postal_code", type="string", example="10001"),
     *                 @OA\Property(property="country", type="string", example="USA"),
     *                 @OA\Property(property="latitude", type="number", format="float", example=40.7128),
     *                 @OA\Property(property="longitude", type="number", format="float", example=-74.0060),
     *                 @OA\Property(property="is_primary", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Warehouse updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Warehouse updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/WarehouseResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Warehouse not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warehouse not found")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The code field is required."),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function update(Warehouse $warehouse, WareHouseUpdateRequest $request)
    {
        $company = Auth::guard('company')->user();
        DB::beginTransaction();
        $warehouse = $this->warehouseRepository->updateWarehouse($company, $warehouse->id, $request->validated());

        if (!$warehouse) {
            return $this->sendErrorResponse('Warehouse not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Warehouse updated successfully',
            'data'    => new WarehouseResource($warehouse)
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/company/warehouses/{warehouse}",
     *     summary="Delete a warehouse",
     *     description="Deletes a warehouse for the authenticated company.",
     *     tags={"Warehouses"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="warehouse",
     *         in="path",
     *         required=true,
     *         description="ID of the warehouse to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Warehouse deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Warehouse deleted successfully"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Warehouse not deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Warehouse not deleted")
     *         )
     *     )
     * )
     */
    public function destroy(Warehouse $warehouse)
    {
        $company = Auth::guard('company')->user();
        $warehouse = $this->warehouseRepository->deleteWarehouse($company, $warehouse->id);
        if (!$warehouse) {
            return $this->sendErrorResponse('Warehouse not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'success' => true,
            'message' => 'Warehouse deleted successfully',
            'data' => null
        ]);
    }
}
