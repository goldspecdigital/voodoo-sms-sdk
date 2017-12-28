<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk;

use GoldSpecDigital\VoodooSmsSdk\Exceptions\ExternalReferenceTooLongException;
use GoldSpecDigital\VoodooSmsSdk\Exceptions\MessageTooLongException;
use Guzzle\Http\Client as HttpClient;
use InvalidArgumentException;

class Client
{
    protected const URI = 'https://www.voodooSMS.com/vapi/server/';
    protected const MESSAGE_LIMIT = 160;
    protected const EXTERNAL_REFERENCE_LIMIT = 30;
    protected const COUNTRY_CODE = 44;
    protected const RESPONSE_FORMAT = 'JSON';

    /**
     * @var \Guzzle\Http\Client
     */
    protected $httpClient;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var array
     */
    protected $headers = ['Accept' => 'application/json'];

    /**
     * Client constructor.
     *
     * @param string $username
     * @param string $password
     * @param null|string $from
     */
    public function __construct(string $username, string $password, ?string $from = null)
    {
        $this->httpClient = new HttpClient(static::URI);
        $this->username = $username;
        $this->password = $password;
        $this->from = $from;
    }

    /**
     * Send an SMS.
     *
     * @param string $message
     * @param string $to
     * @param null|string $from
     * @param null|string $externalReference The external reference.
     * @return object
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\MessageTooLongException
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\ExternalReferenceTooLongException
     */
    public function send(string $message, string $to, ?string $from = null, ?string $externalReference = null): object
    {
        if (strlen($message) > static::MESSAGE_LIMIT) {
            throw new MessageTooLongException();
        }

        if ($from === null && $this->from === null) {
            throw new InvalidArgumentException('The from parameter must be set.');
        }

        if (is_string($externalReference) && strlen($externalReference) > static::EXTERNAL_REFERENCE_LIMIT) {
            throw new ExternalReferenceTooLongException();
        }

        $uri = 'sendSMS';
        $parameters = [
            // Required parameters.
            'dest' => $to,
            'orig' => $from ?? $this->from,
            'msg' => $message,
            'uid' => $this->username,
            'pass' => $this->password,
            'validity' => 1,
            'format' => static::RESPONSE_FORMAT,

            // Optional parameters.
            'cc' => static::COUNTRY_CODE,
            'eref' => $externalReference,
        ];
        $parameters = array_filter($parameters, [$this, 'isNotNull']);

        $request = $this->httpClient->post($uri, $this->headers, $parameters);
        $response = $this->httpClient->send($request);

        return json_decode((string)$response->getBody());
    }

    /**
     * @param string $referenceID
     * @return object
     * @throws \Exception
     */
    public function getDeliveryStatus(string $referenceID): object
    {
        $uri = 'getDlrStatus';
        $parameters = [
            'uid' => $this->username,
            'pass' => $this->password,
            'reference_id' => $referenceID,
        ];

        $request = $this->httpClient->post($uri, $this->headers, $parameters);
        $response = $this->httpClient->send($request);

        return json_decode((string)$response->getBody());
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isNotNull($value): bool
    {
        return $value !== null;
    }
}
