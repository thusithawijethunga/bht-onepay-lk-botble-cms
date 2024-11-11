<?php

namespace ByHeartLK\OnepayLk\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Routing\Controller;
use ByHeartLK\OnepayLk\Http\Requests\OnepayLkPaymentCallbackRequest;
use Illuminate\Http\Request;
use Botble\Ecommerce\Models\Order;
use Botble\Payment\Models\Payment;

class OnepayLkController extends Controller
{

    public function callback(OnepayLkPaymentCallbackRequest $request, BaseHttpResponse $response)
    {

        $appCallbackToken = setting('payment_onepaylk_appCallbackToken');
        $signature = $request->server('HTTP_AUTHORIZATION');
        $content = $request->getContent();

        if (!$appCallbackToken || !$signature || !$content) {
            return response()->noContent();
        }

        if ($appCallbackToken !== $signature) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed!'));
        }

        // Encode the array as a JSON string
        $json_content = json_decode($content);

        if (!isset($json_content->transaction_id)) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed!'));
        }

        $transaction_id = $json_content->transaction_id;

        $payment = Payment::query()
            ->where('charge_id', $transaction_id)
            ->first();

        if ($payment) {

            $order_id = $payment->order_id;

            $order = Order::query()->find($order_id);

            if ($order !== null) {

                $customer = $order->user;

                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'charge_id' => $payment->charge_id,
                    'order_id' => $order_id,
                    'customer_id' => $customer->id,
                    'customer_type' => get_class($customer),
                    'payment_channel' => ONEPAYLK_PAYMENT_METHOD_NAME,
                    'status' => PaymentStatusEnum::COMPLETED,
                ]);

            }

        }

        return response()->noContent();

    }

    public function getSuccess(Request $request, BaseHttpResponse $response)
    {

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL())
            ->setMessage(__('Checkout successfully!'));

    }

    public function getCancel(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->withInput()
            ->setMessage(__('Payment failed!'));
    }


}
