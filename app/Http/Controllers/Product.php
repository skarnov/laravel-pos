<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

class Product extends Controller
{
    public function saveProduct(Request $data)
    {
        $products = new Products;
        $products->product_name = $data->input('name');
        $products->save();
        return $products;
    }

    public function manageProduct()
    {
        return Products::orderByDesc('product_id')->get();
    }
}
