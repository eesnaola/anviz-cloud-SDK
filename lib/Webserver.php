<?php

/**
 * File Name: Webserver.php
 * Created by Jacobs <jacobs@anviz.com>.
 * Date: 2016-3-22
 * Time: 9:07
 * Description:
 */
//define('KEY', 'AnvizClientSecretkey');
define('KEY', 'AnvizCloudForAttDevice');

/** System */
define('CMD_LOGIN', 9001);              //Inicio de sesión
define('CMD_NOCOMMAND', 9002);          //Orden vacía
define('CMD_FORBIDDEN', 9003);          //Access defined
define('CMD_REGESTER', 9004);           //Registro del dispositivo
define('CMD_ERROR', 9005);              //Error de instrucción
/** Device */
define('CMD_GETNETWORK', 1003);         //Obtener los parámetros de red del dispositivo
/** Employee */
define('CMD_GETALLEMPLOYEE', 2001);     //Descargue información básica sobre todos los empleados en el dispositivo
define('CMD_PUTALLEMPLOYEE', 2101);     //Carga por lotes la información básica de los empleados en el dispositivo
define('CMD_GETONEEMPLOYEE', 2002);     //Obtener información básica del personal
define('CMD_PUTONEEMPLOYEE', 2102);     //Sube una información básica del personal
define('CMD_DELETEALLEMPLOYEE', 2021);  //Eliminar todos los empleados en el dispositivo
define('CMD_DELETEONEEMPLOYEE', 2022);  //Eliminar el empleado especificado del dispositivo
/** Fingerprint */
define('CMD_GETALLFINGER', 2031);       //Descargar todos los datos de huellas dactilares en el dispositivo
define('CMD_GETONEFINGER', 2032);       //Descargue datos de huellas digitales de un empleado
define('CMD_PUTALLFINGER', 2131);       //Datos de huellas dactilares de empleados carga por lotes
define('CMD_PUTONEFINGER', 2132);       //Cargar datos de huellas dactilares de un empleado
define('CMD_DELETEALLFINGER', 2041);    //Eliminar todos los datos de huellas dactilares del dispositivo
define('CMD_DELETEONEFINGER', 2042);    //Eliminar los datos de huellas dactilares de un empleado del dispositivo
/** Records */
define('CMD_GETALLRECORD', 3001);       //Descargar todos los registros de asistencia en el dispositivo
define('CMD_GETNEWRECORD', 3002);       //Descargue el último registro de asistencia del equipo

/** Demo Configure */
$demoData = array (
    'device_id' => 'a823ca8d9e2181d12727a03172ed182e',
    'token' => array (
        'randomkey' => '65869824',
        'randomtime' => '9999999999',
        'token' => '88925ccb'
    )
);

/**
 * Class Webserver
 */
class Webserver
{
    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionRegister
     * @param string $data
     * @Description: Register Device
     */
    public function actionRegister ($data = "")
    {
        global $demoData;

        Tools::log('debug', 'actionRegister: Data - ' . $data);
        if (empty($data)) {
            Tools::log('error', 'actionRegister: Receive Data is NULL');
            return false;
        }

        $result = Protocol::RegisterDevice($data);
        if (!$result) {
            Tools::log('error', 'actionRegister: Register fail');
            return false;
        }

        /** Create Device ID */
        //$device_id = Tools::uuid();
        /** Demo Device ID */
        $device_id = $demoData['device_id'];
        Tools::log('debug', 'actionRegister: Device ID - ' . $device_id);

        /** Create Token  */
        //$token = Protocol::getToken();
        /** Demo Token */
        $token = $demoData['token'];
        Tools::log('debug', 'actionRegister: RandomKey - ' . $token['randomkey'] . '; Token - ' . $token['token']);

        $device = array (
            'id' => $device_id,
            'randomkey' => $token['randomkey'],
            'randomtime' => $token['randomtime'],
            'token' => $token['token'],
            'serial_number' => $result['serial_number'],
            'model' => $result['model'],
            'firmware' => $result['firmware'],
            'protocol' => $result['protocol']
        );

        Tools::log('debug', 'actionRegister: Device - ' . json_encode($device));

        /** The Action of save data */


        /** Return to let device to login system */
        $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_LOGIN, 0, 32, $device_id);
        return Tools::R($token['randomkey'] . $command);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionTransport
     * @param string $serial_number
     * @param string $data
     * @Description:Transport
     */
    public function actionTransport ($serial_number = "", $data = "")
    {
        global $demoData;

        Tools::log('debug', 'actionTransport: Device - ' . $serial_number . '; Data - ' . $data);

        //$device_id = $serial_number;
        /** Demo Device ID */
        $device_id = $demoData['device_id'];
        $token = $demoData['token'];

        if (empty($serial_number) || empty($data)) {
            Tools::log('error', 'actionTransport: The lack of necessary parameters');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_ERROR, 0, 32, 0);
            return Tools::R($command);
        }

