<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\GeneralSettingUpdateRequest;
use App\Http\Resources\GeneralSettingResource;
use App\Models\GeneralSetting;
use App\Repositories\GeneralSettingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GeneralSettingController extends Controller
{
    private $generalSettingRepository;

    public function __construct(
        GeneralSettingRepository $generalSettingRepository
    ) {
        $this->generalSettingRepository = $generalSettingRepository;
    }
    
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

        return ApiHelper::successResponse(
            $paginate,
            GeneralSettingResource::collection($generalSettings),
            'General settings fetched successfully'
        );
    }

    public function show(GeneralSetting $generalSetting)
    {
        $company = Auth::guard('company')->user();
        $generalSetting = $this->generalSettingRepository->getGeneralSettingById($company, $generalSetting->id);

        return response()->json([
            'success' => true,
            'message' => 'General setting fetched successfully',
            'data' => GeneralSettingResource::make($generalSetting),
        ]);
    }

    public function update(GeneralSettingUpdateRequest $request, GeneralSetting $generalSetting, $key)
    {
        $company = Auth::guard('company')->user();
        $generalSetting = $this->generalSettingRepository->updateGeneralSetting($company, $request->all(), $generalSetting->id, $key);

        if (! $generalSetting) {
            return $this->sendErrorResponse('Setting not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'General setting updated successfully',
            'data' => GeneralSettingResource::make($generalSetting),
        ]);
    }
}
