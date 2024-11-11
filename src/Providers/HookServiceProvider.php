<?php

namespace ByHeartLK\OnepayLk\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use ByHeartLK\OnepayLk\Forms\OnepayLkPaymentMethodForm;
use ByHeartLK\OnepayLk\Services\Gateways\OnepayLkPaymentService;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerOnepayLkMethod'], 2, 2);

        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithOnepayLk'], 2, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 2);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['VITTAPAY'] = ONEPAYLK_PAYMENT_METHOD_NAME
                ;
            }

            return $values;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ONEPAYLK_PAYMENT_METHOD_NAME
            ) {
                $value = 'OnepayLk';
            }

            return $value;
        }, 2, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == ONEPAYLK_PAYMENT_METHOD_NAME
            ) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 2, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == ONEPAYLK_PAYMENT_METHOD_NAME
            ) {
                $data = OnepayLkPaymentService::class;
            }

            return $data;
        }, 2, 2);
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . OnepayLkPaymentMethodForm::create()->renderForm();
    }

    public function registerOnepayLkMethod(?string $html, array $data): string
    {
        PaymentMethods::method(ONEPAYLK_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/onepaylk::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithOnepayLk(array $data, Request $request): array
    {
        if ($request->input('payment_method') == ONEPAYLK_PAYMENT_METHOD_NAME
        ) {
            $currentCurrency = get_application_currency();

            $currencyModel = $currentCurrency->replicate();

            $bKashPayService = $this->app->make(OnepayLkPaymentService::class);

            $supportedCurrencies = $bKashPayService->supportedCurrencyCodes();

            $currency = strtoupper($currentCurrency->title);

            $notSupportCurrency = false;

            if (! in_array($currency, $supportedCurrencies)) {
                $notSupportCurrency = true;

                if (! $currencyModel->where('title', 'LKR')->exists()) {
                    $data['error'] = true;
                    $data['message'] = __(":name doesn't support :currency. List of currencies supported by :name: :currencies.", [
                        'name' => 'OnepayLk',
                        'currency' => $currency,
                        'currencies' => implode(', ', $supportedCurrencies),
                    ]);

                    return $data;
                }
            }

            $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

            if ($notSupportCurrency) {
                $usdCurrency = $currencyModel->where('title', 'LKR')->first();

                $paymentData['currency'] = 'LKR';
                if ($currentCurrency->is_default) {
                    $paymentData['amount'] = $paymentData['amount'] * $usdCurrency->exchange_rate;
                } else {
                    $paymentData['amount'] = format_price($paymentData['amount'], $currentCurrency, true);
                }
            }

            $checkoutUrl = $bKashPayService->makePayment($paymentData);

            if (isset($checkoutUrl['statusCode'])) {
                // If statusCode exists in the response
                if (isset($checkoutUrl['statusMessage'])) {
                    $data['message'] = $checkoutUrl['statusMessage'];
                } else {
                    $data['message'] = __('Something went wrong. Please try again later.');
                }
                $data['error'] = true;
            } else {
                // If no statusCode, assuming a successful response
                if ($checkoutUrl) {
                    $data['checkoutUrl'] = $checkoutUrl;
                } else {
                    $data['error'] = true;
                    $data['message'] = __('Something went wrong. Please try again later.');
                }
            }

            return $data;
        }

        return $data;
    }
}
