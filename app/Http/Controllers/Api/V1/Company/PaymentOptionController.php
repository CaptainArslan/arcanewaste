<?php

namespace App\Http\Controllers\Api\V1\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\PaymentOptionUpdateRequest;
use App\Http\Resources\PaymentOptionResource;
use App\Models\PaymentOption;
use App\Notifications\FcmDatabaseNotification;
use App\Repositories\PaymentOptionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PaymentOptionController extends Controller
{
    private $paymentOptionRepository;

    public function __construct(
        PaymentOptionRepository $paymentOptionRepository
    ) {
        $this->paymentOptionRepository = $paymentOptionRepository;
    }

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
                'data' => PaymentOptionResource::collection($paymentOptions),
                'meta' => [
                    'current_page' => $paymentOptions->currentPage(),
                    'last_page' => $paymentOptions->lastPage(),
                    'per_page' => $paymentOptions->perPage(),
                    'total' => $paymentOptions->total(),
                    'links' => [
                        'first' => $paymentOptions->url(1),
                        'last' => $paymentOptions->url($paymentOptions->lastPage()),
                        'prev' => $paymentOptions->previousPageUrl(),
                        'next' => $paymentOptions->nextPageUrl(),
                    ],
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment options fetched successfully',
            'data' => PaymentOptionResource::collection($paymentOptions),
        ]);
    }

    public function show(PaymentOption $paymentOption)
    {
        $company = Auth::guard('company')->user();
        $paymentOption = $this->paymentOptionRepository->getPaymentOptionById($company, $paymentOption->id);

        return response()->json([
            'success' => true,
            'message' => 'Payment option fetched successfully',
            'data' => PaymentOptionResource::make($paymentOption),
        ]);
    }

    public function update(PaymentOptionUpdateRequest $request, PaymentOption $paymentOption, $type)
    {
        $company = Auth::guard('company')->user();
        $paymentOption = $this->paymentOptionRepository->updatePaymentOption($company, $request->all(), $paymentOption->id, $type);

        if (! $paymentOption) {
            return $this->sendErrorResponse('Payment option not found', Response::HTTP_NOT_FOUND);
        }

        $company->notify(new FcmDatabaseNotification(
            'Payment option updated',
            "Payment option updated for {$paymentOption->name}",
            []
        ));

        return response()->json([
            'success' => true,
            'message' => 'Payment option updated successfully',
            'data' => PaymentOptionResource::make($paymentOption),
        ]);
    }
}
