<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'company_email' => 'required|email|unique:companies',
            'admin_id' => 'required|exists:users,id',
            'address' => 'required|string',
            'phone' => 'nullable|string'
        ]);

        $company = new Company;

        $company->name = $request->input('name');
        $company->company_email = $request->input('company_email');
        $company->address = $request->input('address');
        $company->phone = $request->input('phone') ? $request->input('phone') : null;

        $company->admin()->associate($request->input('admin_id'));

        $company->save();

        return response()->json([
            'company' => $company,
            'admin' => $company->admin()->first(),
            'employees' => [],
        ], 201);
    }
    public function getCompanies()
    {
        $companies = Company::get();
        $collection = collect($companies)->map(function ($company) {
            return [
                'company' => $company,
                'admin' => $company->admin()->first(),
                'employees' => $company->employees()->get()
            ];
        });
        return response()->json([
            'companies' => $collection,
        ], 200);
    }

    public function show(Company $company)
    {
        $admin = $company->admin()->first();
        $employees = $company->employees()->get();

        return response()->json([
            'company' => $company,
            'admin' => $admin,
            'employees' => $employees
        ], 200);
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'nullable|string',
            'company_email' => 'nullable|email',
            'address' => 'nullable|string',
            'phone' => 'nullable|string'
        ]);

        if ($request->input('name') && $company->name != $request->input('name')) {
            $company->name = $request->input('name');
        }
        if ($request->input('company_email') && $company->company_email != $request->input('company_email')) {
            $request->validate([
                'company_email' => 'unique:companies,company_email'
            ]);
            $company->company_email = $request->input('company_email');
        }
        if ($request->input('address') && $company->address != $request->input('address')) {
            $company->address = $request->input('address');
        }
        if ($request->input('phone') && $company->phone != $request->input('phone')) {
            $company->phone = $request->input('phone');
        }

        $company->save();

        return response()->json([
            'company' => $company,
        ], 200);
    }

    public function search(Request $request)
    {
        $request->validate([
            'searched_value' => 'required|string'
        ]);

        $companies = Company::where('name', 'like', '%' . $request->input('searched_value') . '%')->orderBy('name')->get();

        $employees = User::where('is_admin', false)->where('name', 'like', '%' . $request->input('searched_value') . '%')->orderBy('name')->get();
        return response()->json([
            'results' => [
                'companies' => $companies,
                'employees' => $employees,
            ]
        ], 200);
    }

    public function destroy(Company $company)
    {
        $employees = $company->employees()->get();
        if (count($employees) > 0) {
            return response()->json([
                'message' => 'You can\'t remove this company !',
            ], 401);
        }
        $company->delete();

        return response()->json([
            'message' => 'Company removed successfully!',
        ], 200);
    }
}
