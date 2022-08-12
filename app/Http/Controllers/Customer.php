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
        $customers->image = $data->file('image')->store('customers');
        $customers->save();
        return $customers;
    }
}
