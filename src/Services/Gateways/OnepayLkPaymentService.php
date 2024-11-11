<?php

namespace ByHeartLK\OnepayLk\Services\Gateways;

use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Http;

class OnepayLkPaymentService
{

    protected string $appId;

    protected string $appHashSalt;

    protected string $appToken;

    protected string $appCallbackToken;

    protected string $nativePhone;

    protected string $nativeEmail;

    public string $onePayURL;

    public function __construct()
    {

        $this->appId            = setting('payment_onepaylk_appId');
        $this->appHashSalt      = setting('payment_onepaylk_appHashSalt');
        $this->appToken         = setting('payment_onepaylk_appToken');
        $this->appCallbackToken = setting('payment_onepaylk_appCallbackToken');

        $this->nativePhone      = setting('payment_onepaylk_nativePhone', "+94775802112");
        $this->nativeEmail      = setting('payment_onepaylk_nativeEmail', "thusithawijethunga@gmail.com");

        $this->onePayURL        = 'https://merchant-api-live-v2.onepay.lk/api/ipg/gateway/request-payment-link/?hash=';

    }

    /**
     * Check if the necessary credentials are set and valid.
     *
     * @return array|null
     */
    protected function checkCredentials(): ?array
    {

        if (empty($this->appId)) {
            return [
                'statusCode'    => 4518,
                'statusMessage' => 'App ID not filled or found. Please contact your admin.',
            ];
        }

        if (empty($this->appHashSalt)) {
            return [
                'statusCode'    => 4518,
                'statusMessage' => 'App Hash Salt not filled or found. Please contact your admin.',
            ];
        }

        if (empty($this->appToken)) {
            return [
                'statusCode'    => 4518,
                'statusMessage' => 'App Token not filled or found. Please contact your admin.',
            ];
        }

        if (empty($this->appCallbackToken)) {
            return [
                'statusCode'    => 4518,
                'statusMessage' => 'Status Callback Token not filled or found. Please contact your admin.',
            ];
        }

        if (empty($this->nativePhone)) {
            return [
                'statusCode'    => 4518,
                'statusMessage' => 'Native Phone not filled or found. Please contact your admin.',
            ];
        }

        if (empty($this->nativeEmail)) {
            return [
                'statusCode'    => 4518,
                'statusMessage' => 'Native Email not filled or found. Please contact your admin.',
            ];
        }

        return null; // Credentials are valid
    }
    public static function cleanName($string)
    {
        $string1 = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string1); // Removes special chars.
    }

    public function makePayment(array $data)
    {
        // Check credentials first
        $credentialCheck = $this->checkCredentials();
        if ($credentialCheck) {
            // Return the error message if credentials are missing
            return $credentialCheck;
        }

        $order_id       = $data['orders'][0]->id;
        $redirect_url   = route('payments.onepaylk.success', ['order_id' => $order_id]);

        $amount         = $data['amount']; // Payment amount
        $reference      = 'SPONPAYLK-' . $data['orders'][0]->id;
        $currency       = $data['currency'];
        $description    = $data['description'];
        $address        = $data['address'];

        $firstname      = $this->cleanName($address['name'] ?? "Guest");
        $lastname       = $this->cleanName("onepaylk");
        $contact        = $address['phone'] ?? $this->nativePhone;
        $email          = $address['email'] ?? $this->nativeEmail;
        $customer_id    = $data['customer_id'];
        $customer_type  = $data['customer_type'];

        $requestData = array(
            "amount"                    => round($amount), //only upto 2 decimal points
            "currency"                  => $currency, //LKR OR USD
            "app_id"                    => $this->appId,
            "reference"                 => "{$reference}", //must have 10 or more digits , spaces are not allowed
            "customer_first_name"       => $firstname, // spaces are not allowed
            "customer_last_name"        => $lastname, // spaces are not allowed
            "customer_phone_number"     => $contact, //must start with +94, spaces are not allowed
            "customer_email"            => $email, // spaces are not allowed
            "transaction_redirect_url"  => $redirect_url, // spaces are not allowed
            "additional_data"           => $reference //only support string, spaces are not allowed, this will return in response also
        );

        $enc_data = json_encode($requestData, JSON_UNESCAPED_SLASHES);

        $data_json = $enc_data . $this->appHashSalt;
        $hash_result = hash('sha256', $data_json);
        $url = $this->onePayURL . $hash_result;

        $response = Http::withHeaders([
            'Authorization'     => $this->appToken,
            'Content-Type'      => 'application/json',
        ])
            ->post($url, $requestData);

        $result = $response->json();

        if (isset($result['status']) && $result['status'] !== 1000) {
            return [
                'statusCode'    => $result['status'],
                'statusMessage' => $result['message'],
            ];
        }

        if (!empty($result['data']['ipg_transaction_id'])) {
            $chargeId = $result['data']['ipg_transaction_id'];
        }

        $paymentStatus = PaymentStatusEnum::PENDING();

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount'            => $amount,
            'currency'          => $currency,
            'charge_id'         => $chargeId,
            'order_id'          => $order_id,
            'customer_id'       => $customer_id,
            'customer_type'     => $customer_type,
            'payment_channel'   => ONEPAYLK_PAYMENT_METHOD_NAME,
            'status'            => $paymentStatus,
        ]);

        if (!empty($result['data']['gateway']['redirect_url'])) {
            return $result['data']['gateway']['redirect_url'];
        }

    }

    public function getToken($data)
    {
        $order = Order::find($data['order_id']);

        return $order->token;
    }

    public function supportedCurrencyCodes(): array
    {
        return ['USD', 'LKR'];
    }

}
