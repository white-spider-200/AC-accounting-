@if (count($payments) > 0)
                <div class="table-responsive">
                    <table class="table table-striped ">
                        <thead>

                            <tr>
                                <th>{{ __('Id') }}</th>

                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Payment Type') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Actions') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments  as $payment )
                            <tr>
                                <td> {{ $payment-> id }} </td>
                                <td> {{ $payment-> real_date }} </td>
                                <td>
                                    {{ app()->getLocale() == 'ar' ? $payment-> paymentType -> label_ar  : $payment-> paymentType -> label_en }}
                                </td>
                                <td> <b>{{ $payment-> paid }} </b></td>
                                <td> {{ $payment-> user->  name }} </td>
                                <td><a href="/admin/payment/{{ $payment-> id}}" class="btn btn-secondary" title="{{ __('Print') }}"><i class="bi  bi-printer "></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
@else
    <b>{{ __('No Data') }}</b>
@endif
