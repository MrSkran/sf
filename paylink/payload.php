<?php
include_once __DIR__ . '/config.php';
header('Content-Type: application/json');

function GetToken()
{
    global $setting;
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://restapi.paylink.sa/api/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($setting),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response, true);
    return $response['id_token'];
}

function Addinv($token)
{

    $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'restpilot.paylink.sa/api/addInvoice',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        "amount": 5,
        "callBackUrl": "https://www.example.com",
        "clientEmail": "myclient@email.com",
        "clientMobile": "0509200900",
        "clientName": "Zaid Matooq",
        "note": "This invoice is for VIP client.",
        "orderNumber": "MERCHANT-ANY-UNIQUE-ORDER-NUMBER-12313123",
        "products": [
            {
                "description": "Brown Hand bag leather for ladies",
                "imageSrc": "http://merchantwebsite.com/img/img1.jpg",
                "price": 150,
                "qty": 1,
                "title": "Hand bag"
            }
        ]
    }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token . '',
                'Content-Type: application/json'
            ),
        ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
}



echo Addinv(GetToken());
