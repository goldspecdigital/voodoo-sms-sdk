<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk\Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp(): void
    {
        (new Dotenv(dirname(__DIR__)))->load();
    }
}
