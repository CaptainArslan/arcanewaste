<?php

namespace App\Http\Controllers\Api\V1\Company;

use Illuminate\Http\Request;
use App\Models\PaymentOption;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PaymentOptionResource;
use App\Repositories\PaymentOptionRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Company\PaymentOptionUpdateRequest;

class PaymentOptionController extends Controller
{
    private $paymentOptionRepository;

    public function __construct(
        PaymentOptionRepository $paymentOptionRepository
    ) {
        $this->paymentOptionRepository = $paymentOptionRepository;
    }

    /**
     * @OA\Get(
     *     path="/company/payment-options",
     *     summary="List payment options",
     *     description="Fetches a list of payment options for the authenticated company with filtering, sorting, and pagination support.",
     *     tags={"Payment Options"},
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="filters[id]",
     *         in="query",
     *         required=false,
     *         description="Filter by payment option ID",
     *         @OA\Schema(type="integer", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[name]",
     *         in="query",
     *         required=false,
     *         description="Filter by payment option name (partial match)",
     *         @OA\Schema(type="string", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[type]",
     *         in="query",
     *         required=false,
     *         description="Filter by payment option type",
     *         @OA\Schema(type="string", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[percentage]",
     *         in="query",
     *         required=false,
     *         description="Filter by percentage value",
     *         @OA\Schema(type="number", format="float", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[description]",
     *         in="query",
     *         required=false,
     *         description="Filter by description (partial match)",
     *         @OA\Schema(type="string", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[is_active]",
     *         in="query",
     *         required=false,
     *         description="Filter by active status",
     *         @OA\Schema(type="integer", enum={0,1}, example=null)
     *     ),
     *     @OA\Parameter(
     *         name="filters[company_id]",
     *         in="query",
     *         required=false,
     *         description="Filter by company ID",
     *         @OA\Schema(type="integer", example=null)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         required=false,
     *         description="Sort order (asc or desc). Default: desc",
     *         @OA\Schema(type="string", enum={"asc","desc"}, example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="paginate",
     *         in="query",
     *         required=false,
     *         description="Whether to paginate results (1/0). Default: 1",
     *         @OA\Schema(type="integer", enum={0,1}, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of results per page (if paginate=1).",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Payment options fetched successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment options fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/PaymentOptionResource")
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=72),
     *                 @OA\Property(property="links", type="object",
     *                     @OA\Property(property="first", type="string", example="http://api.example.com/company/payment-options?page=1"),
     *                     @OA\Property(property="last", type="string", example="http://api.example.com/company/payment-options?page=5"),
     *                     @OA\Property(property="prev", type="string", example=null),
     *                     @OA\Property(property="next", type="string", example="http://api.example.com/company/payment-options?page=2")
     *                 )
     *             )
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

        $paymentOptions = $this->paymentOptionRepository->getAllPaymentOptions($company, $filters, $sort, $paginate, $perPage);

        if ($paginate) {
            return response()->json([
                'success' => true,
                'message' => 'Payment options fetched successfully',
                'data'    => PaymentOptionResource::collection($paymentOptions),
                'meta'    => [
                    'current_page' => $paymentOptions->currentPage(),
                    'last_page'    => $paymentOptions->lastPage(),
                    'per_page'     => $paymentOptions->perPage(),
                    'total'        => $paymentOptions->total(),
                    'links'        => [
                        'first' => $paymentOptions->url(1),
                        'last'  => $paymentOptions->url($paymentOptions->lastPage()),
                        'prev'  => $paymentOptions->previousPageUrl(),
                        'next'  => $paymentOptions->nextPageUrl(),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment options fetched successfully',
            'data'    => PaymentOptionResource::collection($paymentOptions)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/company/payment-options/{id}",
     *     summary="Get payment option details",
     *     description="Fetch a specific payment option by its ID for the authenticated company.",
     *     tags={"Payment Options"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment option ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Payment option fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment option fetched successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/PaymentOptionResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Payment option not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Payment option not found")
     *         )
     *     ),
     *
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
    public function show(PaymentOption $paymentOption)
    {
        $company = Auth::guard('company')->user();
        $paymentOption = $this->paymentOptionRepository->getPaymentOptionById($company, $paymentOption->id);
        return response()->json([
            'success' => true,
            'message' => 'Payment option fetched successfully',
            'data'    => PaymentOptionResource::make($paymentOption)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/company/payment-options/{id}/{type}",
     *     summary="Update a payment option",
     *     description="Update specific fields of a payment option for the authenticated company. 
     *         Only partial updates are allowed (you do not need to send all fields).",
     *     tags={"Payment Options"},
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment option ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Type of update (e.g. 'partial')",
     *         @OA\Schema(type="string", example="partial")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="Upfront Payment"),
     *             @OA\Property(property="percentage", type="number", format="float", example=10),
     *             @OA\Property(property="description", type="string", example="Upfront 50% payment option"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Payment option updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Payment option updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/PaymentOptionResource"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Payment option not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Payment option not found")
     *         )
     *     ),
     *
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
    public function update(PaymentOptionUpdateRequest $request, PaymentOption $paymentOption, $type)
    {
        $company = Auth::guard('company')->user();
        $paymentOption = $this->paymentOptionRepository->updatePaymentOption($company, $request->all(), $paymentOption->id, $type);

        if (!$paymentOption) {
            return $this->sendErrorResponse('Payment option not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment option updated successfully',
            'data'    => PaymentOptionResource::make($paymentOption)
        ]);
    }
}
