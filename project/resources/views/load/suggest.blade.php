@foreach($prods as $prod)
	<div class="docname">
		<a href="{{ route('front.product', $prod->slug) }}">
			{!! strlen($prod->name) > 26 ? str_replace($slug,'<b>'.$slug.'</b>',substr($prod->name,0,26)).'...' : str_replace($slug,'<b>'.$slug.'</b>',$prod->name)  !!} - {{ $prod->showPrice() }}
			
		</a>
		<img src="{{ asset('assets/images/thumbnails/'.$prod->thumbnail) }}" alt="" style="
    width: 50px;
    height: 50px;
">
	</div> 
@endforeach