<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;

class Customer extends Controller
{
    public function saveCustomer(Request $request)
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
            $ifExists = Customers::where('name', $request->input('name'))->where('created_by', auth()->user()->id)->first();
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
                $activities->name = 'Customer Created -' . auth()->user()->user_name;
                $activities->ip_address = user_ip();
                $activities->visitor_country =  ip_info('Visitor', 'Country');
                $activities->visitor_state = ip_info('Visitor', 'State');
                $activities->visitor_city = ip_info('Visitor', 'City');
                $activities->visitor_address = ip_info('Visitor', 'Address');
                $activities->created_time = current_time();
                $activities->created_date = current_date();
                $activities->created_by = auth()->user()->id;
                $activities->save();

                $customers = new Customers;
                $customers->name = $request->input('name');
                $customers->mobile = $request->input('mobile');
                $customers->created_time = current_time();
                $customers->created_date = current_date();
                $customers->created_by = auth()->user()->id;
                $customers->save();

                return $customers;
            endif;
        }
    }

    public function manageCustomer()
    {
        // return Customers::orderByDesc('id')->get();
    }

    public function selectCustomer($id)
    {
        // return Customers::find($id);
    }

    public function updateCustomer(Request $data)
    {
        // $customers = new Customers;
        // $customers->name = $data->input('name');
        // $customers->image = $data->file('file')->store('customers');
        // $customers->save();
        // return $customers;
    }

    public function deleteCustomer($id)
    {
        // $result = Customers::where('id', $id)->delete();
        // if ($result) {
        //     return ['result' => 'Customer has been deleted'];
        // } else {
        //     return ['result' => 'Failed'];
        // }
    }
}
