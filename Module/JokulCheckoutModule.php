<?php

require_once dirname( __FILE__ ) . '/JokulBased.php';
require_once dirname( __FILE__ ) . '/JokulCore.php';

class JokulCheckoutPayment {

    public function hitApi($config, $params)
    {
        $header = array();

        $requestId = 'Request-'.$params['invoiceNumber'];
        $targetPath= "/checkout/v1/payment";
        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        $dateTimeFinal = substr($dateTime,0,19)."Z";

        $data = array(
            "order" => array(
                "amount" => $params['amount'],
                "invoice_number" => $params['invoiceNumber'],
                "line_items" => $params['lineItem'],
                "currency" => "IDR",
                "callback_url" => $params['urlSuccess']
            ),
            "payment" => array(
                "payment_due_date" => $params['expiryTime']
            ),
            "customer" => array(
                "id" => $params['customerId'],
                "name" => trim($params['customerName']),
                "email" => $params['customerEmail'],
                "phone" => $params['phone'],
                "address" => $params['address'],
                "country" => $params['country']
            )
        );
        $env = $config['environment'] === 'production'? 'production': 'sandbox';
        $getUrl = JokulBased::getBaseUrl($env);
        $url = $getUrl.$targetPath;

        $header['Client-Id'] = $config['client_id'];
        $header['Request-Id'] = $requestId;
        $header['Request-Timestamp'] = $dateTimeFinal;
        $header['Request-Target'] = $targetPath;

        $signature = JokulCore::generateSignature($header, json_encode($data), $config['shared_key']);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Signature:'.$signature,
            'Request-Id:'.$requestId,
            'Client-Id:'.$config['client_id'],
            'Request-Timestamp:'.$dateTimeFinal,

        ));

        $responseJson = curl_exec($ch);

        curl_close($ch);

        if (is_string($responseJson)) {
            return json_decode($responseJson, true);
        } else {
            print_r($responseJson);
        }
    }
}

?>
