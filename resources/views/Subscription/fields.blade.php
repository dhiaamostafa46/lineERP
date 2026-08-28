<div class="table-responsive">
    <table class="table table-striped  table-bordered text-center gy-7 gs-7" id="subscription">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('models/Subscription.fields.from')</th>
                <th>@lang('models/Subscription.fields.to')</th>
                <th>@lang('models/Subscription.fields.price')</th>
                <th>@lang('models/Subscription.fields.subscription')</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($subscriptions as $key=> $item)
                <tr>
                    <td>{{  $key }} </td>
                    <td>{{ $item['from'] }} @lang('models/Subscription.fields.user')</td>
                    <td>{{ $item['to'] }} @lang('models/Subscription.fields.user')</td>
                    <td>
                        @if ($item['price'] == '')
                            @lang('models/Subscription.fields.up')
                        @else
                            {{ $item['price'] }} @lang('models/Subscription.fields.SAR')
                        @endif
                    </td><script src="{{ asset('admin_assets') }}/js/widgets.bundle.js"></script>
                    <td>
                        <a href="{{route('Subscription.payment' ,[1,$key])}}" class="btn btn-icon btn-light-facebook me-5 "> <img src="{{ asset('admin_assets') }}/media/payment/mada.svg" style="w" alt=""></a>
                        <a href="{{route('Subscription.payment' ,[2,$key])}}" class="btn btn-icon btn-light-facebook me-5 ">  <i class="fab fa-cc-visa fs-4"></i> </a>
                        <a href="{{route('Subscription.payment' ,[3,$key])}}" class="btn btn-icon btn-light-twitter me-5 "><img src="{{ asset('admin_assets') }}/media/payment/apple-pay.svg" alt=""> </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
