<?php

namespace AliOSS\Tests;


use AliOSS\Result\GetLoggingResult;
use AliOSS\Http\ResponseCore;
use AliOSS\Core\OssException;


class GetLoggingResultTest extends \PHPUnit_Framework_TestCase
{
    private $validXml = <<<BBBB
<?xml version="1.0" encoding="utf-8"?>
<BucketLoggingStatus>
<LoggingEnabled>
<TargetBucket>TargetBucket</TargetBucket>
<TargetPrefix>TargetPrefix</TargetPrefix>
</LoggingEnabled>
</BucketLoggingStatus>
BBBB;

    public function testParseValidXml()
    {
        $response = new ResponseCore(array(), $this->validXml, 200);
        $result = new GetLoggingResult($response);
        $this->assertTrue($result->isOK());
        $this->assertNotNull($result->getData());
        $this->assertNotNull($result->getRawResponse());
        $loggingConfig = $result->getData();
        $this->assertEquals($this->cleanXml($this->validXml), $this->cleanXml($loggingConfig->serializeToXml()));
        $this->assertEquals("TargetBucket", $loggingConfig->getTargetBucket());
        $this->assertEquals("TargetPrefix", $loggingConfig->getTargetPrefix());
    }

    private function cleanXml($xml)
    {
        return str_replace("\n", "", str_replace("\r", "", $xml));
    }

    public function testInvalidResponse()
    {
        $response = new ResponseCore(array(), $this->validXml, 300);
        try {
            new GetLoggingResult($response);
            $this->assertTrue(false);
        } catch (OssException $e) {

        }
    }
}
