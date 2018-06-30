<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk;

use GoldSpecDigital\VoodooSmsSdk\Exceptions\ExternalReferenceTooLongException;
use GoldSpecDigital\VoodooSmsSdk\Exceptions\MessageTooLongException;
use GoldSpecDigital\VoodooSmsSdk\Responses\DeliveryStatusResponse;
use GoldSpecDigital\VoodooSmsSdk\Responses\SendSmsResponse;
use GuzzleHttp\Client as HttpClient;
use InvalidArgumentException;

class Client
{
    protected const URI = 'https://www.voodooSMS.com/vapi/server/';
    protected const MESSAGE_LIMIT = 160;
    protected const EXTERNAL_REFERENCE_LIMIT = 30;
    protected const COUNTRY_CODE = 44;
    protected const RESPONSE_FORMAT = 'JSON';
    protected const HEADERS = ['Accept' => 'application/json'];

    /**
     * @var \GuzzleHttp\Client
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
     * Client constructor.
     *
     * @param string $username
     * @param string $password
     * @param null|string $from
     */
    public function __construct(string $username, string $password, string $from = null)
    {
        $this->httpClient = new HttpClient([
            'base_uri' => static::URI,
            'headers' => static::HEADERS,
        ]);
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
     * @return \GoldSpecDigital\VoodooSmsSdk\Responses\SendSmsResponse
     * @throws \InvalidArgumentException
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\MessageTooLongException
     * @throws \GoldSpecDigital\VoodooSmsSdk\Exceptions\ExternalReferenceTooLongException
     */
    public function send(string $message, string $to, string $from = null, string $externalReference = null): SendSmsResponse
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

        $response = $this->httpClient->post($uri, ['form_params' => $parameters]);
        $responseContents = json_decode((string)$response->getBody(), true);

        return new SendSmsResponse($responseContents);
    }

    /**
     * @param string $referenceID
     * @return \GoldSpecDigital\VoodooSmsSdk\Responses\DeliveryStatusResponse
     */
    public function getDeliveryStatus(string $referenceID): DeliveryStatusResponse
    {
        $uri = 'getDlrStatus';
        $parameters = [
            'uid' => $this->username,
            'pass' => $this->password,
            'reference_id' => $referenceID,
        ];

        $response = $this->httpClient->post($uri, ['form_params' => $parameters]);
        $responseContents = json_decode((string)$response->getBody(), true);

        return new DeliveryStatusResponse($responseContents);
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
