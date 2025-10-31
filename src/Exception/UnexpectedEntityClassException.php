<?php

namespace UserAgreementBundle\Exception;

class UnexpectedEntityClassException extends \InvalidArgumentException
{
    public function __construct(string $entityClass, ?\Throwable $previous = null)
    {
        parent::__construct("Unexpected entity class: {$entityClass}", 0, $previous);
    }
}
