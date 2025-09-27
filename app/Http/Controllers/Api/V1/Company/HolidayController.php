<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CompanyHolidayResource;
use App\Repositories\Company\HolidayRepository;
use App\Http\Requests\Company\HolidayCreateRequest;
use App\Http\Requests\Company\HolidayUpdateRequest;

class HolidayController extends Controller
{
    private $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    /**
     * @OA\Get(
     *     path="/days-of-week-options",
     *     summary="Get days of week options",
     *     description="Fetches all days of the week as options with numeric values and string labels.",
     *     tags={"Company Holidays"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Days of week options fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company days of week options fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="value", type="integer", example=0, description="Numeric representation of the day (0=Sunday, 6=Saturday)"),
     *                     @OA\Property(property="label", type="string", example="Sunday", description="Readable name of the day")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function daysOfWeekOptions()
    {
        $daysOfWeekOptions = $this->holidayRepository->getDaysOfWeekOptions();
        return response()->json([
            'success' => true,
            'message' => 'Company days of week options fetched successfully',
            'data'    => $daysOfWeekOptions
        ]);
    }

    /**
     * @OA\Get(
     *     path="/company/holidays",
     *     summary="Get company holidays",
     *     description="Fetch a list of holidays for the authenticated company with filtering, sorting, and pagination.",
     *     tags={"Company Holidays"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(name="id", in="query", required=false, @OA\Schema(type="integer", nullable=true, example=1)),
     *     @OA\Parameter(name="company_id", in="query", required=false, @OA\Schema(type="integer", nullable=true, example=2)),
     *     @OA\Parameter(name="driver_id", in="query", required=false, @OA\Schema(type="integer", nullable=true, example=5)),
     *     @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string", nullable=true, example="Christmas")),
     *     @OA\Parameter(name="date", in="query", required=false, @OA\Schema(type="string", format="date", nullable=true, example="2025-12-25")),
     *     @OA\Parameter(name="from_date", in="query", required=false, @OA\Schema(type="string", format="date", nullable=true, example="2025-01-01")),
     *     @OA\Parameter(name="to_date", in="query", required=false, @OA\Schema(type="string", format="date", nullable=true, example="2025-12-31")),
     *     @OA\Parameter(
     *         name="recurrence_type", in="query", required=false,
     *         @OA\Schema(type="string", nullable=true, enum={"none","weekly","monthly","yearly"}, example="yearly")
     *     ),
     *     @OA\Parameter(name="day_of_week", in="query", required=false, @OA\Schema(type="integer", nullable=true, example=1, description="0=Sunday .. 6=Saturday")),
     *     @OA\Parameter(name="month_day", in="query", required=false, @OA\Schema(type="integer", nullable=true, example=25, description="Day of month (1-31)")),
     *     @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean", nullable=true, example=true)),
     *     @OA\Parameter(name="is_approved", in="query", required=false, @OA\Schema(type="boolean", nullable=true, example=true)),
     *
     *     @OA\Parameter(name="sort", in="query", required=false, @OA\Schema(type="string", enum={"asc","desc"}, default="desc")),
     *     @OA\Parameter(name="paginate", in="query", required=false, @OA\Schema(type="boolean", default=true, example=true)),
     *     @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", default=10, example=10)),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company holidays fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company holidays fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CompanyHolidayResourceSchema")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */


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

    /**
     * @OA\Get(
     *     path="/company/holidays/{id}",
     *     summary="Get a specific company holiday",
     *     description="Fetch a single holiday record for the authenticated company by its ID.",
     *     tags={"Company Holidays"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the holiday to fetch",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company holiday fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company holiday fetched successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CompanyHolidayResourceSchema")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Holiday not found")
     * )
     */

    public function show($id)
    {
        $company = Auth::guard('company')->user();
        $holiday = $this->holidayRepository->getHolidayById($company, $id);

        return response()->json([
            'success' => true,
            'message' => 'Company holiday fetched successfully',
            'data'    => CompanyHolidayResource::make($holiday)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/company/holidays",
     *     summary="Create a company holiday",
     *     description="Create a new holiday record for the authenticated company.",
     *     tags={"Company Holidays"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","date","recurrence_type"},
     *             @OA\Property(property="name", type="string", example="Independence Day"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-08-14"),
     *             @OA\Property(property="recurrence_type", type="string", enum={"none","weekly","yearly"}, example="yearly"),
     *             @OA\Property(property="day_of_week", type="integer", nullable=true, minimum=0, maximum=6, example=5, description="0=Sunday ... 6=Saturday"),
     *             @OA\Property(property="month_day", type="string", nullable=true, example="08-14", description="Used when recurrence_type is yearly"),
     *             @OA\Property(property="reason", type="string", nullable=true, example="National holiday"),
     *             @OA\Property(property="is_approved", type="string", enum={"approved","pending","rejected"}, example="approved"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Company holiday created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company holiday created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/CompanyHolidayResourceSchema")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(HolidayCreateRequest $request)
    {
        $company = Auth::guard('company')->user();
        $holiday = $this->holidayRepository->createHoliday($company, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Company holiday created successfully',
            'data'    => CompanyHolidayResource::make($holiday)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/company/holidays/{id}",
     *     summary="Update a company holiday",
     *     description="Update an existing company holiday for the authenticated company.",
     *     operationId="updateCompanyHoliday",
     *     tags={"Company Holidays"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Holiday ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Independence Day"),
     *             @OA\Property(property="date", type="string", format="date", example="2025-08-14"),
     *             @OA\Property(property="recurrence_type", type="string", enum={"none","weekly","yearly"}, example="yearly"),
     *             @OA\Property(property="day_of_week", type="integer", nullable=true, minimum=0, maximum=6, example=null),
     *             @OA\Property(property="month_day", type="string", nullable=true, example="08-14"),
     *             @OA\Property(property="reason", type="string", example="National holiday"),
     *             @OA\Property(property="is_approved", type="string", enum={"approved","pending","rejected"}, example="approved"),
     *             @OA\Property(property="is_active", type="boolean", example=true),
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Company holiday updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Company holiday updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/CompanyHolidayResourceSchema"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Holiday not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Holiday not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={"name": {"The name field is required."}}
     *             )
     *         )
     *     )
     * )
     */
    public function update(HolidayUpdateRequest $request, $id)
    {
        $company = Auth::guard('company')->user();
        $holiday = $this->holidayRepository->updateHoliday($company, $request->all(), $id);
        return response()->json([
            'success' => true,
            'message' => 'Company holiday updated successfully',
            'data'    => CompanyHolidayResource::make($holiday)
        ]);
    }

    
    public function destroy($id)
    {
        $company = Auth::guard('company')->user();
        $this->holidayRepository->deleteHoliday($company, $id);
        return response()->json([
            'success' => true,
            'message' => 'Company holiday deleted successfully',
            'data'    => null
        ]);
    }
}
