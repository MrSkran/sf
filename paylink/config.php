<?php

$setting = array(
    "apiId" => "APP_ID_1123453311", // Data model to send to login using Merchant API account
    "persistToken" => false, //This is boolean value. if set to true, then the returned token valid for 30 hours. Otherwise, the returned token will be valid for 30 minutes.
    "secretKey" => "0662abb5-13c7-38ab-cd12-236e58f43766"
    //This secret key and must be saved in a secure place and must not be exposed outside the server side of the merchant system.
    // Secret key is given by Paylink. If you need the SECRET KEY, send request for Merchant API account to email info@paylink.sa
);

//echo $setting['apiId'];