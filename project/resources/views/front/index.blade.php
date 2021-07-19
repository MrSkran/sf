@extends('layouts.front')

@section('content')

	@if($ps->slider == 1)

		@if(count($sliders))

			@include('includes.slider-style')

		@endif

	@endif

	@if($ps->slider == 1 || $ps->service == 1)
		<!-- Hero Area Start -->
		<section class="hero-area">
			@if($ps->slider == 1)

				@if(count($sliders))
					<div class="hero-area-slider">
						<div class="intro-carousel">
							@foreach($sliders as $data)
								<div class="intro-content {{$data->position}}" style="background-image: url({{asset('assets/images/sliders/'.$data->photo)}})">
									<div class="container">
										<div class="row">
											<div class="col-lg-12">
												<div class="slider-content">
													<!-- layer 1 -->
													<div class="layer-1">
														<h4 style="font-size: {{$data->subtitle_size}}px; color: {{$data->subtitle_color}}" class="subtitle subtitle{{$data->id}}" data-animation="animated {{$data->subtitle_anime}}">{{$data->subtitle_text}}</h4>
														<h2 style="font-size: {{$data->title_size}}px; color: {{$data->title_color}}" class="title title{{$data->id}}" data-animation="animated {{$data->title_anime}}">{{$data->title_text}}</h2>
													</div>
													<!-- layer 2 -->
													<div class="layer-2">
														<p style="font-size: {{$data->details_size}}px; color: {{$data->details_color}}"  class="text text{{$data->id}}" data-animation="animated {{$data->details_anime}}">{{$data->details_text}}</p>
													</div>
													<!-- layer 3 -->
	@if($data->slid_link == 1)

													<div class="layer-3">
														<a href="{{$data->link}}" target="_blank" class="mybtn1"><span>{{ $langg->lang25 }} <i class="fas fa-chevron-right"></i></span></a>
													</div>
@endif

												</div>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				@endif

			@endif

		</section>
		<!-- Hero Area End -->
	@endif


	{{-- Info Area Start --}}
	<section class="info-area">
		<div class="container">
			@if($ps->service == 1)
            @if($langg->rtl == "1")
								<div class="row">
						<div class="col-lg-12 p-0">
							<div class="info-big-box">
								<div class="row">
										<div class="col-6 col-xl-3 p-0">
											<div class="info-box">
												<div class="icon">
													<img src="{{ asset('assets/images/services/1561348133service1.png') }}">
												</div>
												<div class="info">
													<div class="details">
														<h4 class="title">سرعة تنفيذ الطلب</h4>
														<p class="text">
															نحن سريعون جدأ
														</p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-6 col-xl-3 p-0">
											<div class="info-box">
												<div class="icon">
													<img src="{{ asset('assets/images/services/1561348138service2.png') }}">
												</div>
												<div class="info">
													<div class="details">
														<h4 class="title">سهولة الدفع</h4>
														<p class="text">
															لدينا طرق دفع متعددة وسهلة
														</p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-6 col-xl-3 p-0">
											<div class="info-box">
												<div class="icon">
													<img src="{{ asset('assets/images/services/1561348143service3.png') }}">
												</div>
												<div class="info">
													<div class="details">
														<h4 class="title">أسترجاع المنتج</h4>
														<p class="text">
															استرجاع للمنتج في حالة وجود اي خلل
														</p>
													</div>
												</div>
											</div>
										</div>
										<div class="col-6 col-xl-3 p-0">
											<div class="info-box">
												<div class="icon">
													<img src="{{ asset('assets/images/services/1561348147service4.png') }}">
												</div>
												<div class="info">
													<div class="details">
														<h4 class="title">قسم الدعم الفني</h4>
														<p class="text">
															لدينا طاقم خبير لمساعدتكم على اكمل وجه
														</p>
													</div>
												</div>
											</div>
										</div>
								</div>
							</div>
						</div>
					</div>
@else
				@foreach($services->chunk(4) as $chunk)

					<div class="row">

						<div class="col-lg-12 p-0">
							<div class="info-big-box">
								<div class="row">
									@foreach($chunk as $service)
										<div class="col-6 col-xl-3 p-0">
											<div class="info-box">
												<div class="icon">
													<img src="{{ asset('assets/images/services/'.$service->photo) }}">
												</div>
												<div class="info">
													<div class="details">
														<h4 class="title">{{ $service->title }}</h4>
														<p class="text">
															{!! $service->details !!}
														</p>
													</div>
												</div>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>

					</div>

				@endforeach

			@endif
			@endif

		</div>
	</section>
	{{-- Info Area End  --}}

	@if($ps->featured == 1)
		<!-- Trending Item Area Start -->
		<section  class="trending">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 remove-padding">
						<div class="section-top">
							<h2 class="section-title">
								{{ $langg->lang26 }}
							</h2>
							{{-- <a href="#" class="link">View All</a> --}}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 remove-padding">
						<div class="trending-item-slider">
							@foreach($feature_products as $prod)
								@include('includes.product.slider-product')
							@endforeach
						</div>
					</div>

				</div>
			</div>
		</section>
		<!-- Tranding Item Area End -->
	@endif

	@if($ps->small_banner == 1)

		<!-- Banner Area One Start -->
		<section class="banner-section">
			<div class="container">
				@foreach($top_small_banners->chunk(2) as $chunk)
					<div class="row">
						@foreach($chunk as $img)
							<div class="col-lg-6 remove-padding">
								<div class="left">
									<a class="banner-effect" href="{{ $img->link }}" target="_blank">
										<img src="{{asset('assets/images/banners/'.$img->photo)}}" alt="">
									</a>
								</div>
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
		</section>
		<!-- Banner Area One Start -->
	@endif

	<section id="extraData">
		<div class="text-center">
			<img src="{{asset('assets/images/'.$gs->loader)}}">
		</div>
	</section>


@endsection

@section('scripts')
	<script>
        $(window).on('load',function() {

            setTimeout(function(){

                $('#extraData').load('{{route('front.extraIndex')}}');

            }, 500);
        });

	</script>
@endsection