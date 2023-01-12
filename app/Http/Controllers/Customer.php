<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Activities;
use App\Models\Customers;
use App\Models\Sales;

class Customer extends Controller
{
    public function saveCustomer(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'   => 'required',
                'mobile'   => 'numeric',
            ],
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'Form Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        } else {
            $ifExists = Customers::where('name', $request->input('name'))->where('created_by', auth()->user()->id)->first();
            if ($ifExists) :
                return response()->json([
                    'status' => 'error',
                    'msg'    => 'Table Unique Error',
                    'errors' => array('Already Saved!'),
                ], 422);
            else :
                $customers = new Customers;
                $customers->name = $request->input('name');
                $customers->mobile = $request->input('mobile');
                $customers->created_time = current_time();
                $customers->created_date = current_date();
                $customers->created_by = auth()->user()->id;
                $customers->save();

                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Customer Created. ID -' . $customers->id;
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                return $customers;
            endif;
        }
    }

    public function manageCustomer()
    {
        return Customers::where('created_by', auth()->user()->id)->orderByDesc('id')->get();
    }

    public function selectCustomer($id)
    {
        return Customers::find($id);
    }

    public function updateCustomer(Request $data)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'   => 'required',
                'mobile'   => 'numeric',
            ],
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'Form Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        } else {
            $ifExists = Customers::where('name', $request->input('name'))->where('created_by', auth()->user()->id)->first();
            if ($ifExists) :
                return response()->json([
                    'status' => 'error',
                    'msg'    => 'Table Unique Error',
                    'errors' => array('Already Saved!'),
                ], 422);
            else :
                $customers = Customers::find($request->input('id'));
                $customers->name = $request->input('name');
                $customers->mobile = $request->input('mobile');
                $customers->created_time = current_time();
                $customers->created_date = current_date();
                $customers->created_by = auth()->user()->id;
                $customers->save();

                $activities = new Activities;

                $activities->fk_admin_id = auth()->user()->id;
                $activities->type = 'success';
                $activities->name = 'Customer Updated. ID -' . $request->input('id');
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                return $customers;
            endif;
        }
    }

    public function deleteCustomer($id)
    {
        $ifAssocSale = Sales::where('fk_customer_id', $id)->where('created_by', auth()->user()->id)->first();
        if ($ifAssocSale) :
            return response()->json([
                'status' => 'error',
                'msg'    => 'This customer is associated with sale ID: ' . $ifAssocSale->id . ', For deleteing this customer, Delete the sale first.',
            ], 422);
        else :
            $activities = new Activities;

            $activities->fk_admin_id = auth()->user()->id;
            $activities->type = 'success';
            $activities->name = 'Customer Deleted. ID Was-' . $id;
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
