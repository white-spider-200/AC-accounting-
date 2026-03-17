<div class="card">
    <div class="card-body">

        <div class="row">
            @foreach ($images as $image)
                <div class="col-md-2 mt-2" id="img-container-{{ $image->id }}">
                    <img src="{{ env('APP_URL') }}/uploads/images/products/{{ $image->img }}" width="120" height="120"
                        id="{{ $image->img }}" />
                    <div class="mt-1">
                        <a href="javascript:void(0)"
                            onclick="deleteit({{ $image->id }},'deleteproduct')" class="btn btn-danger thin-p" >{{ __('Delete') }}</a>
                        <a href="javascript:void(0)"
                            onclick="product_image({{ $image->id }})" id="main-{{ $image->id }}" class="btn btn-success thin-p">
                            @if($image-> main == 1)
                            <span class="badge bg-success"> {{ __('Main') }} </span>
                            @else
                            {{ __('Set Main') }}
                            @endif
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</div>
