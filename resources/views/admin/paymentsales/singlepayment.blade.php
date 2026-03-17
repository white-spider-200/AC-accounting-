<style>
    .bordered table {
        text-align: right;
    }

    .bordered tr:nth-child(even) {
        background-color: #ccc;
    }

    .bordered tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .bordered th {
        border: 1px solid #d1d5db;
        padding-top: 10px;
        padding-bottom: 10px;
        padding-right: 10px;
        text-align: right;

    }

    .bordered td {
        border: 1px solid #d1d5db;
        padding-right: 10px;
        text-align: right;

    }
</style>

<table width="100%">
    <tr>
        <td><img src="/uploads/images/{{ @$allSetting['logo']->field_value_en }}" width="100" height="100"
                alt="Logo" id="logo"></td>
        <td style="    text-align: right;
       padding-right: 14px;"> {{ date('Y/M/d') }} : {{ __('Date') }} <br/> {{ @$sale-> id }} :{{ __('ID')}}</td>
    </tr>
</table>
<br />
<table width="100%">
    <tr>

        <td width="15%" style="    text-align: right;
        padding-right: 14px;">
            <table width="100%" style="    direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};">
                <tr>
                    <td colspan="5" style="    padding: 10px;
                    border-bottom: solid 1px #ccc"> {{ __('Company Name') }} <br/></td>
                </tr>
                <tr>
                    <td> <b>{{ __('Name') }}</b> : </td>
                    <td>
                        {{ app()->getLocale() == 'ar' ? @$allSetting['name']->field_value_ar : @$allSetting['name']->field_value_en }}
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>{{ __('Address') }}</b> :
                    </td>
                    <td>
                        {{ app()->getLocale() == 'ar' ? @$allSetting['address']->field_value_ar : @$allSetting['address']->field_value_en }}
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>{{ __('Email') }}</b> :
                    </td>
                    <td>
                        {{ app()->getLocale() == 'ar' ? @$allSetting['email']->field_value_ar : @$allSetting['email']->field_value_en }}
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>{{ __('Phone') }}</b> :
                    </td>
                    <td>
                        {{ app()->getLocale() == 'ar' ? @$allSetting['phone']->field_value_ar : @$allSetting['phone']->field_value_en }}
                        <br />
                    </td>
                </tr>
            </table>

        </td>
        <td width="15%" style="    text-align: right;
        padding-right: 14px;">
            <table width="100%" style="    direction: {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }};">
                <tr>
                    <td colspan="5" style="    padding: 10px;
                    border-bottom: solid 1px #ccc"> {{ __('Client  Name') }} <br/> </td>
                </tr>
                <tr>
                    <td> <b>{{ __('Name') }}</b> : </td>
                    <td>
                        {{ @$sale->client->name }}
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>{{ __('Address') }}</b> :
                    </td>
                    <td>
                        {{ @$sale->client->address }}
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>{{ __('Email') }}</b> :
                    </td>
                    <td>
                        {{ @$sale-> client-> email }}
                        <br />
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>{{ __('Phone') }}</b> :
                    </td>
                    <td>
                        {{ @$sale-> client-> phone }}
                        <br />
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<br />
<table class="bordered" width="100%">
    <thead>

        <tr>
            <th>{{ __('Id') }}</th>

            <th>{{ __('Date') }}</th>
            <th>{{ __('Payment Type') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('User') }}</th>
        </tr>
    </thead>
    <tbody>

        <tr>
            <td> {{ $payment->id }} </td>
            <td> {{ $payment->real_date }} </td>
            <td> {{ $payment->paymentType->label_en }} </td>
            <td> <b>{{ $payment->paid }} </b></td>
            <td> {{ $payment->user->name }} </td>
        </tr>

    </tbody>
</table>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.print();
    });
</script>
