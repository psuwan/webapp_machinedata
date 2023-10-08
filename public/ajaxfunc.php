<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Bangkok');

const DS = DIRECTORY_SEPARATOR;

$dir_root = realpath(dirname(dirname(__FILE__)));
$dir_app  = $dir_root . DS . 'app';

defined('APPLICATION_PATH') || define('APPLICATION_PATH', $dir_app);

require_once APPLICATION_PATH . DS . 'config' . DS . 'config.php';

$process_name = filter_input(INPUT_POST, 'process_name');

if (!empty($process_name)) {
    switch ($process_name) {
        default:
            echo 'Invalid process name..!';
            break;

        case 'set_modaddr':
            $mod2rd_reference = filter_input(INPUT_POST, 'read_modref');
            $mod2rd_modbusid = filter_input(INPUT_POST, 'read_modbusid');
            $mod2rd_modfncode = filter_input(INPUT_POST, 'read_funccode');
            $mod2rd_modaddr = filter_input(INPUT_POST, 'read_modaddr');
            $mod2rd_numregis = filter_input(INPUT_POST, 'read_numregis');
            $mod2rd_addrname = filter_input(INPUT_POST, 'read_addrname');

            if ($mod2rd_modaddr != '0')
                if (empty($mod2rd_modaddr) || empty($mod2rd_addrname)) {
                    echo '<script>alert(\'Data enter error [empty value]\'</script>';
                    echo '<script>window.location.href=\'?page=setting&action=setdata\'</script>';
                    break;
                }


            if (db_cntrowby1ref('tbl_mod2rd', 'mod2rd_addr', $mod2rd_modaddr, 2) > 0) {
                echo 0;
                break;
            }

            $mod2rd_tblname = filter_input(INPUT_POST, 'table_readmod');

            if (db_cntrowby1ref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2) === 0) {
                db_insertref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_id', $mod2rd_modbusid, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_func', $mod2rd_modfncode, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_addr', $mod2rd_modaddr, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_numreg', $mod2rd_numregis, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_name', $mod2rd_addrname, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_datetime', date('Y-m-d H:i:s'), 2);
            } else {
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_id', $mod2rd_modbusid, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_func', $mod2rd_modfncode, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_addr', $mod2rd_modaddr, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_numreg', $mod2rd_numregis, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_name', $mod2rd_addrname, 2);
                db_updaterowbyref($mod2rd_tblname, 'mod2rd_reference', $mod2rd_reference, 2, 'mod2rd_datetime', date('Y-m-d H:i:s'), 2);
            }

            echo 'process \'set_modaddr\' ok';
            break;

        case 'chkdup_modaddr':
            $mod2rd_modaddr = filter_input(INPUT_POST, 'read_modaddr');
            $mod2rd_tblname = filter_input(INPUT_POST, 'table_readmod');
            echo db_cntrowby1ref($mod2rd_tblname, 'mod2rd_addr', $mod2rd_modaddr, 2);
            break;

        case 'chkdup_modname':
            $mod2rd_modname = filter_input(INPUT_POST, 'read_modname');
            $mod2rd_tblname = filter_input(INPUT_POST, 'table_readmod');
            echo db_cntrowby1ref($mod2rd_tblname, 'mod2rd_name', $mod2rd_modname, 2);
            break;

        case 'set_comm':
            $data = $_POST;

            unset($data["process_name"]);
            unset($data["serport_flow"]);

            // Create the serial port configuration file
            $file_name = $config['PATH_TO_DATA'] . "serialport_config.json";
            $file_contents = json_encode($data);

            // Write the file contents
            file_put_contents($file_name, $file_contents);

            // Success message
            echo "Serial port configuration file created successfully.";

            break;

        case 'genfileconf':
            // Get the JSON data
            require_once($config['PATH_TO_DATA'] . 'data_setting.php');

            $file_name = $config['PATH_TO_DATA'] . "serialport_config.json";

            // Specify the path to your JSON file
            $jsonFilePath = $file_name;

            // Read the JSON file contents
            $jsonData = file_get_contents($jsonFilePath);

            // Decode the JSON data into an associative array
            $dataArray = json_decode($jsonData, true);

            if ($dataArray === null) {
                // JSON decoding failed
                die("Error decoding JSON data.");
            }

            // Extract the serial setting values
            $serport_name = $dataArray['serport_name'];
            $serport_baud = $dataArray['serport_baud'];
            $serport_data = $dataArray['serport_data'];
            $serport_parity = $dataArray['serport_parity'];
            $serport_stop = $dataArray['serport_stop'];

            // Get data_modbus2rd() function result
            $data_modbus2rd = data_modbus2rd();

            // Create and open the CSV file for writing
            $csvFilePath = $config['PATH_TO_DATA'] . 'config.csv';
            $csvFile = fopen($csvFilePath, 'w');

            // Write data rows
            foreach ($data_modbus2rd as $data) {
                $csvRow = [
                    "$serport_name,$serport_baud,$serport_data,$serport_parity,$serport_stop",
                    $data['mod2rd_id'],
                    $data['mod2rd_func'],
                    $data['mod2rd_addr'],
                    $data['mod2rd_numreg'],
                ];

                // Manually format the CSV line without quotes
                $csvLine = implode(',', array_map(function ($value) {
                    return str_replace('"', '', $value);
                }, $csvRow));

                fwrite($csvFile, $csvLine . "\n");
            }

            // Close the CSV file
            fclose($csvFile);

            echo "CSV file 'config.csv' generated successfully.";
            break;
    }
}
