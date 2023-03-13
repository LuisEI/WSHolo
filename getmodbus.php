<?php

use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadCoilsRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadCoilsResponse;
use ModbusTcpClient\Packet\ResponseFactory;

require __DIR__ . '\vendor\autoload.php';

$connection = BinaryStreamConnection::getBuilder()
    ->setPort(502)
    ->setHost('192.168.0.101')
    ->build();

$startAddress = 0;
$quantity = 2;
$packet = new ReadCoilsRequest($startAddress, $quantity);
echo 'Packet to be sent (in hex): ' . $packet->toHex() . PHP_EOL;

try {
    $binaryData = $connection->connect()
        ->sendAndReceive($packet);
    echo 'Binary received (in hex):   ' . unpack('H*', $binaryData)[1] . PHP_EOL;

    /* @var $response ReadCoilsResponse */
    $response = ResponseFactory::parseResponseOrThrow($binaryData);
    echo 'Parsed packet (in hex):     ' . $response->toHex() . PHP_EOL;
    echo 'Data parsed from packet (bytes):' . PHP_EOL;
    print_r($response->getCoils());

    // set internal index to match start address to simplify array access
    $responseWithStartAddress = $response->withStartAddress($startAddress);
    print_r($responseWithStartAddress[0]); // coil value at 12288

} catch (Exception $exception) {
    echo 'An exception occurred' . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;
    echo $exception->getTraceAsString() . PHP_EOL;
} finally {
    $connection->close();
}
