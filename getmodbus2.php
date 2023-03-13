<?php

use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersResponse;
use ModbusTcpClient\Packet\ResponseFactory;
use ModbusTcpClient\Utils\Endian;

require __DIR__ . '/vendor/autoload.php';
//require __DIR__ . '/logger.php';

Endian::$defaultEndian = Endian::BIG_ENDIAN_LOW_WORD_FIRST; // set default (global) endian used for parsing data

$connection = BinaryStreamConnection::getBuilder()
    ->setPort(502)
    ->setHost('192.168.0.101')
    ->setConnectTimeoutSec(1.5) // timeout when establishing connection to the server
    ->setWriteTimeoutSec(0.5) // timeout when writing/sending packet to the server
    ->setReadTimeoutSec(0.3) // timeout when waiting response from server
    ->build();

$startAddress = 0;
$quantity = 2;
$unitID = 0;
$packet = new ReadHoldingRegistersRequest($startAddress, $quantity, $unitID); // NB: This is Modbus TCP packet not Modbus RTU over TCP!

try {
    $binaryData = $connection->connect()->sendAndReceive($packet);

    /**
     * @var $response ReadHoldingRegistersResponse
     */
    $response = ResponseFactory::parseResponseOrThrow($binaryData);

    $dataArray = $response->getData();
    $data[0] = $dataArray[0]*256 + $dataArray[1];
    $data[1] = $dataArray[2]*256 + $dataArray[3];
    print json_encode($data);
    
    // foreach ($response as $word) {
    //     print_r($word->getBytes());
    // }
    // foreach ($response->asDoubleWords() as $doubleWord) {
    //     print_r($doubleWord->getBytes());
    // }

    // set internal index to match start address to simplify array access
    // $responseWithStartAddress = $response->withStartAddress($startAddress);
    // print json_encode($responseWithStartAddress[$startAddress]->getBytes()); // use array access to get word
    // print_r($responseWithStartAddress->getDoubleWordAt($startAddress)->getFloat());

} catch (Exception $exception) {
    echo 'An exception occurred' . PHP_EOL;
    echo $exception->getMessage() . PHP_EOL;
    echo $exception->getTraceAsString() . PHP_EOL;
} finally {
    $connection->close();
}
