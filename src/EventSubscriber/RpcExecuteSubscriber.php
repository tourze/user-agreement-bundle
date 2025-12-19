<?php

namespace UserAgreementBundle\EventSubscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\JsonRPC\Core\Event\BeforeMethodApplyEvent;
use UserAgreementBundle\Attribute\IsAgreeTerms;
use UserAgreementBundle\Exception\TermsNeedAgreeException;
use UserAgreementBundle\Service\ProtocolService;

/**
 * 协议版本，必须同意，增加注解来自动判断
 */
final class RpcExecuteSubscriber
{
    public function __construct(
        private readonly Security $security,
        private readonly ProtocolService $protocolService,
    ) {
    }

    #[AsEventListener]
    public function beforeMethodApply(BeforeMethodApplyEvent $event): void
    {
        $classReflection = new \ReflectionClass($event->getMethod());
        $attributes = $classReflection->getAttributes(IsAgreeTerms::class);

        // Check class-level attributes first, then method-level
        if (0 === count($attributes)) {
            $methodReflection = new \ReflectionMethod($event->getMethod(), '__invoke');
            $attributes = $methodReflection->getAttributes(IsAgreeTerms::class);
        }
        if (0 === count($attributes)) {
            return;
        }

        // 一定要登录
        $user = $this->security->getUser();
        if (null === $user) {
            throw new TermsNeedAgreeException();
        }

        $attribute = $attributes[0]->newInstance();
        /** @var IsAgreeTerms $attribute */
        if (!$this->protocolService->checkAgree($user, $attribute->type)) {
            throw new TermsNeedAgreeException();
        }
    }
}
