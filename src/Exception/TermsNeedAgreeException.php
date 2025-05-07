<?php

namespace UserAgreementBundle\Exception;

use Tourze\JsonRPC\Core\Exception\ApiException;

class TermsNeedAgreeException extends ApiException
{
    public function __construct($mixed = '', array $data = [], ?\Throwable $previous = null)
    {
        parent::__construct($mixed, -988, $data, $previous);
    }
}
