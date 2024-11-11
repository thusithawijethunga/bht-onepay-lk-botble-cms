# Merchant Account

Create [IPG App](https://merchant-v2.onepay.lk/pages/developer-configurations/ipg-apps) and Access [Merchant](https://merchant-v2.onepay.lk/) account for more...

### App Details

- App ID : `****************`
- App Token : `**********************************...` 
- Hash Salt : `********************` 

### Status Callback
- Callback URL : https://yoursite.com/checkout/payment/onepay/payment-success 
- Callback Token : `********************` 


```json
{
    "amount":270.0,
    "currency":"LKR",
    "app_id":"****************",
    "reference":"****************",
    "customer_first_name":"****************",
    "customer_last_name":"****************",
    "customer_phone_number":"+94********",
    "customer_email":"****************@gmail.com",
    "transaction_redirect_url":"https://yoursite.com/checkout/payment/onepay/payment-success",
    "additional_data":"****************"
} 
```
Send to this Link : `https://merchant-api-live-v2.onepay.lk/api/ipg/gateway/request-payment-link/?hash=d87b10d4dfcb2c8dab0ff7f26ffac17e0dbb7a03456f49d3f24e44155014927b`

```json
{
    "status":1000,
    "message":"success",
    "data":{
        "ipg_transaction_id":"****************",
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
                "redirect_url":"https://payment.onepay.lk/redirect/****************/****************/****************"
            }
    }
} 
```

This is sample code for read response for after each transaction. you need to host this file in your server and provide us url in our portal [callback url](https://yoursite.com/checkout/payment/onepay/payment-success)


```json
{
    "transaction_id": "****************", 
    "status": 1, 
    "status_message": "SUCCESS", 
    "additional_data": "****************"
}
```

Header Sequrity

```php
 [
    {
        "HTTP_AUTHORIZATION":"****************",
        "CONTENT_TYPE":"application/json; charset=UTF-8",
        "HTTP_USER_AGENT":"onepay/2.0"
    }
]
```