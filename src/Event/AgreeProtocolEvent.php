<?php

namespace UserAgreementBundle\Event;

use Tourze\UserEventBundle\Event\UserInteractionEvent;
use UserAgreementBundle\Entity\AgreeLog;
use UserAgreementBundle\Entity\ProtocolEntity;

class AgreeProtocolEvent extends UserInteractionEvent
{
    private ProtocolEntity $protocol;

    private AgreeLog $agreeLog;

    public function getProtocol(): ProtocolEntity
    {
        return $this->protocol;
    }

    public function setProtocol(ProtocolEntity $protocol): void
    {
        $this->protocol = $protocol;
    }

    public function getAgreeLog(): AgreeLog
    {
        return $this->agreeLog;
    }

    public function setAgreeLog(AgreeLog $agreeLog): void
    {
        $this->agreeLog = $agreeLog;
    }
}
