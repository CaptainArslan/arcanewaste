<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\DriverCreateRequest;
use App\Http\Requests\Company\DriverUpdateRequest;
use App\Http\Resources\CompanyDriverResource;
use App\Models\Driver;
use App\Repositories\Company\DriverRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
{
    private $driverRepository;

    public function __construct(DriverRepository $driverRepository)
    {
        $this->driverRepository = $driverRepository;
    }

    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $drivers = $this->driverRepository->getAllDrivers($company, $filters, $sort, $paginate, $perPage);

        return ApiHelper::successResponse(
            $paginate,
            CompanyDriverResource::collection($drivers),
            'Drivers fetched successfully'
        );
    }

    public function show(Driver $driver)
    {
        $company = Auth::guard('company')->user();
        $driver = $this->driverRepository->getDriverById($company, $driver->id);

        if (! $driver) {
            return $this->sendErrorResponse('Driver not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver fetched successfully',
            'data' => new CompanyDriverResource($driver),
        ], Response::HTTP_OK);
    }

    public function store(DriverCreateRequest $request)
    {
        $company = Auth::guard('company')->user();
        $driver = $this->driverRepository->createDriver($company, $request->all());

        if (! $driver) {
            return $this->sendErrorResponse('Driver not created', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver created successfully',
            'data' => new CompanyDriverResource($driver),
        ], Response::HTTP_OK);
    }

    public function update(DriverUpdateRequest $request, Driver $driver)
    {
        $company = Auth::guard('company')->user();
        $driver = $this->driverRepository->updateDriver($company, $request->all(), $driver);
        if (! $driver) {
            return $this->sendErrorResponse('Driver not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver updated successfully',
            'data' => new CompanyDriverResource($driver),
        ], Response::HTTP_OK);
    }

    public function destroy(Driver $driver)
    {
        $company = Auth::guard('company')->user();
        $isDeleted = $this->driverRepository->deleteDriver($company, $driver->id);
        if (! $isDeleted) {
            return $this->sendErrorResponse('Driver not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver deleted successfully',
        ], Response::HTTP_OK);
    }

    public function terminate(Driver $driver)
    {
        $company = Auth::guard('company')->user();
        $isTerminated = $this->driverRepository->terminateDriver($company, $driver->id);
        if (! $isTerminated) {
            return $this->sendErrorResponse('Driver not terminated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Driver terminated successfully',
        ], Response::HTTP_OK);
    }
}
