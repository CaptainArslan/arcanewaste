<?php

namespace App\Http\Controllers\Api\V1\Company;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CompanyDetailResource;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Company\CompanyUpdateRequest;

class CompanyController extends Controller
{
    public function details(CompanyUpdateRequest $request)
    {
        $company = Auth::guard('company')->user();

        DB::transaction(function () use ($company, $request) {
            $company->update($request->only('name', 'email', 'logo', 'phone', 'description', 'website'));

            if ($request->filled('address')) {
                $address = $request->input('address');
                $address['id'] = $company->defaultAddress->id;
                $company->updateAddress($address);
            }
        });

        $company->load([
            'generalSettings',
            'addresses',
            'defaultAddress',
            'documents',
            'warehouses',
            'paymentOptions',
            'timings',
            'holidays',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Company updated successfully',
            'data'    => CompanyDetailResource::make($company),
        ], Response::HTTP_OK);
    }
}
