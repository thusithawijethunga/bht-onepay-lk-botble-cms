# OnePay Lk Payment gateway for Botble CMS

OnePay Lk Payment gateway for Botble CMS

This  plugin allows you to integrate OnePay payment sri lanka gateway into Botble CMS.

## Installation

- Download and extract plugin files to `platform/plugins/onepay-lk` folder.
- Go to Admin Panel > Plugins, then activate OnePay LK plugin.
- Go to Admin Panel > Settings > Payment methods, then enter your OnePay LK credentials.

## License

This plugin is released under the [MIT license](LICENSE).

## About Author

This plugin is developed by [ByHeartLK](https://github.com/thusithawijethunga/onepay-lk-botble-cms/).

Contact me at [thusithawijethunga@gmail.com](mailto:thusithawijethunga@gmail.com) for more information.

Access [Merchant](https://merchant-v2.onepay.lk/) account for more...

## Changelog

Please see the [Releases](../../releases) page for more information what has changed recently.


```json
{
    "amount":270.0,
    "currency":"LKR",
    "app_id":"3CO41187CB311837E7B90",
    "reference":"SPONPAYLK-20",
    "customer_first_name":"widhura-dictionary",
    "customer_last_name":"hadawathalk",
    "customer_phone_number":"+94775802112",
    "customer_email":"widhuradictionary@gmail.com",
    "transaction_redirect_url":"https://hadawatha.lk/checkout/payment/onepay/payment-success",
    "additional_data":"SPONPAYLK-20"
} 
```

`https://merchant-api-live-v2.onepay.lk/api/ipg/gateway/request-payment-link/?hash=d87b10d4dfcb2c8dab0ff7f26ffac17e0dbb7a03456f49d3f24e44155014927b`

```json
{
    "status":1000,
    "message":"success",
    "data":{
        "ipg_transaction_id":"S3N6118E6DE77F93465B7",
        "amount":
            {
                "gross_amount":270.0,
                "discount":0,
                "handling_fee":0,
                "net_amount":270.0,
                "currency":"LKR"
            },
        "gateway":
            {
                "redirect_url":"https://payment.onepay.lk/redirect/3CO41187CB311837E7B90/S3N6118E6DE77F93465B7/c69d0268893731c86af59ef5d4cf82051cdbdcf82521591cc836df9e71f0f968"
            }
    }
} 
```

<!-- This is sample code for read response for after each transaction. you need to host this file in your server and provide us url in our portal (callback url) -->

<!-- Sample response -> {'transaction_id': 'E2D51187B9A137DB7E867', 'pl_ref_no': '', 'status': 1, 'status_message': 'SUCCESS'} -->

```json
{
    "transaction_id": "HWHG118E6DE7D35D51742", 
    "status": 1, 
    "status_message": "SUCCESS", 
    "additional_data": "SPONPAYLK-23"
}
```

```php
 [
    {
        "HTTP_AUTHORIZATION":"V0hxaYUuDa1BvJLhgJSPnftNx7KNz+3VtGw8iJQ7euo=",
        "CONTENT_TYPE":"application/json; charset=UTF-8",
        "HTTP_USER_AGENT":"onepay/2.0"
    }
]
```