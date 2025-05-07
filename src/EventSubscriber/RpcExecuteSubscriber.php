<?php

namespace UserAgreementBundle\EventSubscriber;

use AppBundle\Entity\BizUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Tourze\JsonRPC\Core\Event\BeforeMethodApplyEvent;
use UserAgreementBundle\Attribute\IsAgreeTerms;
use UserAgreementBundle\Exception\TermsNeedAgreeException;
use UserAgreementBundle\Service\ProtocolService;

/**
 * 协议版本，必须同意，增加注解来自动判断
 */
class RpcExecuteSubscriber
{
    public function __construct(
        private readonly Security $security,
        private readonly ProtocolService $protocolService,
    ) {
    }

    #[AsEventListener]
    public function beforeMethodApply(BeforeMethodApplyEvent $event): void
    {
        $attributes = (new \ReflectionClass($event->getMethod()))->getAttributes(IsAgreeTerms::class);
        if (empty($attributes)) {
            return;
        }

        // 一定要登录
        $user = $this->security->getUser();
        if (!$user) {
            throw new TermsNeedAgreeException();
        }
        if (!($user instanceof BizUser)) {
            return;
        }

        $attribute = $attributes[0]->newInstance();
        /** @var IsAgreeTerms $attribute */
        if (!$this->protocolService->checkAgree($user, $attribute->type)) {
            throw new TermsNeedAgreeException();
        }
    }
}
