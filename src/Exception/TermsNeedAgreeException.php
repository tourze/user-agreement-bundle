<?php

namespace UserAgreementBundle\Exception;

class TermsNeedAgreeException extends \RuntimeException
{
    /** @var array<string, mixed> */
    private array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $message = '', array $data = [], ?\Throwable $previous = null)
    {
        $this->data = $data;
        parent::__construct($message, -988, $previous);
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
