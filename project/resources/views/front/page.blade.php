@extends('layouts.front')
@section('content')

<!-- Breadcrumb Area Start -->
<div class="breadcrumb-area">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <ul class="pages">
          <li>
            <a href="{{ route('front.index') }}">
              {{ $langg->lang17 }}
            </a>
          </li>
          <li>
            <a href="{{ route('front.page',$page->slug) }}">
                @if($langg->rtl == "1")

              {{ $page->title }}
                @else
                {{ $page->title2 }}
                @endif  
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- Breadcrumb Area End -->



<section class="about">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="about-info">
            <h4 class="title">
                           @if($langg->rtl == "1")
              {!! $page->title !!}
                @else
                {{ $page->title2 }}
@endif   
            </h4>
            <p>
                @if($langg->rtl == "1")
              {!! $page->details !!}
                @else
                {!! $page->details2 !!}
@endif
            </p>

          </div>
        </div>
      </div>
    </div>
  </section>

@endsection