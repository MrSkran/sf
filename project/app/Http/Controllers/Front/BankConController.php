<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Childcategory;
use App\Models\Product;
use App\Models\ProductClick;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Rating;
use App\Classes\Paginate;
use Auth;
use Session;
use Illuminate\Support\Collection;

class BankConController extends Controller
{

// CATEGORIES SECTOPN

public function MyBank()
{
    return view('front.myb');
}


// CATEGORIES SECTION ENDS









}
