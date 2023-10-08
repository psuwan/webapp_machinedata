<?php

function params_serialport()
{
    $params = array();
    $file_serialsetting = __DIR__ . DS . 'serialport_config.json';
    if (file_exists($file_serialsetting)) {
        $json_serialparams = file_get_contents($file_serialsetting);
        $params = json_decode($json_serialparams, true);
    }

    return $params;
}

function system_serialport()
{
    $ports = array();
    $file_systemports = __DIR__ . DS . 'serialport_system.json';
    if (file_exists($file_systemports)) {
        $json_systemports = file_get_contents($file_systemports);
        $ports = json_decode($json_systemports, true);
    } else {
        $ports = array(
            __DIR__,
            'xxxx', 'yyyy'
        );
    }

    return $ports;
}

function data_modbus2rd()
{
    $db_conn = db_connected();

    $sqlcmd = "SELECT * FROM tbl_mod2rd WHERE 1";

    $stmt = mysqli_prepare($db_conn, $sqlcmd);

    if (!$stmt) {
        die('Error...');
    }

    mysqli_stmt_execute($stmt);
    $sqlres = mysqli_stmt_get_result($stmt);

    $data = array();
    while ($row = mysqli_fetch_assoc($sqlres)) {
        $data[] = $row;
    }

    mysqli_free_result($sqlres);
    mysqli_stmt_close($stmt);
    mysqli_close($db_conn);

    return $data;
}

function data_modrdconf()
{
    // $params = array();
    // $file_serialsetting = __DIR__ . DS . 'config.csv';
    // if (file_exists($file_serialsetting)) {
    //     $json_serialparams = file_get_contents($file_serialsetting);
    //     $params = json_decode($json_serialparams, true);
    // }

    // return $params;

    // Specify the path to your CSV file
    $csvFilePath = __DIR__ . DS . 'config.csv';

    // Initialize an empty array to store the CSV data
    $csvData = [];

    // Open the CSV file for reading
    if (($handle = fopen($csvFilePath, 'r')) !== false) {
        // Loop through each row in the CSV file
        while (($data = fgetcsv($handle)) !== false) {
            $csvData[] = $data; // Add the row to the CSV data array
        }

        // Close the CSV file
        fclose($handle);
    }

    // Now $csvData contains the CSV data as an array
    return $csvData;
}
