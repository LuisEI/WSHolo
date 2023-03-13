<?php

use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterRequest;
use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterResponse;
use ModbusTcpClient\Packet\ResponseFactory;

require __DIR__ . '/vendor/autoload.php';

$connection = BinaryStreamConnection::getBuilder()
    ->setPort(502)
    ->setHost('192.168.0.101')
    ->build();

$startAddress = 1;
$value = $_REQUEST['value'];
$unitID = 0;
$packet = new WriteSingleRegisterRequest($startAddress, $value * 1000, $unitID); // NB: This is Modbus TCP packet not Modbus RTU over TCP!
// echo 'Packet to be sent (in hex): ' . $packet->toHex() . PHP_EOL;

try {
    $binaryData = $connection->connect()
        ->sendAndReceive($packet);
    // echo 'Binary received (in hex):   ' . unpack('H*', $binaryData)[1] . PHP_EOL;

    // /* @var $response WriteSingleRegisterResponse */
    // $response = ResponseFactory::parseResponseOrThrow($binaryData);
    // echo 'Parsed packet (in hex):     ' . $response->toHex() . PHP_EOL;
    // echo 'Register value parsed from packet:' . PHP_EOL;
    // print_r($response->getWord()->getInt16());
    echo '';

} catch (Exception $exception) {
    echo 'An exception occurred' . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;
    echo $exception->getTraceAsString() . PHP_EOL;
} finally {
    $connection->close();
}
