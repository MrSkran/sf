@extends('layouts.vendor')
        
@section('content')
        <div class="content-area">

                    <div class="mr-breadcrumb">
                        <div class="row">
                            <div class="col-lg-12">
                                <h4 class="heading">{{ $langg->lang586 }} <a class="add-btn" href="{{ route('vendor-order-show',$order->order_number) }}"><i class="fas fa-arrow-left"></i> {{ $langg->lang550 }}</a></h4>
                                <ul class="links">
                                    <li>
                                        <a href="{{ route('vendor-dashboard') }}">{{ $langg->lang441 }} </a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">{{ $langg->lang443 }}</a>
                                    </li>
                                    <li>
                                        <a href="javascript:;">{{ $langg->lang586 }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-table-wrap">
                        <div class="invoice-wrap">
                            <div class="invoice__title">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="invoice__logo text-left">
                                            <img src="{{ asset('assets/images/'.$gs->logo) }}"
                                                alt="woo commerce logo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row invoice__metaInfo">
                                <div class="col-lg-4 col-md-6">
                                                        <div class="buyer">
                                                            <p class="font-weight-bold">{{ $langg->lang587 }}</p>
                                                            <strong>{{$order->customer_name}}</strong>
                                                            <address>
                                                                {{$order->customer_address}}<br>
                                                                {{$order->customer_city}}<br>
                                                                {{$order->customer_country}}<br>
                                                            </address>
                                                        </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                                        <div class="invoce__date">
                                                            <strong>{{ $langg->lang588 }}</strong>
                                                            <p>{{ $langg->lang589}}</p>
                                                            <p>{{ $langg->lang590 }}</p>
                                                        </div>
                                </div>  
                                <div class="col-lg-4 col-md-6">
                                                        <div class="invoce__number">
                                                            <strong>{{sprintf("%'.08d", $order->id)}}</strong>
                                                            <p>{{date('d-M-Y',strtotime($order->created_at))}}</p>
                                                            <p>{{$order->order_number}}</p>
                                                        </div>
                                </div>
                                        

                                        
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="invoice_table">
                                            <div class="mr-table">
                                                    <div class="table-responsive">
                                                            <table id="example2" class="table table-hover dt-responsive" cellspacing="0" width="100%">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ $langg->lang591 }}</th>
                                                                        <th>{{ $langg->lang592 }}</th>
                                                                        <th>{{ $langg->lang593 }}</th>
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
                                            <td><a target="_blank" href="{{route('front.product',['id1' => $product['item']['id'], $product['item']['name']])}}">{{strlen($product['item']['name']) > 30 ? substr($product['item']['name'],0,30).'...' : $product['item']['name']}}</a></td>
                                            <td>{{$product['size']}}</td>
                                            <td><span style="float: right; width: 40px; height: 20px; display: block; background: {{$product['color']}};"></span></td>
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
                                                                <td colspan="5">{{ $langg->lang597 }}</td>
                                                                <td>{{$order->currency_sign}}{{ round($subtotal, 2) }}</td>
                                                              </tr>
                                                              @if($user->shipping_cost != 0)
                                                              @php 
                                                              $subtotal = $subtotal + round($user->shipping_cost * $order->currency_value , 2);
                                                              @endphp
                                                              <tr>
                                                                <td colspan="5">{{ $langg->lang598 }}({{$order->currency_sign}})</td>
                                                                <td>{{ round($user->shipping_cost * $order->currency_value , 2) }}</td>
                                                              </tr>
                                                              @endif
                                                              @if($order->tax != 0)
                                                              <tr>
                                                                <td colspan="5">{{ $langg->lang599 }}({{$order->currency_sign}})</td>
                                                                @php 
                                                                $tax = ($subtotal / 100) * $order->tax;
                                                                $subtotal =  $subtotal + $tax;
                                                                @endphp
                                                                <td>{{round($tax,2)}}</td>
                                                              </tr>
                                                              @endif
                                                              <tr>
                                                                <td colspan="4"></td>
                                                                <td>{{ $langg->lang600 }}</td>
                                                                <td>{{$order->currency_sign}}{{ round($subtotal, 2) }}</td>
                                                              </tr>
                                                            </tfoot>  
                                                            </table>
                                                    </div>
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
                                                @if($order->dp == 0)
                                                    <div class="invoice__shipping">
                                                        <p><strong>{{ $langg->lang606 }}</strong></p>
                                                        <p>{{$order->shipping_name == null ? $order->customer_name : $order->shipping_name}}</pstyle="text-align:>
                                                        <address>
                                                            {{$order->shipping_address == null ? $order->customer_address : $order->shipping_address}}<br>
                                                            {{$order->shipping_city == null ? $order->customer_city : $order->shipping_city}}<br>
                                                            {{$order->shipping_country == null ? $order->customer_country : $order->shipping_country}}<br>
                                                        </address>
                                                    </div>
                                                @endif
                                                </div>
                                <div class="col-sm-6 text-right">
                                    <a class="btn  add-newProduct-btn print"
                                        href="{{route('vendor-order-print',$order->order_number)}}"
                                        target="_blank"><i class="fa fa-print"></i> {{ $langg->lang607 }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main Content Area End -->
            </div>
        </div>
        </div>

@endsection