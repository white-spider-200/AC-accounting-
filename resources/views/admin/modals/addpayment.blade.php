<div id="myModal2" class="modal-new">

    <!-- Modal content -->
    <div class="modal-content-new fitc">
        <span class="close" id="custum-close">&times;</span>
        <form id="add_payment">
            <div id="modalContent">
            <div class="row">
                <div class="col-md-6 form-group mt-3">
                    <label>{{ __('Payment Date') }} *</label>
                    <input type="date" id="real_date" class="form-control" value="{{ date('Y-m-d') }}" />
                </div>
                <div class="col-md-6 mt-3">

                    <label>{{ __('Payment Type') }} *</label>
                    <select id="payment_type_id" class="form-select">
                        @foreach ($paymentTypes as $paymentType)
                            <option value="{{ $paymentType->id }}">
                                {{ app()->getLocale() == 'ar' ? $paymentType->label_ar : $paymentType->label_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mt-3">

                    <label>{{ __('Due Date') }} </label>
                    <input type="date" id="due_date" class="form-control" value="{{ date('Y-m-d') }}" />

                </div>

                <div class="col-md-6 mt-3">
                    <div id="message-payment"> </div>
                    <label>{{ __('Amount') }} *</label>
                    <input type="number" step="any" id="paid" class="form-control" min='0' />
                </div>
                <div class="col-md-6 mt-3">
                    <label>{{ __('Still') }} *</label>
                    <input type="text" id="still" class="form-control" readonly />
                </div>
                <div class="col-md-6 mt-3">
                    <label>{{ __('Comment') }} *</label>
                    <textarea id="comment" class="form-control"></textarea>
                </div>
            </div>
            <input type="hidden" id="mod_id" value="{{ isset(request()->pos) ? @$tempId : 0 }}" />
            @if (isset(request()-> pos))

            <input type="button" name="btn" value="{{ __('Save') }}" class="btn btn-primary mt-3"
                onclick="savealldata(event,'pos')" />
            @else
            <input type="button" name="btn" value="{{ __('Save') }}" class="btn btn-primary mt-3"
                onclick="savepayment()" />
            @endif
            </div>
        </form>
    </div>

</div>
