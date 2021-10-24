<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        //Validate data
        $data = $request->only('creditor_company_id', 'debtor_company_id', 'invoice_date', 'items');

        $validator = Validator::make($data, [
            'creditor_company_id' => 'required|string',
            'debtor_company_id' => 'required|string',
            'invoice_date' => 'required|date_format:d/m/Y',
            'items' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        // Validate debtor credit limit
        $debtorCompany = Company::where( 'id', $request->debtor_company_id)->first();

        $invoiceTotal = Invoice::where([
                'debtor_company_id' => $debtorCompany['id'],
                'status' => 'PENDING'
            ])
            ->sum('invoice_amount');

        $newInvoiceTotal = array_sum(array_column($request->items, 'amount'));
        $totalValueWithPending = $newInvoiceTotal + $invoiceTotal;

        if($totalValueWithPending >= $debtorCompany['debtor_limit']) {
            return response()->json([
                'success' => false,
                'message' => 'Debtor limit cannot be exceed'
            ], Response::HTTP_OK);
        }

        $invoiceDate = Carbon::createFromFormat('d/m/Y', $request->invoice_date)->format('Y-m-d');

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'creditor_company_id' => $request->creditor_company_id,
                'debtor_company_id' => $request->debtor_company_id,
                'invoice_date' => $invoiceDate
            ]);

            if ($invoice->id) {
                $total_amount = 0;
                foreach ($request->items as $item) {
                    $invoiceItems = InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $item['description'],
                        'qty' => $item['qty'],
                        'amount' => $item['amount']
                    ]);

                    $total_amount += $item['amount'];
                }

                $invoice->invoice_amount = $total_amount;
                $invoice->save();
            }

            DB::commit();

            //Invoice created, return success response
            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Invoice creation error, please try again'
            ], Response::HTTP_OK);
        }
    }

    public function completeInvoice(Request $request)
    {
        //Validate data
        $data = $request->only('invoice_id', 'complete');

        $validator = Validator::make($data, [
            'invoice_id' => 'required|integer',
            'complete' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $invoice = Invoice::where('id', $request->invoice_id)->first();

        if( !empty($invoice) ){
            $invoice->status = 'COMPLETED';
            $invoice->save();

            return response()->json([
                'success' => true,
                'message' => 'Invoice completed successfully'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], Response::HTTP_OK);
        }


    }
}
