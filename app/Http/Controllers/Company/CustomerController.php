<?php

namespace App\Http\Controllers\Company;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CustomerCreateRequest;
use App\Http\Requests\Company\CustomerUpdateRequest;
use App\Http\Resources\CompanyCustomerResource;
use App\Models\Customer;
use App\Repositories\Company\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
        $company = Auth::guard('company')->user();
        $customer = $this->customerRepository->getCustomerById($company, $customer->id);

        if (! $customer) {
            return $this->sendErrorResponse('Customer not found', Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer fetched successfully',
            'data' => new CompanyCustomerResource($customer),
        ], Response::HTTP_OK);
    }

    public function store(CustomerCreateRequest $request)
    {
        $company = Auth::guard('company')->user();

        try {
            DB::beginTransaction();

            $customer = $this->customerRepository->createCustomer($company, $request->all());

            if (! $customer) {
                return $this->sendErrorResponse('Customer not created', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            DB::commit();

            // Ensure pivot and relations are loaded
            $customer->load('companies', 'emergencyContacts');

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => new CompanyCustomerResource($customer),
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Customer creation failed: ' . $th->getMessage());

            return $this->sendErrorResponse('Customer not created: ' . $th->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(CustomerUpdateRequest $request, Customer $customer)
    {
        try {
            DB::beginTransaction();
            $company = Auth::guard('company')->user();
            $customer = $this->customerRepository->updateCustomer($company, $request->all(), $customer);
            if (! $customer) {
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

        // Prevent deletion if customer has orders for this company
        if ($customer->orders()->where('company_id', $company->id)->exists()) {
            return $this->sendErrorResponse(
                'Cannot delete customer: customer has orders for this company.',
                Response::HTTP_FORBIDDEN
            );
        }

        try {
            $deleted = $this->customerRepository->deleteCustomer($company, $customer);

            if (! $deleted) {
                return $this->sendErrorResponse('Customer not deleted for this company', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully for this company',
                'data' => null,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Customer deletion failed: ' . $th->getMessage());

            return $this->sendErrorResponse(
                'Customer not deleted: ' . $th->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
