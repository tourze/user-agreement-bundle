<?php

namespace UserAgreementBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class UserAgreementBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle::class => ['all' => true],
        ];
    }
}
