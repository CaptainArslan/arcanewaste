<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\SyncTimingsRequest;
use App\Http\Resources\TimingResource;
use App\Models\Timing;
use App\Repositories\TimingRepsitory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimingsController extends Controller
{
    protected $timingRepository;

    public function __construct(TimingRepsitory $timingRepository)
    {
        $this->timingRepository = $timingRepository;
    }

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

    public function update(SyncTimingsRequest $request)
    {
        $company = Auth::guard('company')->user();
        $timing = $this->timingRepository->syncCompanyTimings($company, $request->timings);

        return response()->json([
            'success' => true,
            'message' => 'Timing updated successfully',
            'data' => TimingResource::collection($timing),
        ]);
    }
}
