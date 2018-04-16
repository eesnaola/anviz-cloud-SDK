<?php
/**
 * File Name: index.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-22
 * Time: 9:26
 * Description:
 */


$path = pathinfo($_SERVER['PHP_SELF']);
$url = $_SERVER['HTTP_HOST'] . $path['dirname'] . '/webserver/wsdl.html?ws=1';
$url = $_SERVER['SERVER_PORT'] == 443 ? 'https://' . $url : 'http://' . $url;

$wsdl = $url;

/** Disable the cache of WSDL */
ini_set('soap.wsdl_cache_enabled', "0");

$soapClient = new SoapClient($url);
try {
    $result = $soapClient->actionRegister(base64_encode('12345678900987654321                 1.0                2.01                   1'));
    //    $result = $soapClient->actionTransport('a823ca8d9e2181d12727a03172ed182e', 'EAs7qKpmwrgG418qrz/nzKqdbb5sObuhbMupd6BMJEonKne58YU/u4J5kSsbe86Fdz2dCjYdfmJfU9qKRxIdw0ufWq74qtfkl/uWnDU3kUdNcIewKcqcyEufWq74qtfk');
    var_dump(base64_decode($result));
} catch (Exception $e) {
    echo $e->getMessage();
}