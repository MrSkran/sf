@extends('layouts.front')
@section('content')


<section class="user-dashbord">
    <div class="container">
      <div class="row">
        @include('includes.user-dashboard-sidebar')
        <div class="col-lg-8">
            <div class="user-profile-details">
                <div class="order-history">
                    <div class="header-area d-flex align-items-center">
                        <h4 class="title">Order Tracking</h4>          
                    </div>
                        <div class="order-tracking-content">
                            @include('includes.form-success')
                            <form id="t-form" class="tracking-form">
                                {{ csrf_field() }}
                                <input type="text" id="code" placeholder="Get Trackng Code" required="">
                                <button type="submit" class="mybtn1">View Tracking</button>
                                <a href="#"  data-toggle="modal" data-target="#order-tracking-modal"></a>
                            </form>
                        </div>
                      
                    </div>
                </div>
		    </div>
	    </div>
	</div>
</section>


<!-- Order Tracking modal Start-->
    <div class="modal fade" id="order-tracking-modal" tabindex="-1" role="dialog" aria-labelledby="order-tracking-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"> <b>Order Tracking</b> </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="order-track">

            </div>
            </div>
        </div>
    </div>
<!-- Order Tracking modal End -->


@endsection

@section('scripts')

<script type="text/javascript">
    $('#t-form').on('submit',function(e){
        e.preventDefault();
        var code = $('#code').val();
        $('#order-track').load('{{ url("user/order/trackings/") }}/'+code);
        $('#order-tracking-modal').modal('show');
    });


</script>

@endsection

