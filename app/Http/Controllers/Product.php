<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Activities;
use App\Models\Products;
use App\Models\Stocks;

class Product extends Controller
{
    public function saveProduct(Request $request)
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
                $products = new Products;
                $products->name = $request->input('name');
                $products->created_time = current_time();
                $products->created_date = current_date();
                $products->created_by = auth()->user()->id;
                $products->save();

                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Product Created. ID-' . $products->id;
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                return $products;
            endif;
        }
    }

    public function manageProduct()
    {
        return Products::where('created_by', auth()->user()->id)->orderByDesc('id')->get();
    }

    public function selectProduct($id)
    {
        return Products::where('created_by', auth()->user()->id)->find($id);
    }

    public function updateProduct(Request $request)
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
                $products = Products::find($request->input('id'));
                $products->name = $request->input('name');
                $products->modified_time = current_time();
                $products->modified_date = current_date();
                $products->modified_by = auth()->user()->id;
                $products->save();

                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Product Updated. ID-' . $request->input('id');
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                return $products;
            endif;
        }
    }

    public function deleteProduct($id)
    {
        $ifAssocStock = Stocks::where('fk_product_id', $id)->where('created_by', auth()->user()->id)->first();
        if ($ifAssocStock) :
            return response()->json([
                'status' => 'error',
                'msg'    => 'This product is associated with stock ID: ' . $ifAssocStock->id . ', For deleteing this product, Delete the stock first.',
            ], 422);
        else :
            $activities = new Activities;

            $activities->fk_admin_id = auth()->user()->id;
            $activities->type = 'success';
            $activities->name = 'Product Deleted. ID Was-' . $id;
            $activities->ip_address = user_ip();
            $activities->visitor_country =  ip_info('Visitor', 'Country');
            $activities->visitor_state = ip_info('Visitor', 'State');
            $activities->visitor_city = ip_info('Visitor', 'City');
            $activities->visitor_address = ip_info('Visitor', 'Address');
            $activities->created_time = current_time();
            $activities->created_date = current_date();
            $activities->created_by = auth()->user()->id;
            $activities->save();

            Products::where('id', $id)->delete();
            return response()->json([
                'msg'    => 'Deleted',
            ], 200);
        endif;
    }
}
