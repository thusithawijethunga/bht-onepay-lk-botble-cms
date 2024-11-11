<ul>
    @foreach($payments->payments as $payment)
        <li>
            @include('plugins/onepaylk::detail', compact('payment'))
        </li>
    @endforeach
</ul>
