 @foreach ($suggestions as $product)
     @if ($product['qty'] > 0)
         <div class="col-lg-4 col-md-6 col-xs-6 mb-2 product-container"
             onclick="addtobasket({{ $product['id'] }},'{{ $product['cost_price'] }}','{{ $product['tax'] }}','{{ $product['code'] }}','{{ $product['qty'] }}','{{ $product['name'] }}')"
             id="product_{{ $product['id'] }}">
             <div class="card" style=" ">
                 <span class="badge bg-primary fitc">{{__('Price') }} {{ $product['cost_price'] }}</span>
                 @if (empty($product['img']))
                     <img src="/uploads/images/products/default.png" class=" card-img-top">
                 @else
                     <img src="/uploads/images/products/{{ $product['img'] }}" class=" card-img-top">
                 @endif
                 <div class="card-body">
                     <h5 class="card-title">{{ $product['name'] }}</h5>
                     <!--<div class="badge  bg-success">{{ $product['code'] }}</div>-->
                     <div class="badge  bg-warning"> {{__('Qty') }} {{ $product['qty'] }}</div>

                 </div>
             </div>
         </div>
     @endif
 @endforeach
