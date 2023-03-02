<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activities;
use App\Models\Products;
use App\Models\Sales;
use App\Models\Customers;
use App\Models\Stocks;
use App\Models\Carts;
use App\Models\SaleDetails;
use App\Models\SaleHistory;
use App\Models\IncomeHistory;

class Sale extends Controller
{
    public function cart()
    {
        return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
            ->where('created_by', auth()->user()->id)
            ->get();
    }

    public function selectCart($id)
    {
        $selectStock = Stocks::select('stocks.id', 'stocks.sale_price', 'products.name')
            ->leftJoin('products', 'stocks.fk_product_id', '=', 'products.id')
            ->where('stocks.created_by', auth()->user()->id)
            ->find($id);

        if ($selectStock) :
            $ifExists = Carts::select('id')
                ->where('fk_stock_id', $selectStock->id)
                ->where('created_by', auth()->user()->id)
                ->first();

            if ($ifExists) :
                $carts = Carts::find($ifExists->id);
                $carts->quantity +=  1;
                $carts->save();

                return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
                    ->where('created_by', auth()->user()->id)
                    ->get();
            else :
                $carts = new Carts;
                $carts->fk_stock_id = $selectStock->id;
                $carts->name = $selectStock->name;
                $carts->quantity = 1;
                $carts->unit =  $selectStock->sale_price;
                $carts->created_by = auth()->user()->id;
                $carts->save();

                return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
                    ->where('created_by', auth()->user()->id)
                    ->get();
            endif;
        else :
            return false;
        endif;
    }

