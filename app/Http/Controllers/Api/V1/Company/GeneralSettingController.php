<?php

namespace App\Http\Controllers\Api\V1\Company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\GeneralSettingResource;
use App\Repositories\GeneralSettingRepository;
use App\Models\GeneralSetting;
use App\Http\Requests\Company\GeneralSettingUpdateRequest;

class GeneralSettingController extends Controller
{
    private $generalSettingRepository;

    public function __construct(
        GeneralSettingRepository $generalSettingRepository
    ) {
        $this->generalSettingRepository = $generalSettingRepository;
    }

    /**
     * @OA\Get(
     *     path="/company/general-settings",
     *     summary="Get general settings",
     *     description="Fetch general settings for the authenticated company. Supports filtering, sorting, and optional pagination.",
     *     tags={"General Settings"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="filters[key]",
     *         in="query",
     *         description="Filter settings by key",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters[value]",
     *         in="query",
     *         description="Filter settings by value",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="filters[type]",
     *         in="query",
     *         description="Filter settings by type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order: 'asc' or 'desc'",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         description="Whether to paginate the results (true/false)",
     *         required=false,
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Number of items per page if pagination is enabled",
     *         required=false,
     *         @OA\Schema(type="integer", default=25)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number if pagination is enabled",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="General settings fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="General settings fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/GeneralSettingResource")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 nullable=true,
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=25),
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="first", type="string", example="http://example.com/api/general-settings?page=1"),
     *                     @OA\Property(property="last", type="string", example="http://example.com/api/general-settings?page=5"),
     *                     @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                     @OA\Property(property="next", type="string", nullable=true, example="http://example.com/api/general-settings?page=2")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
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

        $generalSettings = $this->generalSettingRepository->getAllGeneralSettings(
            $company,
            $filters,
            $sort,
            $paginate,
            $perPage
        );

        if ($paginate) {
            return response()->json([
                'success' => true,
                'message' => 'General settings fetched successfully',
                'data'    => GeneralSettingResource::collection($generalSettings),
                'meta'    => [
                    'current_page' => $generalSettings->currentPage(),
                    'last_page'    => $generalSettings->lastPage(),
                    'per_page'     => $generalSettings->perPage(),
                    'total'        => $generalSettings->total(),
                    'links'        => [
                        'first' => $generalSettings->url(1),
                        'last'  => $generalSettings->url($generalSettings->lastPage()),
                        'prev'  => $generalSettings->previousPageUrl(),
                        'next'  => $generalSettings->nextPageUrl(),
                    ],
                ],
            ]);
        }


        // Non-paginated response
        return response()->json([
            'success' => true,
            'message' => 'General settings fetched successfully',
            'data'    => GeneralSettingResource::collection($generalSettings)
        ]);
    }
    /**
     * @OA\Get(
     *     path="/company/general-settings/{id}",
     *     summary="Get a single general setting",
     *     description="Fetches a single general setting by its ID for a given model (polymorphic).",
     *     tags={"General Settings"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the general setting",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="General setting fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="General setting fetched successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/GeneralSettingResource")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="General setting not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="General setting not found")
     *         )
     *     )
     * )
     */
    public function show(GeneralSetting $generalSetting)
    {
        $company = Auth::guard('company')->user();
        $generalSetting = $this->generalSettingRepository->getGeneralSettingById($company, $generalSetting->id);
        return response()->json([
            'success' => true,
            'message' => 'General setting fetched successfully',
            'data'    => GeneralSettingResource::make($generalSetting)
        ]);
    }

    public function update(GeneralSettingUpdateRequest $request, GeneralSetting $generalSetting, $key)
    {
        $company = Auth::guard('company')->user();
        $generalSetting = $this->generalSettingRepository->updateGeneralSetting($company, $request->all(), $generalSetting->id, $key);
        if (!$generalSetting) {
            return $this->sendErrorResponse('General setting not found', Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'success' => true,
            'message' => 'General setting updated successfully',
            'data'    => GeneralSettingResource::make($generalSetting)
        ]);
    }
}
