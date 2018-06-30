<?php

declare(strict_types=1);

namespace GoldSpecDigital\VoodooSmsSdk\Responses;

class SendSmsResponse extends Response
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
    public function getResultText(): string
    {
        return $this->response['resultText'];
    }

    /**
     * @return array
     */
    public function getReferenceId(): array
    {
        return $this->response['reference_id'];
    }
}
