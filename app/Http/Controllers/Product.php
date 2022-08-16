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
        $products->product_name = $data->input('name');
        $products->save();

        if (!$products->product_id) :
            echo 'not saved';

        else :
            echo 'saved';

        endif;



        // return $products;
    }

    public function manageProduct()
    {
        return Products::orderByDesc('product_id')->get();
    }

    public function searchProduct($key)
    {
        return Products::where('product_name', 'like', "%$key%")->get();
    }
}
