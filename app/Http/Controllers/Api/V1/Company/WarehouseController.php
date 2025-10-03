<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\WareHouseCreateRequest;
use App\Http\Requests\Company\WareHouseUpdateRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use App\Repositories\Company\WarehouseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class WarehouseController extends Controller
{
    private $warehouseRepository;

    public function __construct(
        WarehouseRepository $warehouseRepository
    ) {
        $this->warehouseRepository = $warehouseRepository;
    }

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
                'data' => WarehouseResource::collection($warehouses),
                'meta' => [
                    'current_page' => $warehouses->currentPage(),
                    'last_page' => $warehouses->lastPage(),
                    'per_page' => $warehouses->perPage(),
                    'total' => $warehouses->total(),
                    'links' => [
                        'first' => $warehouses->url(1),
                        'last' => $warehouses->url($warehouses->lastPage()),
                        'prev' => $warehouses->previousPageUrl(),
                        'next' => $warehouses->nextPageUrl(),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Warehouses fetched successfully',
            'data' => WarehouseResource::collection($warehouses),
        ]);
    }

    public function show(Warehouse $warehouse)
    {
        $company = Auth::guard('company')->user();
        $warehouse = $this->warehouseRepository->getWarehouseById($company, $warehouse->id);
        if (! $warehouse) {
            return $this->sendErrorResponse('Warehouse not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Warehouse fetched successfully',
            'data' => new WarehouseResource($warehouse),
        ]);
    }

    
    public function store(WareHouseCreateRequest $request)
    {
        $company = Auth::guard('company')->user();
        try {
            DB::beginTransaction();
            $warehouse = $this->warehouseRepository->createWarehouse($company, $request->validated());

            if (! $warehouse) {
                return $this->sendErrorResponse('Warehouse not created', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Warehouse created successfully',
                'data' => new WarehouseResource($warehouse),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Warehouse creation failed: '.$th->getMessage());

            return $this->sendErrorResponse('Warehouse not created', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Warehouse $warehouse, WareHouseUpdateRequest $request)
    {
        $company = Auth::guard('company')->user();
        DB::beginTransaction();
        $warehouse = $this->warehouseRepository->updateWarehouse($company, $warehouse->id, $request->validated());

        if (! $warehouse) {
            return $this->sendErrorResponse('Warehouse not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Warehouse updated successfully',
            'data' => new WarehouseResource($warehouse),
        ]);
    }

    public function destroy(Warehouse $warehouse)
    {
        $company = Auth::guard('company')->user();
        $warehouse = $this->warehouseRepository->deleteWarehouse($company, $warehouse->id);
        if (! $warehouse) {
            return $this->sendErrorResponse('Warehouse not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Warehouse deleted successfully',
            'data' => null,
        ]);
    }
}
