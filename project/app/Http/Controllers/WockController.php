<?php

namespace App\Http\Controllers;
use App\Classes\WockAPI;
use Illuminate\Http\JsonResponse;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;

class WockController extends Controller
{
    public function updateProductsQuantities(){

        // Get all worldofcdkeys products
        $products_ids = DB::table('products')->select("wock_product_id")->where('wock_product','=',1)->get();
        $ids=[];
        foreach($products_ids as $id){
            $ids[]=$id->wock_product_id;
        }
        $wock = new WockAPI();
        $response = $wock->getProducts($ids);
        if($response["code"]!="200"){
            return new JsonResponse($response);
        }

        // Update product quantities 
        foreach($response["body"]["products"] as $product){
            $product_to_update =  Product::where('wock_product_id', '=', $product["product_id"])->firstOrFail();
            if($product_to_update and $product_to_update->autoprice == 1){
                $product_to_update->license_qty = $product["quantity"];
                $percentage = 30;
                $numberToAdd = ($product["price"] / 100) * $percentage;
                $price = $product["price"] + $numberToAdd;
                $product_to_update->price = round($price * 4.47,2);
                $product_to_update->wock_product_price = $product["price"];
                $product_to_update->save();
            }elseif($product_to_update){
                $product_to_update->license_qty = $product["quantity"];
                $product_to_update->wock_product_price = $product["price"];
                $product_to_update->save();

            }
        }

        return new JsonResponse("Success");
        
    }
}
