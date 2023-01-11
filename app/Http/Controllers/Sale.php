<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Activities;
use App\Models\Sales;
use App\Models\SaleDetails;
use App\Models\SaleHistory;
use App\Models\IncomeHistory;

class Sale extends Controller
{
    public function saveSale(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'   => 'required',
            ],
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'Form Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        } else {
            $ifExists = Products::where('name', $request->input('name'))->where('created_by', auth()->user()->id)->first();
            if ($ifExists) :
                return response()->json([
                    'status' => 'error',
                    'msg'    => 'Table Unique Error',
                    'errors' => array('Already Saved!'),
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

                $products = new Products;
                $products->name = $request->input('name');
                $products->created_time = current_time();
                $products->created_date = current_date();
                $products->created_by = auth()->user()->id;
                $products->save();

                return $products;
            endif;
        }
    }

    public function manageSale()
    {
        return Sales::where('created_by', auth()->user()->id)->orderByDesc('id')->get();
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
