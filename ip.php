<?php
 
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
  CURLOPT_POSTFIELDS =>'{
    "amount": 5,
    "callBackUrl": "https://www.example.com",
    "clientEmail": "myclient@email.com",
    "clientMobile": "0509200900",
    "clientName": "Zaid Matooq",
    "note": "This invoice is for VIP client.",
    "orderNumber": "MERCHANT-ANY-UNIQUE-ORDER-NUMBER-123123123",
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
    'Authorization: eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJoYXNzYW4uYXlvdWIuMTk4MEBnbWFpbC5jb20iLCJhdXRoIjoiUk9MRV9NRVJDSEFOVCxST0xFX01FUkNIQU5UX0FDQ09VTlQiLCJleHAiOjE2MTA4MzU1MzF9.mxAW2zPsj_zHrwBpo8-JhbW9AlN2SR6g5oPIwSdgTRHlZBg8rqZxokY0VeHThx89GbuQ89-DdPGENaWl2nO_1Qnull',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
