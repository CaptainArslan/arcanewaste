<?php

namespace App\Http\Controllers\Api\V1\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TimingRepsitory;
use App\Http\Resources\TimingResource;
use App\Http\Requests\Company\SyncTimingsRequest;
use App\Models\Timing;

class TimingsController extends Controller
{
    protected $timingRepository;

    public function __construct(TimingRepsitory $timingRepository)
    {
        $this->timingRepository = $timingRepository;
    }

    /**
     * @OA\Get(
     *     path="/company/timings",
     *     summary="Get company timings",
     *     description="Fetch all timings for the authenticated company with optional filters, sorting, and pagination",
     *     tags={"Timings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="filters[day_of_week]",
     *         in="query",
     *         description="Filter by day of week (monday, tuesday, ...)",
     *         required=false,
     *         @OA\Schema(type="string", default=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[is_closed]",
     *         in="query",
     *         description="Filter by closed/open status",
     *         required=false,
     *         @OA\Schema(type="boolean", default=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[opens_at]",
     *         in="query",
     *         description="Filter by opening time",
     *         required=false,
     *         @OA\Schema(type="string", format="time", default=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[closes_at]",
     *         in="query",
     *         description="Filter by closing time",
     *         required=false,
     *         @OA\Schema(type="string", format="time", default=null)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sorting order: asc or desc",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc","desc"}, default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Whether to paginate results",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timings fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Timings fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TimingResource")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=7),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="first", type="string", example="url_to_first_page"),
     *                     @OA\Property(property="last", type="string", example="url_to_last_page"),
     *                     @OA\Property(property="prev", type="string", example=null),
     *                     @OA\Property(property="next", type="string", example=null)
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
        $timings = $this->timingRepository->getAllTimings($company, $filters, $sort, $paginate, $perPage);

        if ($paginate) {
            return response()->json([
                'success' => true,
                'message' => 'Timings fetched successfully',
                'data' => TimingResource::collection($timings),
                'meta' => [
                    'current_page' => $timings->currentPage(),
                    'last_page' => $timings->lastPage(),
                    'per_page' => $timings->perPage(),
                    'total' => $timings->total(),
                    'links' => [
                        'first' => $timings->url(1),
                        'last' => $timings->url($timings->lastPage()),
                        'prev' => $timings->previousPageUrl(),
                        'next' => $timings->nextPageUrl(),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Timings fetched successfully',
            'data' => TimingResource::collection($timings),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/company/timings/{id}",
     *     summary="Get a single timing",
     *     description="Fetch a specific timing for the authenticated company by ID",
     *     tags={"Timings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the timing",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timing fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Timing fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/TimingResource"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Timing not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Timing not found")
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
    public function show(Request $request, Timing $timing)
    {
        $company = Auth::guard('company')->user();
        $timing = $this->timingRepository->getTimingById($company, $timing->id);
        return response()->json([
            'success' => true,
            'message' => 'Timing fetched successfully',
            'data' => new TimingResource($timing),
        ]);
    }
    /**
     * @OA\Put(
     *     path="/company/timings/sync",
     *     summary="Sync company timings",
     *     description="Delete old timings and create new weekly timings for the authenticated company. Only timings sent in the payload will exist after this operation.",
     *     tags={"Timings"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="timings",
     *                 type="array",
     *                 description="Array of timings to sync for the week",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"day_of_week","opens_at","closes_at","is_closed"},
     *                     @OA\Property(property="day_of_week", type="string", example="monday", description="Day of the week (lowercase)"),
     *                     @OA\Property(property="opens_at", type="string", format="time", example="09:00:00", description="Opening time"),
     *                     @OA\Property(property="closes_at", type="string", format="time", example="18:00:00", description="Closing time"),
     *                     @OA\Property(property="is_closed", type="boolean", example=false, description="Whether the day is closed")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timings synced successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Timing updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Array of updated timings",
     *                 @OA\Items(ref="#/components/schemas/TimingResource")
     *             )
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
    public function update(SyncTimingsRequest $request)
    {
        $company = Auth::guard('company')->user();
        $timing = $this->timingRepository->syncCompanyTimings($company, $request->timings);
        return response()->json([
            'success' => true,
            'message' => 'Timing updated successfully',
            'data' => TimingResource::collection($timing)
        ]);
    }
}
