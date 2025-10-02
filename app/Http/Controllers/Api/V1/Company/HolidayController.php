<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\HolidayCreateRequest;
use App\Http\Requests\Company\HolidayUpdateRequest;
use App\Http\Resources\CompanyHolidayResource;
use App\Repositories\Company\HolidayRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HolidayController extends Controller
{
    private $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    public function daysOfWeekOptions()
    {
        $daysOfWeekOptions = $this->holidayRepository->getDaysOfWeekOptions();

        return response()->json([
            'success' => true,
            'message' => 'Company days of week options fetched successfully',
            'data' => $daysOfWeekOptions,
        ]);
    }

    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $holidays = $this->holidayRepository->getAllHolidays($company, $filters, $sort, $paginate, $perPage);

        return ApiHelper::successResponse(
            $paginate,
            CompanyHolidayResource::collection($holidays),
            'Company holidays fetched successfully'
        );
    }

    public function show($id)
    {
        $company = Auth::guard('company')->user();
        $holiday = $this->holidayRepository->getHolidayById($company, $id);

        return response()->json([
            'success' => true,
            'message' => 'Company holiday fetched successfully',
            'data' => CompanyHolidayResource::make($holiday),
        ]);
    }

    public function store(HolidayCreateRequest $request)
    {
        $company = Auth::guard('company')->user();
        $holiday = $this->holidayRepository->createHoliday($company, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Company holiday created successfully',
            'data' => CompanyHolidayResource::make($holiday),
        ]);
    }

    public function update(HolidayUpdateRequest $request, $id)
    {
        $company = Auth::guard('company')->user();
        $holiday = $this->holidayRepository->updateHoliday($company, $request->all(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Company holiday updated successfully',
            'data' => CompanyHolidayResource::make($holiday),
        ]);
    }

    public function destroy($id)
    {
        $company = Auth::guard('company')->user();
        $this->holidayRepository->deleteHoliday($company, $id);

        return response()->json([
            'success' => true,
            'message' => 'Company holiday deleted successfully',
            'data' => null,
        ]);
    }
}
