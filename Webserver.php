<?php

/**
 * File Name: Webserver.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-21
 * Time: 17:16
 * Description:
 */

/**
 * true: Enable debug mode
 * false: Disable debug mode
 */
define('DEBUG', true);

include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Tools.php');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Protocol.php');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Webserver.php');
include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'SoapDiscovery.php');


header("Content-type: text/xml; charset=utf-8");
/**
 * Generate WSDL file
 * eg. http://localhost/webserver/wsdl.html
 */
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $wsdl = new SoapDiscovery('Webserver', 'Webserver');
    $content = $wsdl->getWSDL();

    header('Content-Length: ' . (function_exists('mb_strlen') ? mb_strlen($content, '8bit') : strlen($content)));

    echo $content;
}
/** Create SOAP Server */
else {
    $url = $_SERVER['HTTP_HOST'] . '/' . $_SERVER['PHP_SELF'];
    $url = $_SERVER['SERVER_PORT'] == 443 ? 'https://' . $url : 'http://' . $url;
    $options = array (
        'encoding' => 'UTF-8',
    );
    $soapServer = new SoapServer($url . "?wsdl");
    $soapServer->setClass('Webserver');
    $soapServer->handle();
}