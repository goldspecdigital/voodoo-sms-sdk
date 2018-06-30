<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk\Tests;

use GoldSpecDigital\VoodooSmsSdk\Client;

class DummyTest extends TestCase
{
    /**
     * @return void
     */
    public function testClientIsInstantiable(): void
    {
        $client = $this->createClient();

        $this->assertInstanceOf(Client::class, $client);
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\MessageTooLongException
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\ExternalReferenceTooLongException
     */
    public function testSendWorksWithExternalReference(): void
    {
        $client = $this->createClient();
        $response = $client->send('This is a test message', getenv('VOODOO_TO'), 'PHPUnitTest', 'testing');

        $this->assertEquals(200, $response->getResult());
    }

    /**
     * @return void
     * @throws \Exception
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\MessageTooLongException
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\ExternalReferenceTooLongException
     */
    public function testSendWorksWithoutExternalReference(): void
    {
        $client = $this->createClient();
        $response = $client->send('This is a test message', getenv('VOODOO_TO'), 'PHPUnitTest');

        $this->assertEquals(200, $response->getResult());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSendWorksWithFromSetInConstructor(): void
    {
        $client = $this->createClient('PHPUnitTest');
        $response = $client->send('This is a test message', getenv('VOODOO_TO'));

        $this->assertEquals(200, $response->getResult());
    }

    /**
     * @throws \Exception
     */
    public function testGetDeliveryStatusWorks(): void
    {
        $client = $this->createClient('PHPUnitTest');
        $response = $client->send('This is a test message', getenv('VOODOO_TO'));
        $deliveryStatus = $client->getDeliveryStatus($response->getReferenceId()[0]);

        $this->assertEquals(200, $deliveryStatus->getResult());
        $this->assertEquals($response->getReferenceId()[0], $deliveryStatus->getReferenceId());
        $this->assertEquals('This is a test message', $deliveryStatus->getMessage());
    }

    /**
     * @param null|string $from
     * @return \GoldSpecDigital\VoodooSmsSdk\Client
     */
    protected function createClient(?string $from = null): Client
    {
        return new Client(getenv('VOODOO_USERNAME'), getenv('VOODOO_PASSWORD'), $from);
    }
}
