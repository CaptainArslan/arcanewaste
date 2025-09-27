<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DumpsterResource;
use Symfony\Component\HttpFoundation\Response;
use App\Repositories\Company\DumpsterRepository;
use App\Http\Requests\Company\DumpsterCreateRequest;


class DumpsterController extends Controller
{
    private $dumpsterRepository;

    public function __construct(DumpsterRepository $dumpsterRepository)
    {
        $this->dumpsterRepository = $dumpsterRepository;
    }

    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $dumpsters = $this->dumpsterRepository->getAllDumpsters($company, $filters, $sort, $paginate, $perPage);

        return ApiHelper::successResponse(
            $paginate,
            DumpsterResource::collection($dumpsters),
            'Dumpsters fetched successfully'
        );
    }

    public function show($id)
    {
        $company = Auth::guard('company')->user();
        $dumpster = $this->dumpsterRepository->getDumpsterById($company, $id);

        return response()->json([
            'success' => true,
            'message' => 'Dumpster fetched successfully',
            'data' => new DumpsterResource($dumpster),
        ], Response::HTTP_OK);
    }

    public function store(DumpsterCreateRequest $request)
    { 
        $company = Auth::guard('company')->user();

        $dumpster = $this->dumpsterRepository->createDumpster($company, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Dumpster created successfully',
            'data' => new DumpsterResource($dumpster),
        ], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $company = Auth::guard('company')->user();
        $dumpster = $this->dumpsterRepository->updateDumpster($company, $id, $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Dumpster updated successfully',
            'data' => new DumpsterResource($dumpster),
        ], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $company = Auth::guard('company')->user();
        $dumpster = $this->dumpsterRepository->deleteDumpster($company, $id);
        return response()->json([
            'success' => true,
            'message' => 'Dumpster deleted successfully',
        ], Response::HTTP_OK);
    }
}
