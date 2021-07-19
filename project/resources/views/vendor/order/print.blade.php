<!DOCTYPE html>
<html>
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="{{$seo->meta_keys}}">
        <meta name="author" content="GeniusOcean">

        <title>{{$gs->title}}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{asset('assets/print/bootstrap/dist/css/bootstrap.min.css')}}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('assets/print/font-awesome/css/font-awesome.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{asset('assets/print/Ionicons/css/ionicons.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('assets/print/css/style.css')}}">
  <link href="{{asset('assets/print/css/print.css')}}" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <link rel="icon" type="image/png" href="{{asset('assets/images/'.$gs->favicon)}}"> 
  <style type="text/css">
@page { size: auto;  margin: 0mm; }
@page {
  size: A4;
  margin: 0;
}
@media print {
  html, body {
    width: 210mm;
    height: 287mm;
  }

html {
    overflow: scroll;
    overflow-x: hidden;
}
::-webkit-scrollbar {
    width: 0px;  /* remove scrollbar space */
    background: transparent;  /* optional: just make scrollbar invisible */
}
  </style>
</head>
<body onload="window.print();">
                                        <div class="invoice-wrap">
                                            <div class="invoice__title">
                                                <div class="row reorder-xs">
                                                    <div class="col-sm-6">
                                                        <div class="invoice__logo text-left">
                                                            <img src="{{asset('assets/images/'.$gs->logo)}}" alt="woo commerce logo">
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="invoice__metaInfo">
                                                        <div class="buyer" style="width: 60%;">
                                                            <p>{{ $langg->lang587 }}</p>
                                                            <strong>{{$order->customer_name}}</strong>
                                                            <address>
                                                                {{$order->customer_address}}<br>
                                                                {{$order->customer_city}}<br>
                                                                {{$order->customer_country}}<br>
                                                            </address>
                                                        </div>

                                                        <div class="invoce__date"  style="width: 20%;">
                                                            <strong>{{ $langg->lang588 }}</strong>
                                                            <p>{{ $langg->lang589}}</p>
                                                            <p>{{ $langg->lang590 }}</p>
                                                        </div>

                                                        <div class="invoce__number"  style="width: 20%;">
                                                            <strong>{{sprintf("%'.08d", $order->id)}}</strong>
                                                            <p>{{date('d-M-Y',strtotime($order->created_at))}}</p>
                                                            <p>{{$order->order_number}}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="invoice__table">
                                                        <div class="table-responsive">
                                                          <table class="table table-hover">
                                                            <thead>
                                                              <tr>
                                                                <th>{{ $langg->lang591 }}</th>
                                                                <th>{{ $langg->lang594 }}</th>
                                                                <th>{{ $langg->lang595 }}</th>
                                                                <th>{{ $langg->lang596 }}</th>
                                                              </tr>
                                                            </thead>
                                                            <tbody>
                                    @php
                                    $subtotal = 0;
                                    $tax = 0;
                                    $ship = 0;
                                    @endphp                                   
                                @foreach($cart->items as $product)
                                @if($product['item']['user_id'] != 0)
                                    @if($product['item']['user_id'] == $user->id)
                                    <tr>
                                            <td>{{strlen($product['item']['name']) > 30 ? substr($product['item']['name'],0,30).'...' : $product['item']['name']}}</td>
                                            <td>{{$order->currency_sign}}{{ round($product['item']['price'] * $order->currency_value , 2) }}</td>
                                            <td>{{$product['qty']}} {{ $product['item']['measure'] }}</td>
                                            <td>{{$order->currency_sign}}{{ round($product['price'] * $order->currency_value , 2) }}</td>
                                            @php
                                            $subtotal += round($product['price'] * $order->currency_value , 2);
                                            @endphp

                                    </tr>
                                    @endif
                                @endif
                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                              <tr>
                                                                <td colspan="3">{{ $langg->lang597 }}</td>
                                                                <td>{{$order->currency_sign}}{{ round($subtotal, 2) }}</td>
                                                              </tr>
                                                              @if($ship != 0)
                                                              <tr>
                                                                <td colspan="3">{{ $langg->lang598 }}({{$order->currency_sign}})</td>
                                                                <td>{{ round($user->shipping_cost * $order->currency_value , 2) }}</td>
                                                              </tr>
                                                              @endif
                                                              @if($order->tax != 0)
                                                              <tr>
                                                                <td colspan="3">{{ $langg->lang599 }}({{$order->currency_sign}})</td>
                                                                @php 
                                                                $subtotal = $subtotal + round($user->shipping_cost * $order->currency_value , 2);
                                                                $tax = ($subtotal / 100) * $order->tax;
                                                                $total =  $subtotal + $tax;
                                                                @endphp
                                                                <td>{{round($tax,2)}}</td>
                                                              </tr>
                                                              @endif
                                                              <tr>
                                                                <td colspan="2"></td>
                                                                <td>{{ $langg->lang600 }}</td>
                                                                <td>{{$order->currency_sign}}{{ round($total, 2) }}</td>
                                                              </tr>
                                                            </tfoot>            
                                                          </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="invoice__orderDetails">
                                                        <p><strong>{{ $langg->lang601 }}</strong></p>
                                            @if($order->dp == 0)
                                                <p>{{ $langg->lang602 }}:   
                                                    @if($order->shipping == "pickup")
                                                        {{ $langg->lang603 }}
                                                    @else
                                                        {{ $langg->lang604 }}
                                                    @endif
                                                </p>
                                            @endif
                                                        <p>{{ $langg->lang605 }}: {{$order->method}}</p>
                                                    </div>
                                                </div>
                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="invoice__shipping">
                                                    @if($order->dp == 0)
                                                        <p style="text-align: left;"><strong>{{ $langg->lang606 }}</strong></p>
                                                        <p style="text-align: left;">{{$order->shipping_name == null ? $order->customer_name : $order->shipping_name}}</p>
                                                        <address>
                                                            {{$order->shipping_address == null ? $order->customer_address : $order->shipping_address}}<br>
                                                            {{$order->shipping_city == null ? $order->customer_city : $order->shipping_city}}<br>
                                                            {{$order->shipping_country == null ? $order->customer_country : $order->shipping_country}}<br>
                                                        </address>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
<!-- ./wrapper -->

<script type="text/javascript">
setTimeout(function () {
        window.close();
      }, 500);
</script>
</body>
</html>
