<?php

namespace App\Classes;
use App\Models\Product;
use App\Models\Order;

class WockAPI { // Worldofcdkeys API 

/** API CONFIG */
    protected $apiUrl = "https://apienv.worldofcdkeys.com/";
    protected $headers = ['auth' => ["softfire-api-user", "f;G>-Q}GfMy[=*M#jLuhV6tS7"] ];
    protected $client = null;

    // Set API Base URI
    public function __construct(){
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->apiUrl]);
    }
/** END API CONFIG */

/** FUNCTIONS */
    // List of all products
    public function getProducts($ids=""){
        $params = "";
        if($ids)
            $params = "?product_id=".implode(",",$ids);
        
        try{
            $res = $this->client->request('GET', 'products'.$params, $this->headers);
        }
        catch(\Exception $e){
            $res = null;
        }

        if($res)
            return $this->bodyFormat($res->getStatusCode(),$res->getBody());
        return $this->bodyFormat("404",'{"message":"Server error..."}');
    }

    public function buyProduct($id=1, $qty=1, $price=12){
        $body=[];
        $body["products"] = [[
            "product_id"=> $id,
			"quantity"=> $qty,
			"price"=> $price,
			"delayed"=> true
        ]];
        $body["only_type"] = "auto";
        $body["bulk_prices"] = false;
        try{
            $res = $this->client->post('order', [
                'auth' => $this->headers["auth"],
                'content-type' => 'application/json', 
                'body' => json_encode($body)
            ]);
        }
        catch(\Exception $e){
            dd($e);
            $res = null;
        }

        if($res)
            return $this->bodyFormat($res->getStatusCode(),$res->getBody());
        return $this->bodyFormat("404",'{"message":"Server error..."}');
    }

    public function getRequest($request_id=""){
        $params = "?client_token=".$request_id;

        try{
            $res = $this->client->request('GET', 'downloadPlain'.$params, $this->headers);
        }
        catch(\Exception $e){
            $res = null;
        }

        if($res)
            return $this->bodyFormat($res->getStatusCode(),$res->getBody());
        return $this->bodyFormat("404",'{"message":"Server error..."}');
    }

    public function getProductsFromCart($order_id){
        $order = Order::findOrFail($order_id);
        if(!$order)
            return "error";
        $cart = unserialize(bzdecompress(utf8_decode($order->cart)));
        $wock_request_id = [];
        $wock_serial_txt = [];
        $wock_serials = [];
        foreach($cart->items as $k=>$item){
            $pct = Product::where(['id' => $k,'wock_product' => '1'])->first();
            if($pct){
                //if($item["price"]>$pct->wock_product_price){
                    $response_bp = $this->buyProduct($pct->wock_product_id, $item["qty"], $pct->wock_product_price);
                    if($response_bp["code"]=="200"){
                        $wock_request_id[] = $response_bp["body"]["request_id"];
                        $response_gr = $this->getRequest($response_bp["body"]["request_id"]);
                        if($response_gr["code"]=="200"){
                            foreach($response_gr["body"]["products"][0]["serials"] as $k => $srs){
                                $serials = [];
                                $serial_txt=[];
                                foreach($srs as $serial){
                                    $serials[] = $serial["code"];
                                    if($serial["mimetype"] == "text/plain")
                                        $serial_txt[] = 1;
                                    else
                                        $serial_txt[] = 0;
                                }
                                $wock_serial_txt[] = $serial_txt;
                                $wock_serials[] = $serials;
                            }
                        }
                    }
                //}
            }
        }
                        
        if($wock_request_id)
            $order->wock_request_id = json_encode($wock_request_id);
        if($wock_serial_txt)
            $order->wock_serial_txt = json_encode($wock_serial_txt);
        if($wock_serials)
            $order->wock_serials = json_encode($wock_serials);
        $order->update();
            
        return "done";
    }
/** END FUNCTIONS */


/** HELPERS */
    // Format Body output
    private function bodyFormat($code, $body){
        return ["code" => $code, "body" => json_decode((string) $body, true)];
    }
/** END HELPERS */
}