<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Bangkok');

include_once $config['PATH_TO_DATA']  . 'data_setting.php';

$current_view = $config['PATH_TO_VIEW'] . DS . 'setting' . DS;

switch (get('action')) {
    default:
    case 'view':
        $serport_params = params_serialport();
        $data_mod2rd = data_modbus2rd();
        $data_modrdconf = data_modrdconf();
        $view = $current_view . 'view.phtml';
        break;

    case 'setcomm':
        $ports = system_serialport();
        $view = $current_view . 'setcomm.phtml';
        break;

    case 'setdata':
        $file_setmodaddr = $config['PATH_TO_DATA'] . 'file_modaddr.json';
        $data_mod2rd = data_modbus2rd();
        $view = $current_view . 'setdata.phtml';
        break;

    case 'setnetw':
        $data_mod2rd = data_modbus2rd();
        $view = $current_view . 'setnetwork.phtml';
        break;
}