    public function findStock($query)
    {
        $selectStock = Stocks::select('stocks.id', 'stocks.sale_price', 'products.name')
            ->leftJoin('products', 'stocks.fk_product_id', '=', 'products.id')
            ->where('barcode', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->where('stocks.created_by', auth()->user()->id)
            ->first();

        if ($selectStock) :
            $ifExists = Carts::select('id')
                ->where('fk_stock_id', $selectStock->id)
                ->where('created_by', auth()->user()->id)
                ->first();

            if ($ifExists) :
                $carts = Carts::find($ifExists->id);
                $carts->quantity +=  1;
                $carts->save();

                return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
                    ->where('created_by', auth()->user()->id)
                    ->get();
            else :
                $carts = new Carts;
                $carts->fk_stock_id = $selectStock->id;
                $carts->name = $selectStock->name;
                $carts->quantity = 1;
                $carts->unit =  $selectStock->sale_price;
                $carts->created_by = auth()->user()->id;
                $carts->save();

                return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
                    ->where('created_by', auth()->user()->id)
                    ->get();
            endif;
        else :
            return response()->json([
                'status' => 'error',
                'msg'    => 'Stock Not Found!',
            ], 422);
        endif;
    }

    public function updateCartIncrease($id)
    {
        $ifExists = Carts::select('id')
            ->where('fk_stock_id', $id)
            ->where('created_by', auth()->user()->id)
            ->first();

        if ($ifExists) :
            $carts = Carts::find($ifExists->id);
            $carts->quantity +=  1;
            $carts->save();

            return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
                ->where('created_by', auth()->user()->id)
                ->get();
        endif;
    }

    public function updateCartDecrease($id)
    {
        $ifEmpty = Carts::select('quantity')
            ->where('fk_stock_id', $id)
            ->where('created_by', auth()->user()->id)
            ->first();

        if ($ifEmpty->quantity < 2) :
            Carts::where('fk_stock_id', $id)->delete();
        endif;

        $ifExists = Carts::select('id')
            ->where('fk_stock_id', $id)
            ->where('created_by', auth()->user()->id)
            ->first();

        if ($ifExists) :
            $carts = Carts::find($ifExists->id);
            $carts->quantity -=  1;
            $carts->save();

            return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
                ->where('created_by', auth()->user()->id)
                ->get();
        endif;
    }

    public function deleteCart($id)
    {
        Carts::where('fk_stock_id', $id)->where('created_by', auth()->user()->id)->delete();
        return Carts::select('fk_stock_id', 'name', 'quantity', 'unit')
            ->where('created_by', auth()->user()->id)
            ->get();
    }

    public function saveSale(Request $request)
    {
        $cartItems = Carts::select('stocks.fk_product_id', 'stocks.buy_price', 'carts.fk_stock_id', 'carts.name', 'carts.quantity', 'carts.unit')
            ->leftJoin('stocks', 'stocks.fk_product_id', '=', 'carts.fk_stock_id')
            ->where('carts.created_by', auth()->user()->id)
            ->get();

        $buyPriceTotal = 0;
        $saleQuantity = 0;
        $cartTotal = 0;

        foreach ($cartItems as $item) :
            $buyPriceTotal += $item->quantity * $item->buy_price;
            $saleQuantity += $item->quantity;
            $cartTotal += $item->quantity * $item->unit;

            $stocks = Stocks::find($item->fk_stock_id);
            $stocks->quantity -= $item->quantity;
            $stocks->save();

            $products = Products::find($item->fk_product_id);
            $products->sale_count += $item->quantity;
            $products->save();
        endforeach;

        $discount = $request->input('discount');
        $customer = $request->input('customer');
        $paid = $request->input('paid');

        $grand_total = $cartTotal - $discount;

        if (!$customer) :
            $paid = $grand_total;
        endif;

        $sale_due = $grand_total - $paid;

        if ($sale_due) :
            $customers = Customers::find($customer);
            $customers->sale_due += $sale_due;
            $customers->total_buy += $grand_total;
            $customers->save();
        endif;

        $income_amount = $grand_total - $buyPriceTotal;
        $netIncomeAfterDue = $paid - $buyPriceTotal;

        $sales = new Sales;

        $sales->fk_customer_id = $customer;
        $sales->income_amount = $income_amount;
        $sales->net_income = $netIncomeAfterDue;
        $sales->total = $cartTotal;
        $sales->discount = $discount;
        $sales->grand_total = $grand_total;
        $sales->paid_amount = $paid;
        $sales->sale_quantity = $saleQuantity;
        $sales->sale_due = $sale_due;
        $sales->created_time = current_time();
        $sales->created_date = current_date();
        $sales->created_by = auth()->user()->id;
        $sales->save();

        $activities = new Activities;

        $activities->fk_admin_id = auth()->user()->id;
        $activities->type = 'success';
        $activities->name = 'Sale Created. ID -' .  $sales->id;
        $activities->ip_address = user_ip();
        $activities->visitor_country =  ip_info('Visitor', 'Country');
        $activities->visitor_state = ip_info('Visitor', 'State');
        $activities->visitor_city = ip_info('Visitor', 'City');
        $activities->visitor_address = ip_info('Visitor', 'Address');
        $activities->created_time = current_time();
        $activities->created_date = current_date();
        $activities->created_by = auth()->user()->id;
        $activities->save();

        foreach ($cartItems as $item) :
            $saleDetails = new SaleDetails;
            $saleDetails->fk_sale_id = $sales->id;
            $saleDetails->fk_customer_id = $customer;
            $saleDetails->fk_stock_id = $item->fk_stock_id;
            $saleDetails->name = $item->name;
            $saleDetails->quantity = $item->quantity;
            $saleDetails->unit_price = $item->unit;
            $saleDetails->subtotal =  $item->quantity * $item->unit;
            $saleDetails->save();
        endforeach;

        Carts::where('created_by', auth()->user()->id)->delete();

        $ifExists = SaleHistory::where('year', date('Y'))->where('created_by', auth()->user()->id)->first();
        if ($ifExists) :
            $SaleHistory = SaleHistory::find($ifExists->id);

            $SaleHistory->total_amount += $grand_total;

            if (date('m') == 1) :
                $SaleHistory->january += $grand_total;
            elseif (date('m') == 2) :
                $SaleHistory->february += $grand_total;
            elseif (date('m') == 3) :
                $SaleHistory->march += $grand_total;
            elseif (date('m') == 4) :
                $SaleHistory->april += $grand_total;
            elseif (date('m') == 5) :
                $SaleHistory->may += $grand_total;
            elseif (date('m') == 6) :
                $SaleHistory->june += $grand_total;
            elseif (date('m') == 7) :
                $SaleHistory->july += $grand_total;
            elseif (date('m') == 8) :
                $SaleHistory->august += $grand_total;
            elseif (date('m') == 9) :
                $SaleHistory->september += $grand_total;
            elseif (date('m') == 10) :
                $SaleHistory->october += $grand_total;
            elseif (date('m') == 11) :
                $SaleHistory->november += $grand_total;
            elseif (date('m') == 12) :
                $SaleHistory->december += $grand_total;
            endif;

            $SaleHistory->modified_time = current_time();
            $SaleHistory->modified_date = current_date();
            $SaleHistory->modified_by = auth()->user()->id;

            $SaleHistory->save();
        else :
            $SaleHistory = new StockHistory;

            $SaleHistory->total_amount = $grand_total;;
            $SaleHistory->year = date('Y');

            if (date('m') == 1) :
                $SaleHistory->january = $grand_total;
            elseif (date('m') == 2) :
                $SaleHistory->february = $grand_total;
            elseif (date('m') == 3) :
                $SaleHistory->march = $grand_total;
            elseif (date('m') == 4) :
                $SaleHistory->april = $grand_total;
            elseif (date('m') == 5) :
                $SaleHistory->may = $grand_total;
            elseif (date('m') == 6) :
                $SaleHistory->june = $grand_total;
            elseif (date('m') == 7) :
                $SaleHistory->july = $grand_total;
            elseif (date('m') == 8) :
                $SaleHistory->august = $grand_total;
            elseif (date('m') == 9) :
                $SaleHistory->september = $grand_total;
            elseif (date('m') == 10) :
                $SaleHistory->october = $grand_total;
            elseif (date('m') == 11) :
                $SaleHistory->november = $grand_total;
            elseif (date('m') == 12) :
                $SaleHistory->december = $grand_total;
            endif;

            $SaleHistory->created_time = current_time();
            $SaleHistory->created_date = current_date();
            $SaleHistory->created_by = auth()->user()->id;

            $SaleHistory->save();
        endif;

        $ifExists = IncomeHistory::where('year', date('Y'))->where('created_by', auth()->user()->id)->first();
        if ($ifExists) :
            $IncomeHistory = IncomeHistory::find($ifExists->id);

            $IncomeHistory->total_amount += $netIncomeAfterDue;

            if (date('m') == 1) :
                $IncomeHistory->january += $netIncomeAfterDue;
            elseif (date('m') == 2) :
                $IncomeHistory->february += $netIncomeAfterDue;
            elseif (date('m') == 3) :
                $IncomeHistory->march += $netIncomeAfterDue;
            elseif (date('m') == 4) :
                $IncomeHistory->april += $netIncomeAfterDue;
            elseif (date('m') == 5) :
                $IncomeHistory->may += $netIncomeAfterDue;
            elseif (date('m') == 6) :
                $IncomeHistory->june += $netIncomeAfterDue;
            elseif (date('m') == 7) :
                $IncomeHistory->july += $netIncomeAfterDue;
            elseif (date('m') == 8) :
                $IncomeHistory->august += $netIncomeAfterDue;
            elseif (date('m') == 9) :
                $IncomeHistory->september += $netIncomeAfterDue;
            elseif (date('m') == 10) :
                $IncomeHistory->october += $netIncomeAfterDue;
            elseif (date('m') == 11) :
                $IncomeHistory->november += $netIncomeAfterDue;
            elseif (date('m') == 12) :
                $IncomeHistory->december += $netIncomeAfterDue;
            endif;

            $IncomeHistory->modified_time = current_time();
            $IncomeHistory->modified_date = current_date();
            $IncomeHistory->modified_by = auth()->user()->id;

            $IncomeHistory->save();
        else :
            $IncomeHistory = new IncomeHistory;

            $IncomeHistory->total_amount = $netIncomeAfterDue;;
            $IncomeHistory->year = date('Y');

            if (date('m') == 1) :
                $IncomeHistory->january = $netIncomeAfterDue;
            elseif (date('m') == 2) :
                $IncomeHistory->february = $netIncomeAfterDue;
            elseif (date('m') == 3) :
                $IncomeHistory->march = $netIncomeAfterDue;
            elseif (date('m') == 4) :
                $IncomeHistory->april = $netIncomeAfterDue;
            elseif (date('m') == 5) :
                $IncomeHistory->may = $netIncomeAfterDue;
            elseif (date('m') == 6) :
                $IncomeHistory->june = $netIncomeAfterDue;
            elseif (date('m') == 7) :
                $IncomeHistory->july = $netIncomeAfterDue;
            elseif (date('m') == 8) :
                $IncomeHistory->august = $netIncomeAfterDue;
            elseif (date('m') == 9) :
                $IncomeHistory->september = $netIncomeAfterDue;
            elseif (date('m') == 10) :
                $IncomeHistory->october = $netIncomeAfterDue;
            elseif (date('m') == 11) :
                $IncomeHistory->november = $netIncomeAfterDue;
            elseif (date('m') == 12) :
                $IncomeHistory->december = $netIncomeAfterDue;
            endif;

            $IncomeHistory->created_time = current_time();
            $IncomeHistory->created_date = current_date();
            $IncomeHistory->created_by = auth()->user()->id;

            $IncomeHistory->save();
        endif;
    }

    public function manageSale()
    {
        return Sales::where('created_by', auth()->user()->id)->orderByDesc('id')->get();
    }

    public function lastSale()
    {
        $sale_info = Sales::where('created_by', auth()->user()->id)->orderByDesc('id')->limit(1)->first();

        if ($sale_info->fk_customer_id) :
            $customer_info = Customers::where('id', $sale_info->fk_customer_id)->where('created_by', auth()->user()->id)->first();
        endif;

        $sale_details = SaleDetails::where('fk_sale_id', $sale_info->id)->get();
        return response()->json([
            'saleInfo'    => $sale_info,
            'saleDetails'    => $sale_details,
            'customerInfo'    => isset($customer_info) ? $customer_info : '',
        ], 200);
    }

    public function selectSale($id)
    {
        $sale_info = Sales::where('id', $id)->where('created_by', auth()->user()->id)->orderByDesc('id')->limit(1)->first();
        $sale_details = SaleDetails::where('fk_sale_id', $id)->get();
        return response()->json([
            'saleInfo'    => $sale_info,
            'saleDetails'    => $sale_details,
        ], 200);
    }



    public function searchSale($key)
    {
        return Products::where('name', 'like', "%$key%")->get();
    }

    public function editSale($id)
    {
    }

    public function updateSale(Request $data)
    {
        // $customers = new Customers;
        // $customers->name = $data->input('name');
        // $customers->image = $data->file('file')->store('customers');
        // $customers->save();
        // return $customers;
    }

    public function deleteSale($id)
    {

        // if not associated with STOCK

    }
}
