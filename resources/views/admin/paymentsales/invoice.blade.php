<div id="invoice-POS">
    <div style="max-width: 400px; margin: 0px auto;">
        <div class="info">
            <div class="invoice_logo text-center mb-2">
                <img src="{{ env('APP_URL') }}/uploads/images/{{ @$allSetting['logo']->field_value_en }}" alt="" width="60" height="60">
            </div>
            <table class="change mt-3" style="font-size: 13px;
            width: 100%;
            font-weight: bold;" >
                <tr>
                    <td>{{ __('Date') }} </td>
                    <td style="text-align: left;">{{ date('Y/M/d') }} </td>
                </tr>
                <tr>
                    <td>{{ __('Address') }} </td>
                    <td style="text-align: left;">{{ app()->getLocale() == 'ar' ? @$allSetting['address']->field_value_ar : @$allSetting['address']->field_value_en }} </td>
                </tr>
                <tr>
                    <td>{{ __('Email') }} </td>
                    <td style="text-align: left;"> {{ app()->getLocale() == 'ar' ? @$allSetting['email']->field_value_ar : @$allSetting['email']->field_value_en }} </td>
                </tr>
                <tr>
                    <td> {{ __('Phone') }} </td>
                    <td style="text-align: left;">{{ app()->getLocale() == 'ar' ? @$allSetting['phone']->field_value_ar : @$allSetting['phone']->field_value_en }} </td>
                </tr>
                <tr>
                    <td> {{ __('Phone') }} </td>
                    <td style="text-align: left;">{{ app()->getLocale() == 'ar' ? @$allSetting['phone']->field_value_ar : @$allSetting['phone']->field_value_en }} </td>
                </tr>
                <tr>
                    <td> {{ __('Client') }}  </td>
                    <td style="text-align: left;">{{ $sale-> client-> name }}</td>
                </tr>
                <tr>
                    <td> {{ __('Warehouse') }}  </td>
                    <td style="text-align: left;">{{ $sale-> warehouse-> name }} </td>
                </tr>
            </table>

        </div>
        <table class="table_data" width="100%">
            <tbody>
                @foreach ($sale->details as $detail)
                <tr>
                    <td colspan="3">
                        {{ $detail->product->label_en }}
                        <span>{{ $detail-> qty }} x
                            {{ $detail-> price }}</span>
                    </td>
                    <td style="text-align: right; vertical-align: bottom;">{{ $detail-> total }}</td>
                </tr>
                @endforeach
                <tr style="margin-top: 10px;">
                    <td colspan="3" class="total">{{ __('Tax') }}</td>
                    <td class="total" style="text-align: right;">{{ $sale-> tax_whole_sale_send }} (	{{ $sale-> order_tax }} %)</td>
                </tr>
                <tr style="margin-top: 10px;">
                    <td colspan="3" class="total">{{ __('Discount') }}</td>
                    <td class="total" style="text-align: right;">{{ $sale-> discount }}</td>
                </tr>
                <tr style="margin-top: 10px;">
                    <td colspan="3" class="total">{{ __('Shipping') }}</td>
                    <td class="total" style="text-align: right;">{{ $sale-> shippment_price }}</td>
                </tr>
                <tr style="margin-top: 10px;">
                    <td colspan="3" class="total">{{ __('Grand Total') }}</td>
                    <td class="total" style="text-align: right;">{{ $sale-> grand_total }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">{{ __('Paid') }}</td>
                    <td class="total" style="text-align: right;">  {{ $sale-> paid }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="total">{{ __('Due') }}</td>
                    <td class="total" style="text-align: right;"> {{ $sale-> due }}</td>
                </tr>
            </tbody>

        </table>
        <table class="change mt-3" style="font-size: 20px;width: 100%" >
            <thead>
                <tr style="background: rgb(238, 238, 238);">
                    <th colspan="1" style="text-align: left;"> {{ __('Paid By') }} </th>
                    <th colspan="2" style="text-align: center;">{{ __('Amount') }}  </th>
                    <th colspan="1" style="text-align: right; display: none">Change Return:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="1" style="text-align: left;">
                        @foreach ($sale-> payments as $payment)
                        {{ app()->getLocale() == 'ar' ? $payment->paymentType->label_ar : $payment->paymentType->label_en }}
                        @endforeach
                    </td>
                    <td colspan="2" style="text-align: center;"> {{ $sale-> paid }}</td>

                </tr>
            </tbody>
        </table>
        <div id="legalcopy" class="ml-2">
            <p class="legal" style="display: none"><strong>Thank You For Shopping With Us . Please Come Again</strong></p>
            <div id="bar">
                <div textmargin="0" fontoptions="bold" class="barcode">


                </div>
            </div>
            <button class="btn btn-outline-primary d-print-none mt-5" onclick="printInvoice();">
                <i class="i-Billing"></i>
                {{ __('Print') }}
            </button>
        </div>
    </div>
</div>

