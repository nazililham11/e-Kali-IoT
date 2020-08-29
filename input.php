
<?php

const DATA_FILE = 'data.json';
const LOG_VOLTAGE_FILE = "volt.json";
const LOG_WATER_LEVEL_FILE = "water.json";
const PARAMS    = ['alm', 'vol', 'wlv', 'grb', 'dam1', 'dam2', 'dam3'];
const VOLTAGE_LOG_LIMIT = 10;
const WATER_LEVEL_LOG_LIMIT = 10;

function main()
{
    $defaultData = defaultData();
    $newData = getParams();
    $oldData = readFromFile(DATA_FILE);
    $data = [];
    foreach (PARAMS as $val) {
        $data[$val] = isset($newData[$val]) ? $newData[$val] : (isset($oldData[$val]) ? $oldData[$val] : 0);
    }

    echo "<br>" . "Default Data" . "<br>";
    var_dump($defaultData);

    echo "<br>" . "Old Data" . "<br>";
    var_dump($oldData);
    echo "<br>" . "New Data" . "<br>";
    var_dump($newData);
    
    echo "<br>" . "Merged Data" . "<br>";
    var_dump($data);

    writeToFile($data, DATA_FILE);

    if (isset($newData['vol'])){
        writeLogToFile(LOG_VOLTAGE_FILE, $newData['vol'], VOLTAGE_LOG_LIMIT);
    }
    if (isset($newData['wlv'])){
        writeLogToFile(LOG_WATER_LEVEL_FILE, $newData['wlv'], WATER_LEVEL_LOG_LIMIT);
    }
}

function getParams(){
    foreach (PARAMS as $val) {
        if (isset($_GET[$val]))
            $data[$val] = (float)$_GET[$val];
    }
    return $data;
}

function writeLogToFile($file_name, $value, $limit){
    $data = readFromFile($file_name);
    if (!$data) {
        $data = [];
    }

    array_push($data, [
        'value' => $value, 
        'time' => date("Y-m-d H:i:s")
    ]);

    $size = sizeof($data);
    if ($size > $limit){
        $data = array_slice($data, $size - 10);
    }
    
    ($data);
    echo "<br>" . "Size" . "<br>";
    var_dump($size);
    writeToFile($data, $file_name);
}

function writeToFile($data, $file_name)
{
    if (!$data) return;
    if ($file = fopen($file_name, 'wb')) {
        fwrite($file, json_encode($data));
        fclose($file);
        echo "<br>" . "Write to file " . $file_name . " Completed";
    }
}

function readFromFile($file_name){
    if (!file_exists($file_name)) return;
    if ($string = file_get_contents($file_name)){
        if ($json_a = json_decode($string, true)){
            return $json_a;
        }
    }
}

function defaultData()
{
    foreach (PARAMS as $val) {
        $data[$val] = 0.0;
    }
    return $data;
}

main();
