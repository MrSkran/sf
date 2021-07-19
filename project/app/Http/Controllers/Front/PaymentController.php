<?php

namespace App\Http\Controllers\Front;
use App\Models\Generalsetting;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use App\Classes\GeniusMailer;
use App\Models\Order;
use App\Models\OrderTrack;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Currency;
use App\Models\Notification;
use App\Models\Product;
use App\Models\Pagesetting;
use App\Models\User;
use App\Models\VendorOrder;
use App\Models\UserNotification;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Classes\WockAPI;

class PaymentController extends Controller
{
    private $_api_context;
    public $curr;

    public function __construct()
    {
        if (Session::has('currency')) 
        {
            $this->curr = Currency::find(Session::get('currency'));
        }
        else
        {
            $this->curr = Currency::where('is_default','=',1)->first();
        }
        $paypal_conf = \Config::get('paypal');
        $paypal_conf['client_id'] = "AZ4m727WU4333sLRp2c5VkJyUcL9ew2fIHBLSUb4btZ8S5wpxcvJvyyISOXZn5FYMzWcYrB8gBqVWYND";
        $paypal_conf['secret'] = "EGGp9rUDv0Cr4OYerSH2zpzI1xqcK1eloyZFJpVTKQJfvMv-LVSMASG73luMSdDX_cg-JVG0vnHaSwgR";
        $paypal_conf['settings']['mode'] = 'live';
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function store(Request $request)
    {
        $input = $request->all();

        if($request->pass_check) {
            $users = User::where('email','=',$request->personal_email)->get();
            if(count($users) == 0) {
                if ($request->personal_pass == $request->personal_confirm){
                    $user = new User;
                    $user->name = $request->personal_name; 
                    $user->email = $request->personal_email;   
                    $user->password = bcrypt($request->personal_pass);
                    $token = md5(time().$request->personal_name.$request->personal_email);
                    $user->verification_link = $token;
                    $user->affilate_code = md5($request->name.$request->email);
                    $user->email_verified = 'Yes';
                    $user->save();
                    Auth::guard('web')->login($user);                     
                }else{
                    return redirect()->back()->with('unsuccess',"Confirm Password Doesn't Match.");     
                }
            }
            else {
                return redirect()->back()->with('unsuccess',"This Email Already Exist.");  
            }
        }

        if (!Session::has('cart')) {
            return redirect()->route('front.cart')->with('success',"You don't have any product to checkout.");
        }

        $settings = Generalsetting::findOrFail(1);
        $order['item_name'] = $settings->title." Order";
        $order['item_number'] = str_random(4).time();
        if (Session::has('currency')) {
            $curr = Currency::find(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', '=', 1)->first();
        }
        if ($curr->sign == "S.R") {
            $order['item_amount'] = $request->total / 3.75;
        } elseif ($curr->sign  == "$") {
            $value2 = $request->total;
            $order['item_amount'] = round($value2, 2);
        } else {
            // return redirect()->back()->with('unsuccess', "currency value Doesn't Match.");
        }
        $cancel_url = action('Front\PaymentController@paycancle');
        $notify_url = action('Front\PaymentController@notify');

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName($order['item_name']) /** item name **/
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice($order['item_amount']); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('USD')
            ->setTotal($order['item_amount']);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($order['item_name'].' Via Paypal');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl($notify_url) /** Specify return URL **/
            ->setCancelUrl($cancel_url);
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            return redirect()->back()->with('unsuccess',$ex->getMessage());
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }
        /** add payment ID to session **/
        Session::put('paypal_data',$input);
        Session::put('order_data',$order);
        Session::put('paypal_payment_id', $payment->getId());
        if (isset($redirect_url)) {
            /** redirect to paypal **/
            return Redirect::away($redirect_url);
        }
        return redirect()->back()->with('unsuccess','Unknown error occurred');
    }

    public function paycancle(){
        $this->code_image();
         return redirect()->route('front.checkout')->with('unsuccess','Payment Cancelled.');
     }

     public function payreturn(){
        $this->code_image();
        if(Session::has('tempcart')){
            $oldCart = Session::get('tempcart');
            $tempcart = new Cart($oldCart);
            $order = Session::get('temporder');
        }
        else{
            $tempcart = '';
            return redirect()->back();
        }
        Session::forget('cart');
        return view('front.success',compact('tempcart','order'));
     }

    public function notify(Request $request)
    {
        $paypal_data = Session::get('paypal_data');
        $order_data = Session::get('order_data');
        $success_url = action('Front\PaymentController@payreturn');
        $cancel_url = action('Front\PaymentController@paycancle');
        $input = $request->all();
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');

        /** clear the session payment ID **/
        if (empty( $input['PayerID']) || empty( $input['token'])) {
            return redirect($cancel_url);
        } 
        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($input['PayerID']);
        /**Execute the payment **/

        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {
            $resp = json_decode($payment, true);

            $oldCart = Session::get('cart');
            $cart = new Cart($oldCart);

            foreach($cart->items as $key => $prod)
            {
                if(!empty($prod['item']['license']) && !empty($prod['item']['license_qty']))
                {
                        foreach($prod['item']['license_qty']as $ttl => $dtl)
                        {
                            if($dtl != 0)
                            {
                                $dtl--;
                                $produc = Product::findOrFail($prod['item']['id']);
                                $temp = $produc->license_qty;
                                $temp[$ttl] = $dtl;
                                $final = implode(',', $temp);
                                $produc->license_qty = $final;
                                $produc->update();
                                $temp =  $produc->license;
                                $license = $temp[$ttl];
                                 $oldCart = Session::has('cart') ? Session::get('cart') : null;
                                 $cart = new Cart($oldCart);
                                 $cart->updateLicense($prod['item']['id'],$license);  
                                 Session::put('cart',$cart);
                                break;
                            }                    
                        }
                }
            }

                    $settings = Generalsetting::findOrFail(1);
                    $order = new Order;
                    $order['user_id'] = Auth::check() ? Auth::user()->id : $paypal_data['user_id'];
                    $order['cart'] = utf8_encode(bzcompress(serialize($cart), 9));
                    $order['totalQty'] = $paypal_data['totalQty'];
                    $order['pay_amount'] = $order_data['item_amount'];
                    $order['method'] = $paypal_data['method'];
                    $order['customer_email'] = $paypal_data['email'];
                    $order['customer_name'] = $paypal_data['name'];
                    $order['customer_phone'] = $paypal_data['phone'];
                    $order['order_number'] = $order_data['item_number'];
                    $order['shipping'] = $paypal_data['shipping'];
                    $order['pickup_location'] = isset($paypal_data['pickup_location'])?$paypal_data['pickup_location']:"";
                    $order['customer_address'] = $paypal_data['address'];
                    $order['customer_country'] = $paypal_data['customer_country'];
                    $order['customer_city'] = $paypal_data['city'];
                    $order['customer_zip'] = $paypal_data['zip'];
                    $order['shipping_email'] = $paypal_data['shipping_email'];
                    $order['shipping_name'] = $paypal_data['shipping_name'];
                    $order['shipping_phone'] = $paypal_data['shipping_phone'];
                    $order['shipping_address'] = $paypal_data['shipping_address'];
                    $order['shipping_country'] = $paypal_data['shipping_country'];
                    $order['shipping_city'] = $paypal_data['shipping_city'];
                    $order['shipping_zip'] = $paypal_data['shipping_zip'];
                    $order['order_note'] = $paypal_data['order_notes'];
                    $order['coupon_code'] = $paypal_data['coupon_code'];
                    $order['coupon_discount'] = $paypal_data['coupon_discount'];
                    $order['payment_status'] = 'Completed';
                    $order['currency_sign'] = $this->curr->sign;
                    $order['currency_value'] = $this->curr->value;
                    $order['shipping_cost'] = $paypal_data['shipping_cost'];
                    $order['packing_cost'] = $paypal_data['packing_cost'];
                    $order['tax'] = $paypal_data['tax'];
                    $order['dp'] = $paypal_data['dp'];
                    $order['transaction_id'] = $resp['transactions'][0]['related_resources'][0]['sale']['id'];

                    if($order['dp'] == 1)
                    {
                        $order['status'] = 'processing';
                    }

                    if (Session::has('affilate')) 
                    {
                        $val = $order_data['item_amount'] / $this->curr->value;
                        $val = $val / 100;
                        $sub = $val * $settings->affilate_charge;
                        $user = User::findOrFail(Session::get('affilate'));
                        if($user){
                            if($order['dp'] == 1)
                            {
                                $user->affilate_income += $sub;
                                $user->update();
                            }
        
                            $order['affilate_user'] = $user->id;
                            $order['affilate_charge'] = $sub;
                        }
                    }
                    $order->save();
                    
                    // SET WOCK Licence
                    $wockAPI = new WockAPI();
                    $return = $wockAPI->getProductsFromCart($order->id);
                    // END WOCK

                    if ($order->user_id != 0 && $order->wallet_price != 0) {
                        $transaction = new \App\Models\Transaction;
                        $transaction->txn_number = str_random(3).substr(time(), 6,8).str_random(3);
                        $transaction->user_id = $order->user_id;
                        $transaction->amount = $order->wallet_price;
                        $transaction->currency_sign = $order->currency_sign;
                        $transaction->currency_code = \App\Models\Currency::where('sign',$order->currency_sign)->first()->name;
                        $transaction->currency_value= $order->currency_value;
                        $transaction->details = 'Payment Via Wallet';
                        $transaction->type = 'minus';
                        $transaction->save();
                    }


        if($order->dp == 1){
            $track = new OrderTrack;
            $track->title = 'Completed';
            $track->text = 'Your order has completed successfully.';
            $track->order_id = $order->id;
            $track->save();
        }
        else {
            $track = new OrderTrack;
            $track->title = 'Pending';
            $track->text = 'You have successfully placed your order.';
            $track->order_id = $order->id;
            $track->save();
        }
                    
                    $notification = new Notification;
                    $notification->order_id = $order->id;
                    $notification->save();
                    
                    if($paypal_data['coupon_id'] != "")
                    {
                       $coupon = Coupon::findOrFail($paypal_data['coupon_id']);
                       $coupon->used++;
                       if($coupon->times != null)
                       {
                            $i = (int)$coupon->times;
                            $i--;
                            $coupon->times = (string)$i;
                       }
                       $coupon->update();

                    }
                    foreach($cart->items as $prod)
                    {
                        $x = (string)$prod['stock'];
                        if($x != null)
                        {
                            $product = Product::findOrFail($prod['item']['id']);
                            $product->stock =  $prod['stock'];
                            $product->update();                
                        }
                    }

        foreach($cart->items as $prod)
        {
            $x = (string)$prod['size_qty'];
            if(!empty($x))
            {
                $product = Product::findOrFail($prod['item']['id']);
                $x = (int)$x;
                $x = $x - $prod['qty'];
                $temp = $product->size_qty;
                $temp[$prod['size_key']] = $x;
                $temp1 = implode(',', $temp);
                $product->size_qty =  $temp1;
                $product->update();               
            }
        }

        foreach($cart->items as $prod)
        {
            $x = (string)$prod['stock'];
            if($x != null)
            {

                $product = Product::findOrFail($prod['item']['id']);
                $product->stock =  $prod['stock'];
                $product->update();  
                if($product->stock <= 5)
                {
                    $notification = new Notification;
                    $notification->product_id = $product->id;
                    $notification->save();                    
                }              
            }
        }

        $notf = null;

        foreach($cart->items as $prod)
        {
            if($prod['item']['user_id'] != 0)
            {
                $vorder =  new VendorOrder;
                $vorder->order_id = $order->id;
                $vorder->user_id = $prod['item']['user_id'];
                $notf[] = $prod['item']['user_id'];
                $vorder->qty = $prod['qty'];
                $vorder->price = $prod['price'];
                $vorder->order_number = $order->order_number;             
                $vorder->save();
            }

        }

        if(!empty($notf))
        {
            $users = array_unique($notf);
            foreach ($users as $user) {
                $notification = new UserNotification;
                $notification->user_id = $user;
                $notification->order_number = $order->order_number;
                $notification->save();    
            }
        }

        $gs = Generalsetting::find(1);

        //Sending Email To Buyer

        if($gs->is_smtp == 1)
        {

            $prod44 = "";
            $prod45 = '<table class="all t"><thead><tr class="all hd"><th class="all hd">المجموع النهائي:</th><th class="all hd">'. $this->curr->sign . round($order_data['item_amount'],2).'</th></tr></thead></table></h5>';
            foreach($cart->items as $prod12)
            {
                $prod44 .= ' <td class="all hd">'.$prod12["item"]["name"]. '</td><td class="all hd">'.$prod12['qty'].'</td><td class="all hd">'. $this->curr->sign . number_format($prod12["item"]["price"] * $order->currency_value,2,'.','') .'</td> <td class="all hd">'. $this->curr->sign . number_format($prod12["price"] * $order->currency_value,2,'.','') .'</td></tr>'.'</br>';
            }
            
            $data = [
                'to' => $order->customer_email,
                'subject' => " $order->order_number الطلب - Soft Fire",
                'body' => '<head>
                <style>
                .all{border:1px solid #ddd;text-align:right;}
                .t{border-collapse:collapse;width:100%;}
                .hd{padding:15px;}
                </style>
                </head><table cellspacing="0" cellpadding="0" width="600" align="center" border="0" style="direction:rtl;font-family:Roboto,RobotoDraft,Helvetica,Arial,sans-serif;font-size:14px;background-color:rgb(254,255,255);width:600px;margin:0 auto;"><tbody><tr><th style="text-align:left;border-width:medium;border-style:none;border-color:initial;width:600px;font-weight:normal;padding:0;"><table cellspacing="0" cellpadding="0" align="left" style="width:700.259px;"><tbody><tr height="102" style="height:102px;"><th style="text-align:left;border-width:medium;border-style:none;border-color:initial;width:570px;vertical-align:top;font-weight:normal;padding:20px 15px;background-color:transparent;"><table cellspacing="0" cellpadding="0" align="center" border="0" style="direction:rtl;margin:0 auto;"><tbody><tr><td align="center" style="padding:2px;"><table cellspacing="0" cellpadding="0" border="0"><tbody><tr><td style="border-width:medium;border-style:none;border-color:initial;background-color:transparent;"><img alt="" src="https://i.imgur.com/uMfiSTU.png" width="246" hspace="0" vspace="0" style="border-width:medium;border-color:initial;border-image:initial;outline:none;display:block;"></td></tr></tbody></table></td></tr></tbody></table></th></tr></tbody></table></th></tr><tr><th style="text-align:left;border-width:5px medium medium;border-style:solid none none;border-top-color:rgb(53, 181, 171);border-right-color:initial;width:600px;border-bottom-color:initial;font-weight:normal;padding:0;border-left-color:initial;"><table cellspacing="0" cellpadding="0" align="left" style="width:599.259px;"><tbody><tr height="206" style="height:206px;"><th style="text-align:left;border-width:medium;border-style:none;border-color:initial;width:570px;vertical-align:top;padding:10px 15px 20px;background-color:transparent;"><p style="margin-right:0;margin-bottom:10px;margin-left:0;color:rgb(68,68,68);line-height:23px;font-family:Helvetica,Arial,sans-serif;text-align:right;font-weight:normal;padding:0;">مرحباً '.$order->customer_name.'.</p><p style="color:rgb(68,68,68);font-family:Helvetica,Arial,sans-serif;font-weight:normal;text-align:right;background-color:rgb(255,255,255);margin-right:0;margin-bottom:10px;margin-left:0;padding:0;line-height:23px;">شكرا لتعاملكم مع سوفت فاير لقد تم استلام طلبك وسيتم انهاء الطلب بعد التأكد من اتمام عملية الدفع.</p><div style="width:268.862px;padding-right:15px;padding-left:15px;max-width:50%;color:rgb(51,51,51);font-family:&quot;Open Sans&quot;,sans-serif;font-size:16px;text-align:right;background-color:rgb(255,255,255);"><h5 style="font-size:18px;color:rgb(53, 181, 171);">تفاصيل الطلب</h5><address style="font-weight:400;font-size:14px;">رقم الطلب: '.$order->order_number.'<br>تاريخ الطلب: '.$order->created_at.'<br>البريد الإلكتروني: '.$order->customer_email.'<br><address>رقم الجوال او الهاتف: '.$order->customer_phone.'<br>&nbsp;طريقة الدفع : '.$order->method.'</address><address></address></address><address style="font-weight:400;font-size:14px;"><span style="color:rgb(53, 181, 171);font-size:18px">معلومات الدفع</span></address></div><div style="width:268.862px;padding-right:15px;padding-left:15px;max-width:50%;color:rgb(51,51,51);font-family:&quot;Open Sans&quot;,sans-serif;font-weight:400;text-align:right;background-color:rgb(255,255,255);"><span style="text-align:left;"><span style="font-weight:bolder;">PayPal</span></span></div><div style="color:rgb(70,85,65);font-size:16px;"><span style="text-align:left;"><span style="font-weight:bolder;"><br></span></span></div></div><p style="color:rgb(68,68,68);font-family:Helvetica,Arial,sans-serif;font-weight:normal;text-align:right;background-color:rgb(255,255,255);margin-right:0;margin-bottom:10px;margin-left:0;padding:0;line-height:23px;"><span style="color:rgb(53, 181, 171);font-family:&quot;Open Sans&quot;,sans-serif;font-size:18px"><br></span></p><p style="color:rgb(68,68,68);font-family:Helvetica,Arial,sans-serif;font-weight:normal;text-align:right;background-color:rgb(255,255,255);margin-right:0;margin-bottom:10px;margin-left:0;padding:0;line-height:23px;"><span style="color:rgb(53, 181, 171);font-family:&quot;Open Sans&quot;,sans-serif;font-size:18px">المنتجات</span><br></p><h5 style="font-size:18px;color:rgb(33,37,41);text-align:left;background-color:rgb(255,255,255);"><table class="all t"><thead><tr class="all hd"><th class="all hd">الاسم</th><th class="all hd">العدد</th><th class="all hd">السعر</th><th class="all hd">المجموع</th></tr></thead><tbody><tr class="all hd">                
                '.$prod44. $prod45.'
                <div style="color: rgb(51, 51, 51); font-size: 16px;"><br></div></h5><p style="color: rgb(68, 68, 68); font-family: Helvetica, Arial, sans-serif; font-weight: normal; text-align: right; background-color: rgb(255, 255, 255); margin-right: 0px; margin-bottom: 10px; margin-left: 0px; padding: 0px; line-height: 23px;"><br></p><table style="text-align: center; color: rgb(68, 68, 68); font-family: Helvetica, Arial, sans-serif; font-weight: 400; border-spacing: 0px; padding: 0px; vertical-align: middle; border-radius: 8px 8px 6px 6px; background-color: rgb(255, 255, 255); width: 670px; margin: 0px auto;"><tbody><tr style="padding: 0px; vertical-align: middle;"><td style="word-break: break-word; padding: 0px; vertical-align: middle; line-height: 23px; border-collapse: collapse;"><span style="display: block; font-size: 12px; padding: 10px 15px; text-align: right;"><div style="color: rgb(80, 0, 80);"><p style="margin-right: 0px; margin-bottom: 10px; margin-left: 0px; font-size: 14px; color: rgb(68, 68, 68); line-height: 23px; padding: 0px;"></p><div style="text-align: center;"><span style="font-weight: bolder;">&nbsp;مع أطيب التحيات&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</span></div><div style="text-align: center;"><span style="font-weight: bolder;">&nbsp; &nbsp;فريق سوفت فاير.&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</span></div></div></span></td></tr></tbody></table><table cellspacing="0" cellpadding="0" style="font-weight: normal; width: 599.259px;"><tbody><tr height="114" style="height: 114px;"><th style="text-align: left; border-width: medium; border-style: none; border-color: initial; width: 570px; vertical-align: top; font-weight: normal; padding: 1px 15px;"><div style="padding: 10px; text-align: center;"><table cellspacing="0" cellpadding="0" border="0" style="display: inline-block;"><tbody><tr><td style="padding-right: 5px;"><a href="https://twitter.com/s0ftfire" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://twitter.com/s0ftfire&amp;source=gmail&amp;ust=1579009031373000&amp;usg=AFQjCNHgZHC805SqK1lhdY-E5toppdipPA"><img title="Twitter" alt="Twitter" src="https://ci4.googleusercontent.com/proxy/lfCcWsp6o9C2_6Ab5Xj-057OmadKeOSO_Bl836cfCQhxXO81rWW4AB09Ce3uboSiIYEaROoU_qCBV8ZyyvbrUjF4cx9Hh4ZIOzVcXmP8oTL4J3nuCc0jJfAvDuKi=s0-d-e1-ft#https://soft-fire.com/img/Image_3_48e377b3-7322-4b87-a4d0-1f7a801ac916.png" width="48" style="border-width: medium; border-color: initial; border-image: initial; outline: none; display: block;"></a></td><td><a href="https://www.youtube.com/channel/UC1LmIeyogjplV0BS7WwUvnA?view_as=subscriber" target="_blank" data-saferedirecturl="https://www.google.com/url?q=https://www.youtube.com/channel/UC1LmIeyogjplV0BS7WwUvnA?view_as%3Dsubscriber&amp;source=gmail&amp;ust=1579009031373000&amp;usg=AFQjCNGx_GO_vC8Njfw4P840UKb7_kIZvQ"><img title="Youtube" alt="Youtube" src="https://ci3.googleusercontent.com/proxy/wk6EsjlRhi3dHejisAu9QuTMXUotykaTVmttjBubb1YXQr70BqZLSpAIJQv1Kd2gtTtOpPpKr4fGjnCNCK1XtNuOMOlDWwDKe9n0LlORBjVe57Ts24Vam_09eOcG=s0-d-e1-ft#https://soft-fire.com/img/Image_4_2159d08f-1a89-493a-8d4e-01c53adc95e0.png" width="48" style="border-width: medium; border-color: initial; border-image: initial; outline: none; display: block;"></a></td></tr></tbody></table></div><p align="center" style="margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: 10px; color: rgb(124, 124, 124); line-height: 12px; font-family: arial, helvetica, sans-serif; padding: 0px; text-align: center; background-color: transparent;">&nbsp;</p><p align="center" style="direction: rtl; margin-right: 0px; margin-bottom: 1em; margin-left: 0px; font-size: 10px; color: rgb(124, 124, 124); line-height: 12px; font-family: arial, helvetica, sans-serif; padding: 0px; text-align: center; background-color: transparent;">. Soft-Fire . All rights reserved 2020 ©</p></th></tr></tbody></table></th></tr></tbody></table></th></tr></tbody></table>',
            ];

            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);        

            // $mailer = new GeniusMailer();
            // $mailer->sendAutoOrderMail($data,$order->id);            
        }
        else
        {
           $to = $paypal_data['email'];
           $subject = "Your Order Placed!!";
           $msg = "Hello ".$paypal_data['name']."!\nYou have placed a new order.\nYour order number is ".$order_data['item_number'].".Please wait for your delivery. \nThank you.";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           mail($to,$subject,$msg,$headers);            
        }
        //Sending Email To Admin
        if($gs->is_smtp == 1)
        {
            $data = [
                'to' => Pagesetting::find(1)->contact_email,
                'subject' => "New Order Recieved!!",
                'body' => "Hello Admin!<br>Your store has received a new order.<br>Order Number is ".$order_data['item_number'].".Please login to your panel to check. <br>Thank you.",
            ];

            $mailer = new GeniusMailer();
            $mailer->sendCustomMail($data);            
        }
        else
        {
           $to = Pagesetting::find(1)->contact_email;
           $subject = "New Order Recieved!!";
           $msg = "Hello Admin!\nYour store has recieved a new order.\nOrder Number is ".$order_data['item_number'].".Please login to your panel to check. \nThank you.";
           $headers = "MIME-Version: 1.0" . "\r\n";
           $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
           $headers .= "From: ".$gs->from_name."<".$gs->from_email.">";
           mail($to,$subject,$msg,$headers);
        }

        Session::put('temporder',$order);
        Session::put('tempcart',$cart);
        Session::forget('cart');
        Session::forget('already');
        Session::forget('coupon');
        Session::forget('coupon_total');
        Session::forget('coupon_total1');
        Session::forget('coupon_percentage');
        Session::forget('cart');
        Session::forget('paypal_data');
        Session::forget('order_data');
        Session::forget('paypal_payment_id');
            return redirect($success_url);

        }
        return redirect($cancel_url);
    }

    // Capcha Code Image
    private function  code_image()
    {
        $actual_path = str_replace('project','',base_path());
        $image = imagecreatetruecolor(200, 50);
        $background_color = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image,0,0,200,50,$background_color);

        $pixel = imagecolorallocate($image, 0,0,255);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixel);
        }

        $font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
        $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $length = strlen($allowed_letters);
        $letter = $allowed_letters[rand(0, $length-1)];
        $word='';
        //$text_color = imagecolorallocate($image, 8, 186, 239);
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $cap_length=6;// No. of character in image
        for ($i = 0; $i< $cap_length;$i++)
        {
            $letter = $allowed_letters[rand(0, $length-1)];
            imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
            $word.=$letter;
        }
        $pixels = imagecolorallocate($image, 8, 186, 239);
        for($i=0;$i<500;$i++)
        {
            imagesetpixel($image,rand()%200,rand()%50,$pixels);
        }
        session(['captcha_string' => $word]);
        imagepng($image, $actual_path."assets/images/capcha_code.png");
    }

}