<?php

declare(strict_types=1);

namespace UserAgreementBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use UserAgreementBundle\UserAgreementBundle;

/**
 * @internal
 */
#[CoversClass(UserAgreementBundle::class)]
#[RunTestsInSeparateProcesses]
final class UserAgreementBundleTest extends AbstractBundleTestCase
{
}
