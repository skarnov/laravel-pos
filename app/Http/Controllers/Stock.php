<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Activities;
use App\Models\Stocks;
use App\Models\StockHistory;
use App\Models\SaleDetails;

class Stock extends Controller
{
    public function saveStock(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'product'   => 'required',
                'buy_price'   => 'required|numeric',
                'sale_price'   => 'required|numeric',
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
                    'errors' => array('Already Exists! Please Edit The Stock, The Stock ID- ' . $ifExists->id),
                ], 422);
            else :
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

                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Stock Created. ID -' .  $stocks->id;
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                $ifExists = StockHistory::where('year', date('Y'))->where('created_by', auth()->user()->id)->first();
                if ($ifExists) :
                    $StockHistory = StockHistory::find($ifExists->id);

                    $stockIn = $request->input('buy_price') * $request->input('quantity');

                    $StockHistory->total_amount += $stockIn;

                    if (date('m') == 1) :
                        $StockHistory->january += $stockIn;
                    elseif (date('m') == 2) :
                        $StockHistory->february += $stockIn;
                    elseif (date('m') == 3) :
                        $StockHistory->march += $stockIn;
                    elseif (date('m') == 4) :
                        $StockHistory->april += $stockIn;
                    elseif (date('m') == 5) :
                        $StockHistory->may += $stockIn;
                    elseif (date('m') == 6) :
                        $StockHistory->june += $stockIn;
                    elseif (date('m') == 7) :
                        $StockHistory->july += $stockIn;
                    elseif (date('m') == 8) :
                        $StockHistory->august += $stockIn;
                    elseif (date('m') == 9) :
                        $StockHistory->september += $stockIn;
                    elseif (date('m') == 10) :
                        $StockHistory->october += $stockIn;
                    elseif (date('m') == 11) :
                        $StockHistory->november += $stockIn;
                    elseif (date('m') == 12) :
                        $StockHistory->december += $stockIn;
                    endif;

                    $StockHistory->modified_time = current_time();
                    $StockHistory->modified_date = current_date();
                    $StockHistory->modified_by = auth()->user()->id;

                    return $StockHistory->save();
                else :
                    $StockHistory = new StockHistory;

                    $stockIn = $request->input('buy_price') * $request->input('quantity');

                    $StockHistory->total_amount = $stockIn;
                    $StockHistory->year = date('Y');

                    if (date('m') == 1) :
                        $StockHistory->january = $stockIn;
                    elseif (date('m') == 2) :
                        $StockHistory->february = $stockIn;
                    elseif (date('m') == 3) :
                        $StockHistory->march = $stockIn;
                    elseif (date('m') == 4) :
                        $StockHistory->april = $stockIn;
                    elseif (date('m') == 5) :
                        $StockHistory->may = $stockIn;
                    elseif (date('m') == 6) :
                        $StockHistory->june = $stockIn;
                    elseif (date('m') == 7) :
                        $StockHistory->july = $stockIn;
                    elseif (date('m') == 8) :
                        $StockHistory->august = $stockIn;
                    elseif (date('m') == 9) :
                        $StockHistory->september = $stockIn;
                    elseif (date('m') == 10) :
                        $StockHistory->october = $stockIn;
                    elseif (date('m') == 11) :
                        $StockHistory->november = $stockIn;
                    elseif (date('m') == 12) :
                        $StockHistory->december = $stockIn;
                    endif;

                    $StockHistory->created_time = current_time();
                    $StockHistory->created_date = current_date();
                    $StockHistory->created_by = auth()->user()->id;

                    return $StockHistory->save();
                endif;
            endif;
        }
    }

    public function manageStock()
    {
        $allStocks = Stocks::leftJoin('products', 'stocks.fk_product_id', '=', 'products.id')
            ->where('stocks.created_by', auth()->user()->id)
            ->orderByDesc('stocks.id')
            ->get();
        return $allStocks;
    }

    public function selectStock($id)
    {
        return Stocks::where('created_by', auth()->user()->id)->find($id);
    }

    public function updateStock(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
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
            $stock_info = Stocks::where('id', $id)->first();
            $previous_quantity = $stock_info->quantity;
            $buy_price = $stock_info->buy_price;

            $stocks =  Stocks::find($request->input('id'));

            $stocks->barcode = $request->input('barcode');
            $stocks->sku = $request->input('sku');
            $stocks->quantity = $request->input('quantity');
            $stocks->modified_time = current_time();
            $stocks->modified_date = current_date();
            $stocks->modified_by = auth()->user()->id;
            $stocks->save();

            $activities = new Activities;

            $activities->fk_admin_id = auth()->user()->id;
            $activities->type = 'success';
            $activities->name = 'Stock Updated. ID -' .  $request->input('id');
            $activities->ip_address = user_ip();
            $activities->visitor_country =  ip_info('Visitor', 'Country');
            $activities->visitor_state = ip_info('Visitor', 'State');
            $activities->visitor_city = ip_info('Visitor', 'City');
            $activities->visitor_address = ip_info('Visitor', 'Address');
            $activities->created_time = current_time();
            $activities->created_date = current_date();
            $activities->created_by = auth()->user()->id;
            $activities->save();

            $quantity_increase = $request->input('quantity') - $previous_quantity;

            if ($quantity_increase) :
                $StockHistory = StockHistory::find($ifExists->id);
                $stockIn = $buy_price * $quantity_increase;

                $StockHistory->total_amount += $stockIn;

                if (date('m') == 1) :
                    $StockHistory->january += $stockIn;
                elseif (date('m') == 2) :
                    $StockHistory->february += $stockIn;
                elseif (date('m') == 3) :
                    $StockHistory->march += $stockIn;
                elseif (date('m') == 4) :
                    $StockHistory->april += $stockIn;
                elseif (date('m') == 5) :
                    $StockHistory->may += $stockIn;
                elseif (date('m') == 6) :
                    $StockHistory->june += $stockIn;
                elseif (date('m') == 7) :
                    $StockHistory->july += $stockIn;
                elseif (date('m') == 8) :
                    $StockHistory->august += $stockIn;
                elseif (date('m') == 9) :
                    $StockHistory->september += $stockIn;
                elseif (date('m') == 10) :
                    $StockHistory->october += $stockIn;
                elseif (date('m') == 11) :
                    $StockHistory->november += $stockIn;
                elseif (date('m') == 12) :
                    $StockHistory->december += $stockIn;
                endif;

                $StockHistory->modified_time = current_time();
                $StockHistory->modified_date = current_date();
                $StockHistory->modified_by = auth()->user()->id;

                return $StockHistory->save();
            else :
                return $stocks;
            endif;
        }
    }

    public function deleteStock($id)
    {
        $ifAssocSale = SaleDetails::where('fk_stock_id', $id)->where('created_by', auth()->user()->id)->first();
        if ($ifAssocSale) :
            return response()->json([
                'status' => 'error',
                'msg'    => 'This stock is associated with sale ID: ' . $ifAssocSale->fk_sale_id . ', For deleteing this stock, Delete the sale first.',
            ], 422);
        else :
            $activities = new Activities;

            $activities->fk_admin_id = auth()->user()->id;
            $activities->type = 'success';
            $activities->name = 'Stock Deleted. ID Was-' . $id;
            $activities->ip_address = user_ip();
            $activities->visitor_country =  ip_info('Visitor', 'Country');
            $activities->visitor_state = ip_info('Visitor', 'State');
            $activities->visitor_city = ip_info('Visitor', 'City');
            $activities->visitor_address = ip_info('Visitor', 'Address');
            $activities->created_time = current_time();
            $activities->created_date = current_date();
            $activities->created_by = auth()->user()->id;
            $activities->save();

            Stocks::where('id', $id)->delete();
            return response()->json([
                'msg'    => 'Deleted',
            ], 200);
        endif;
    }
}
