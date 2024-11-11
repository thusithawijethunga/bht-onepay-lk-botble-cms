<?php

namespace ByHeartLK\OnepayLk\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Payment\Forms\PaymentMethodForm;

class OnepayLkPaymentMethodForm extends PaymentMethodForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(ONEPAYLK_PAYMENT_METHOD_NAME)
            ->paymentName('OnepayLk')
            ->paymentDescription(__('Customer can buy product and pay with :name', ['name' => 'OnePay Lk']))
            ->paymentLogo(url('vendor/core/plugins/onepaylk/images/onepaylk.png'), )
            ->paymentUrl('https://merchant-v2.onepay.lk/')
            ->paymentInstructions(view('plugins/onepaylk::settings')->render())

            ->add(
                sprintf('payment_%s_appId', ONEPAYLK_PAYMENT_METHOD_NAME),
                'text',
                TextFieldOption::make()
                    ->label(__('App Id'))
                    ->attributes(['data-counter' => 100])
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('appId', ONEPAYLK_PAYMENT_METHOD_NAME))
                    ->toArray()
            )

            ->add(
                sprintf('payment_%s_appToken', ONEPAYLK_PAYMENT_METHOD_NAME),
                'text',
                TextFieldOption::make()
                    ->label(__('App Token'))
                    ->attributes(['data-counter' => 400])
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('appToken', ONEPAYLK_PAYMENT_METHOD_NAME))
                    ->toArray()
            )

            ->add(
                sprintf('payment_%s_appHashSalt', ONEPAYLK_PAYMENT_METHOD_NAME),
                'text',
                TextFieldOption::make()
                    ->label(__('App Hash Salt'))
                    ->attributes(['data-counter' => 100])
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('appHashSalt', ONEPAYLK_PAYMENT_METHOD_NAME))
                    ->toArray()
            )

            ->add(
                sprintf('payment_%s_appCallbackToken', ONEPAYLK_PAYMENT_METHOD_NAME),
                'text',
                TextFieldOption::make()
                    ->label(__('Status Callback Token'))
                    ->attributes(['data-counter' => 400])
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('appCallbackToken', ONEPAYLK_PAYMENT_METHOD_NAME))
                    ->toArray()
            )

            ->add(
                sprintf('payment_%s_nativePhone', ONEPAYLK_PAYMENT_METHOD_NAME),
                'tel',
                TextFieldOption::make()
                    ->label(__('Native Phone'))
                    ->attributes(['data-counter' => 12])
                    ->value(BaseHelper::hasDemoModeEnabled() ? '+94*********' : get_payment_setting('nativePhone', ONEPAYLK_PAYMENT_METHOD_NAME))
                    ->toArray()
            )

            ->add(
                sprintf('payment_%s_nativeEmail', ONEPAYLK_PAYMENT_METHOD_NAME),
                'email',
                TextFieldOption::make()
                    ->label(__('Native E-Mail'))
                    ->attributes(['data-counter' => 100])
                    ->value(BaseHelper::hasDemoModeEnabled() ? '**********@***.**' : get_payment_setting('nativeEmail', ONEPAYLK_PAYMENT_METHOD_NAME))
                    ->toArray()
            )

            ;
    }
}