        if (time() < $token['randomkey']) {
            Tools::log('error', 'actionTransport: The token has expires');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_REGESTER, 0, 32, 0);
            return false;
        }

        $data = Protocol::explodeCommand($token['token'], $data);
        if (!$data) {
            Tools::log('error', 'actionTransport: The token has expires');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_REGESTER, 0, 32, 0);
            return false;
        }

        Tools::log('debug', 'actionTransport: Data - ' . json_encode($data));
        switch ($data['command']) {
            case CMD_REGESTER:
                $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_REGESTER, 0, 32, 0);
                return Tools::R($command);
                break;
            case CMD_LOGIN:
                $result = Protocol::LoginDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            case CMD_GETNETWORK:
                $result = Protocol::NetworkDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            case CMD_GETALLEMPLOYEE:
            case CMD_GETONEEMPLOYEE:
                $result = Protocol::EmployeeDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            case CMD_GETALLFINGER:
            case CMD_GETONEFINGER:
                $result = Protocol::FingerDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            case CMD_GETALLRECORD:
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            default:
                break;
        }
        Tools::log('debug', 'actionTransport: ' . $data['command'] . ' - ' . json_encode($result));

        /** Get the next command **/
        $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_NOCOMMAND, 5, 0);

        return Tools::R($command);
    }

    /**
     * @Created by Jacobs <jacobs@anviz.com>
     * @Name: actionReport
     * @param string $serial_number
     * @param string $data
     * @Description: Report
     */
    public function actionReport ($serial_number = "", $data = "")
    {
        global $demoData;

        Tools::log('debug', 'actionReport: Device - ' . $serial_number . '; Data - ' . $data);

        //$device_id = $serial_number;
        $device_id = $demoData['device_id'];
        $token = $demoData['token'];

        if (empty($serial_number) || empty($data)) {
            Tools::log('error', 'actionReport: The lack of necessary parameters');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_ERROR, 0, 32, 0);
            return Tools::R($command);
        }

        if (time() < $token['randomkey']) {
            Tools::log('error', 'actionReport: The token has expires');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_REGESTER, 0, 32, 0);
            return Tools::R($command);
        }

        $data = Protocol::explodeCommand($token['token'], $data);
        if (!$data) {
            Tools::log('error', 'actionReport: The token has expires');

            $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_REGESTER, 0, 32, 0);
            return Tools::R($command);
        }

        Tools::log('debug', 'actionReport: Data - ' . json_encode($data));
        switch ($data['command']) {
            case CMD_GETALLEMPLOYEE:
            case CMD_GETONEEMPLOYEE:
                $result = Protocol::EmployeeDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            case CMD_GETALLFINGER:
            case CMD_GETONEFINGER:
                $result = Protocol::FingerDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            case CMD_GETALLRECORD:
            case CMD_GETNEWRECORD:
                $result = Protocol::RecordDevice($data['content']);
                /**
                 *
                 *
                 */
                break;
            default:
                break;
        }
        Tools::log('debug', 'actionReport: ' . $data['command'] . ' - ' . json_encode($result));

        /** Get Next Command */
        $command = Protocol::joinCommand($token['token'], $device_id, '11111111', CMD_NOCOMMAND, 0, 32, 0);

        return Tools::R($command);
    }
}
