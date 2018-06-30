<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk\Responses;

use DateTime;

class DeliveryStatusResponse extends Response
{
    /**
     * @return int
     */
    public function getResult(): int
    {
        return (int)$this->response['result'];
    }

    /**
     * @return string
     */
    public function getReferenceId(): string
    {
        return $this->response['reference_id'];
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->response['message'];
    }

    /**
     * @return string
     */
    public function getDeliveryStatus(): string
    {
        return $this->response['delivery_status'];
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryDateTime(): DateTime
    {
        return new DateTime($this->response['delivery_datetime']);
    }
}
