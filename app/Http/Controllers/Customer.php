<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;


class Customer extends Controller
{

    public function saveCustomer(Request $data)
    {
        $customers = new Customers;
        $customers->name = $data->input('name');
        $customers->image = $data->file('file')->store('customers');
        $customers->save();
        return $customers;
    }

    public function manageCustomer()
    {
        return Customers::orderByDesc('id')->get();
    }

    public function selectCustomer($customer_id)
    {
        return Customers::find($customer_id);
    }

    public function updateCustomer(Request $data)
    {
        $customers = new Customers;
        $customers->name = $data->input('name');
        $customers->image = $data->file('file')->store('customers');
        $customers->save();
        return $customers;
    }

    public function deleteCustomer($customer_id)
    {
        $result = Customers::where('id', $customer_id)->delete();
        if ($result) {
            return ['result' => 'Customer has been deleted'];
        } else {
            return ['result' => 'Failed'];
        }
    }
}
