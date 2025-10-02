<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DumpsterSizeCreateRequest;
use App\Http\Requests\Company\DumpsterSizeUpdateRequest;
use App\Http\Resources\DumpsterSizeResource;
use App\Models\DumpsterSize;
use App\Repositories\Company\DumpsterSizeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DumpsterSizeController extends Controller
{
    private $dumpsterSizeRepository;

    public function __construct(
        DumpsterSizeRepository $dumpsterSizeRepository
    ) {
        $this->dumpsterSizeRepository = $dumpsterSizeRepository;
    }

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

    public function show(DumpsterSize $dumpsterSize)
    {
        $company = Auth::guard('company')->user();
        $dumpsterSize = $this->dumpsterSizeRepository->getDumpsterSizeById($company, $dumpsterSize->id);

        if (! $dumpsterSize) {
            return $this->sendErrorResponse('Dumpster size not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dumpster size fetched successfully',
            'data' => new DumpsterSizeResource($dumpsterSize),
        ]);
    }


    public function store(DumpsterSizeCreateRequest $request)
    {
        $company = Auth::guard('company')->user();

        if ($company->dumpsterSizes()->where('code', $request->code)->exists()) {
            return $this->sendErrorResponse('Dumpster size already exists', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();
            $dumpsterSize = $this->dumpsterSizeRepository->createDumpsterSize($company, $request->validated());
            if (! $dumpsterSize) {
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

    public function update(DumpsterSizeUpdateRequest $request, DumpsterSize $dumpsterSize)
    {
        $company = Auth::guard('company')->user();
        try {
            DB::beginTransaction();

            $dumpsterSize = $this->dumpsterSizeRepository->updateDumpsterSize($company, $request->all(), $dumpsterSize->id);
            if (! $dumpsterSize) {
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

    public function destroy(DumpsterSize $dumpsterSize)
    {
        $company = Auth::guard('company')->user();
        $this->dumpsterSizeRepository->deleteDumpsterSize($company, $dumpsterSize->id);
        if (! $dumpsterSize) {
            return $this->sendErrorResponse('Dumpster size not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dumpster size deleted successfully',
            'data' => null,
        ], Response::HTTP_OK);
    }
}
