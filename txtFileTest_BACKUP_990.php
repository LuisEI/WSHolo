<?php

use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterRequest;
use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterResponse;
use ModbusTcpClient\Packet\ResponseFactory;

require __DIR__ . '/vendor/autoload.php';

$connection = BinaryStreamConnection::getBuilder()
    ->setPort(502)
<<<<<<< HEAD
    ->setHost('192.168.100.84')
=======
    ->setHost('192.168.0.232')
>>>>>>> cf6be769d6e436f0fb5d3c1f5a21c21a50b858d0
    ->build();

$startAddress = 1;
$value = $_REQUEST['value'];
$unitID = 0;
$packet = new WriteSingleRegisterRequest($startAddress, $value * 1000, $unitID); // NB: This is Modbus TCP packet not Modbus RTU over TCP!
// echo 'Packet to be sent (in hex): ' . $packet->toHex() . PHP_EOL;

try {
    $binaryData = $connection->connect()
        ->sendAndReceive($packet);
    echo '';

} catch (Exception $exception) {
    echo 'An exception occurred' . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;
    echo $exception->getTraceAsString() . PHP_EOL;
} finally {
    $connection->close();
}
