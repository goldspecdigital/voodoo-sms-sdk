<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk\Responses;

abstract class Response
{
    /**
     * @var array
     */
    protected $response;

    /**
     * SendSmsResponse constructor.
     *
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }
}
