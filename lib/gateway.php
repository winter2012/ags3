<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 2018/8/23
 * Time: 12:40
 */
header("content-Type: text/html; charset=UTF-8");
$merchantPrivateKey = '-----BEGIN PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBALyb9hExD9TiYS1e
d6xnTN+MKZlj90hTXKejJDFJtzlvup/FeRTW9RjHFvKO24afmPZh2ZpS9bI7Hf6Z
MrUpS30TI0lUm/N3yf0GWQ5fvQbaWSMXdjIl0hB5q48taj6eQaogOQj0avdUdNj5
2B2haSy+G9J6hpjf3L+Irq394bDrAgMBAAECgYEAqghIG1OJnDfBt670YF54NQgc
8IXohjFw1EgE9tIn9gW9zw+tipYHO6EwFNepHIKA2Y15KOElUtpsvfvKdPuXasxA
pGhTvxVObQKI6xVMEFAxWh2nfAbut5kWWQGVa6g8iI6UKzaBCiuRo1Wv6Tx8Siu3
4yspLSRnK74ppd18nRECQQDeIovnQG8ZsBDDJhcdDnL+DwXVsGez6pCIqtfhEKRx
WP3nk+A4vnTiUj/rau8Uc/oL19w7DfKcEO7+E0ecdAcHAkEA2Vz6J1y3K9u+WKXG
De+pDC3It2xDuQN9B8/OHaxqxjm9Ly//Kx3UtXPuq3/lbzxZ3/yTrVzC33O7yhih
i+yJ/QJAYhkDi7aK2d9FJ8lUf8J3yfa8bugeg/fcqF46Q+xjkqLoTjKh3K1PVPtZ
uw9YUcH99Oj5GyNHtuBLiuzcvR0IVQJBAMPFlV1siWIMOiW3sWmN6PEaL4TdEyYJ
OUyW4ushBs5g5L8ieK3J4XJI57c5q1kDv2MZJ51mRfJiV8oPYzkWo7UCQDzRy2XK
FsYHgPg6R2Kz+9s7qe6ODHDVcyssfgwJ1Fj3Xx08J/Y8DPIh6heJvYPkzCITEn7d
iDlfKpN47ujEzOA=
-----END PRIVATE KEY-----';

$apiUrl = "https://pay.islpay.hk/gateway?input_charset=UTF-8";
$merchant_code = "103888887120";
$service_type ="direct_pay";
$interface_version ="V3.0";
$sign_type ="RSA-S";
$input_charset = "UTF-8";
$notify_url = iconv("GBK","UTF-8", "http://".$_SERVER['SERVER_NAME']);
$order_no = "AGS".date('YmdHis'.rand(100,999),time());
$order_time = date('Y-m-d H:i:s');
$order_amount = $_POST['faceValue'];
$bank_code = $_POST['payType'];
$product_name = $_POST['notice'] ? $_POST['notice'] : "default";

$signStr= "";
if($bank_code != ""){
    $signStr = $signStr."bank_code=".$bank_code."&";
}
$signStr = $signStr."input_charset=".$input_charset."&";
$signStr = $signStr."interface_version=".$interface_version."&";
$signStr = $signStr."merchant_code=".$merchant_code."&";
$signStr = $signStr."notify_url=".$notify_url."&";
$signStr = $signStr."order_amount=".$order_amount."&";
$signStr = $signStr."order_no=".$order_no."&";
$signStr = $signStr."order_time=".$order_time."&";
$signStr = $signStr."product_name=".$product_name."&";
$signStr = $signStr."service_type=".$service_type;

$merchant_private_key = openssl_get_privatekey($merchantPrivateKey);
openssl_sign($signStr,$sign_info,$merchant_private_key,OPENSSL_ALGO_MD5);
$sign = base64_encode($sign_info);

$data = [
    'bank_code'         => $bank_code,
    'merchant_code'     => $merchant_code,
    'service_type'      => $service_type,
    'notify_url'        => $notify_url,
    'interface_version' => $interface_version,
    'input_charset'     => $input_charset,
    'order_no'          => $order_no,
    'order_time'        => $order_time,
    'order_amount'      => $order_amount,
    'product_name'      => $product_name,
    'sign'              => $sign,
    'sign_type'         => "RSA-S"
];

$sHtml = "<form name='paySubmit' action='".$apiUrl."' method='post'>";
foreach ($data as $key => $val) {
    $sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
}
$sHtml.= "</form>";
$sHtml.= "<script>document.forms['paySubmit'].submit();</script>";
echo $sHtml;