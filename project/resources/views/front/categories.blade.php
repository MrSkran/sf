@extends('layouts.front')
@section('content')

<div class="category-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white">
                    @foreach($categories as $category)
                        <div class="sub-category-menu">
                            <h3 class="category-name"><a href="{{ route('front.category',$category->slug) }}">{{ $category->name }}</a></h3>
                            @if(count($category->subs) > 0)
                                <ul>
                                @foreach($category->subs as $subcat)
                                    <li>
                                        <a href="{{ route('front.subcat',['slug1' => $subcat->category->slug, 'slug2' => $subcat->slug]) }}">{{$subcat->name}}</a>
                                    </li>
                                @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection