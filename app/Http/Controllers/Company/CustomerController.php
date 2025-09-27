<?php

namespace App\Http\Controllers\Company;

use App\Models\Customer;
use App\Helpers\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\CompanyCustomerResource;
use App\Repositories\Company\CustomerRepository;
use App\Http\Requests\Company\CustomerCreateRequest;
use App\Http\Requests\Company\CustomerUpdateRequest;

class CustomerController extends Controller
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request)
    {
        $company = Auth::guard('company')->user();
        $filters = $request->filters ?? [];
        $sort = $request->sort ?? 'desc';
        $paginate = toBoolean($request->paginate ?? true);
        $perPage = (int) $request->limit ?? getPaginated();

        $customers = $this->customerRepository->getAllCustomers($company, $filters, $sort, $paginate, $perPage);

        return ApiHelper::successResponse(
            $paginate,
            CompanyCustomerResource::collection($customers),
            'Customers fetched successfully'
        );
    }

    public function show(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'message' => 'Customer fetched successfully',
            'data' => new CompanyCustomerResource($customer),
        ], Response::HTTP_OK);
    }

    public function store(CustomerCreateRequest $request)
    {
        $company = Auth::guard('company')->user();

        if ($company->customers()->where('email', $request->email)->exists()) {
            return $this->sendErrorResponse('Customer already exists', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();
            $customer = $this->customerRepository->createCustomer($company, $request->all());
            if (!$customer) {
                return $this->sendErrorResponse('Customer not created', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => new CompanyCustomerResource($customer),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Customer creation failed: ' . $th->getMessage());
            return $this->sendErrorResponse('Customer not created' . $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        try {
            DB::beginTransaction();
            $company = Auth::guard('company')->user();
            $customer = $this->customerRepository->updateCustomer($company, $request->all(), $customer->id);
            if (!$customer) {
                return $this->sendErrorResponse('Customer not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => new CompanyCustomerResource($customer),
            ], Response::HTTP_OK);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Customer update failed: ' . $th->getMessage());
            return $this->sendErrorResponse('Customer not updated', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Customer $customer)
    {
        $company = Auth::guard('company')->user();
        $this->customerRepository->deleteCustomer($company, $customer->id);

        if (!$customer) {
            return $this->sendErrorResponse('Customer not deleted', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
            'data' => null,
        ], Response::HTTP_OK);
    }
}
