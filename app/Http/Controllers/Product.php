<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activities;
use App\Models\Products;

class Product extends Controller
{
    public function saveProduct(Request $data)
    {
        $products = new Products;
        $products->name = $data->input('name');
        $products->save();
        return $products;
    }

    public function manageProduct()
    {
        return Products::orderByDesc('id')->get();
    }

    public function searchProduct($key)
    {
        return Products::where('name', 'like', "%$key%")->get();
    }
}
