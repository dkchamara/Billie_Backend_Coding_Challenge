<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CompanyController extends Controller
{
    public function getList(){
        die('in');
    }

    public function add(Request $request)
    {
        //Validate data
        $data = $request->only('name', 'address', 'email', 'contact_number', 'is_creditor', 'is_debtor',
            'debtor_limit');

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'address' => 'required|string',
            'email' => 'required|email|unique:companies'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $company = Company::create([
            'name' => $request->name,
            'address' => $request->address,
            'email' => $request->email,
            'contact_number' => $request->contact_number,
            'is_creditor' => $request->is_creditor,
            'is_debtor' => $request->is_debtor,
            'debtor_limit' => $request->debtor_limit
        ]);

        //Company created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Company created successfully',
            'data' => $company
        ], Response::HTTP_OK);
    }
}
