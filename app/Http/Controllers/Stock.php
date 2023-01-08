<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Activities;
use App\Models\Stocks;
use App\Models\StockHistory;

class Stock extends Controller
{
    public function saveStock(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product'   => 'required',
                'buy_price'   => 'required|decimal',
                'sale_price'   => 'required|decimal',
                'quantity'   => 'required|numeric',
            ],
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'Form Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        } else {
            $ifExists = Stocks::where('fk_product_id', $request->input('product'))
                ->where('buy_price', $request->input('buy_price'))
                ->where('sale_price', $request->input('sale_price'))
                ->where('created_by', auth()->user()->id)
                ->first();
            if ($ifExists) :
                return response()->json([
                    'status' => 'error',
                    'msg'    => 'Table Unique Error',
                    'errors' => array('Already Exists! Please Edit The Stock, The Stock ID- '.$ifExists->id),
                ], 422);
            else :
                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Product Created -' . auth()->user()->user_name;
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                $stocks = new Stocks;

                $stocks->fk_product_id = $request->input('product');
                $stocks->barcode = $request->input('barcode');
                $stocks->sku = $request->input('sku');
                $stocks->buy_price = $request->input('buy_price');
                $stocks->sale_price = $request->input('sale_price');
                $stocks->quantity = $request->input('quantity');
                $stocks->created_time = current_time();
                $stocks->created_date = current_date();
                $stocks->created_by = auth()->user()->id;
                $stocks->save();


// CHECK IF EXISTES THE ROW

                $stock_history = new StockHistory;

                $stock_history->total_amount = $request->input('total_amount');
                $stock_history->year = date('Y');


                $stock_history->created_time = current_time();
                $stock_history->created_date = current_date();
                $stock_history->created_by = auth()->user()->id;
                $stock_history->save();

                return $stocks;
            endif;
        }
    }

    public function manageStock()
    {
        return Products::where('created_by', auth()->user()->id)->orderByDesc('id')->get();
    }

    public function searchStock($key)
    {
        return Products::where('name', 'like', "%$key%")->get();
    }

    public function editStock($id)
    {
    }

    public function deleteStock($id)
    {

        // if not associated with STOCK

    }
}
