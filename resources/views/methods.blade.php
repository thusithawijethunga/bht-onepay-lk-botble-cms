@if (setting('payment_onepaylk_status') == 1)
    <x-plugins-payment::payment-method
        :name="ONEPAYLK_PAYMENT_METHOD_NAME"
        paymentName="OnePay LK"
        label="Pay with OnePay IPG"
        description="Onepay Sri Lanka’s first payment gateway!"
        :supportedCurrencies="(new ByHeartLK\OnepayLk\Services\Gateways\OnepayLkPaymentService)->supportedCurrencyCodes()"
    >
        <x-slot name="currencyNotSupportedMessage">
            <p class="mt-1 mb-0">
                {{ __('Learn more') }}:
                {{ Html::link('https://merchant-v2.onepay.lk/pages/developer-configurations/ipg-apps', attributes: ['target' => '_blank', 'rel' => 'nofollow']) }}.
            </p>
        </x-slot>
    </x-plugins-payment::payment-method>
@endif
