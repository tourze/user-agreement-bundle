# User Agreement Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)
[![License](https://img.shields.io/packagist/l/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/php-monorepo/CI.svg)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg)](https://codecov.io/gh/tourze/php-monorepo)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)

A Symfony bundle for managing user agreements, protocols, and consent records. 
This bundle provides comprehensive functionality for handling user terms of service, 
privacy policies, and user account deletion requests in compliance with data protection regulations.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Basic Configuration](#basic-configuration)
  - [Database Setup](#database-setup)
  - [Admin Interface](#admin-interface)
- [Quick Start](#quick-start)
  - [1. Create Protocol Entities](#1-create-protocol-entities)
  - [2. Record User Agreement](#2-record-user-agreement)
  - [3. Handle Deletion Requests](#3-handle-deletion-requests)
- [Advanced Usage](#advanced-usage)
  - [Custom Protocol Validation](#custom-protocol-validation)
  - [Event-Driven Architecture](#event-driven-architecture)
  - [Service Integration](#service-integration)
- [Protocol Types](#protocol-types)
- [Revocation Types](#revocation-types)
- [JSON-RPC API](#json-rpc-api)
- [Admin Interface](#admin-interface)
- [Compliance Features](#compliance-features)
- [Requirements](#requirements)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Protocol Management**: Create and manage different types of user agreements 
  (registration, usage, privacy, marketing)
- **Consent Tracking**: Record and track user agreement to protocols with IP tracking
- **Account Deletion**: Handle user account deletion requests with different revocation types
- **Admin Interface**: EasyAdmin integration for backend management
- **JSON-RPC API**: Programmatic access to agreement functionality
- **GDPR Compliance**: Built-in support for data protection requirements

## Installation

```bash
composer require tourze/user-agreement-bundle
```

Add the bundle to your `config/bundles.php`:

```php
return [
    // ...
    UserAgreementBundle\UserAgreementBundle::class => ['all' => true],
];
```

## Configuration

### Basic Configuration

Create `config/packages/user_agreement.yaml`:

```yaml
user_agreement:
    enabled: true
    ip_tracking: true
    retention_period: 365  # days
```

### Database Setup

Run the migrations to create required database tables:

```bash
php bin/console doctrine:migrations:migrate
```

### Admin Interface

Enable EasyAdmin integration in `config/packages/easy_admin.yaml`:

```yaml
easy_admin:
    entities:
        ProtocolEntity:
            class: UserAgreementBundle\Entity\ProtocolEntity
        AgreeLog:
            class: UserAgreementBundle\Entity\AgreeLog
        RevokeRequest:
            class: UserAgreementBundle\Entity\RevokeRequest
```

## Quick Start

### 1. Create Protocol Entities

```php
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;

$protocol = new ProtocolEntity();
$protocol->setType(ProtocolType::PRIVACY);
$protocol->setTitle('Privacy Policy');
$protocol->setVersion('1.0');
$protocol->setContent('Your privacy policy content...');
$protocol->setRequired(true);
$protocol->setEffectiveTime(new \DateTimeImmutable());

$entityManager->persist($protocol);
$entityManager->flush();
```

### 2. Record User Agreement

```php
use UserAgreementBundle\Entity\AgreeLog;

$agreeLog = new AgreeLog();
$agreeLog->setMemberId($user->getId());
$agreeLog->setProtocolId($protocol->getId());
$agreeLog->setValid(true);

$entityManager->persist($agreeLog);
$entityManager->flush();
```

### 3. Handle Deletion Requests

```php
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

$revokeRequest = new RevokeRequest();
$revokeRequest->setUser($user);
$revokeRequest->setType(RevokeType::All);
$revokeRequest->setRemark('User requested account deletion');

$entityManager->persist($revokeRequest);
$entityManager->flush();
```

## Advanced Usage

### Custom Protocol Validation

```php
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Callback]
public function validateProtocol(ExecutionContextInterface $context): void
{
    if ($this->type === ProtocolType::PRIVACY && empty($this->content)) {
        $context->buildViolation('Privacy policy content is required')
            ->atPath('content')
            ->addViolation();
    }
}
```

### Event-Driven Architecture

```php
use UserAgreementBundle\Event\AgreeProtocolEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProtocolSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AgreeProtocolEvent::class => 'onProtocolAgreed',
        ];
    }

    public function onProtocolAgreed(AgreeProtocolEvent $event): void
    {
        // Custom logic when user agrees to protocol
        $protocol = $event->getProtocol();
        $user = $event->getUser();
        
        // Send confirmation email, log activity, etc.
    }
}
```

### Service Integration

```php
use UserAgreementBundle\Service\MemberService;

class MyService
{
    public function __construct(
        private MemberService $memberService,
    ) {}
    
    public function processUser(UserInterface $user): void
    {
        $memberId = $this->memberService->extractMemberId($user);
        // Process user agreement logic
    }
}
```

## Protocol Types

- `MEMBER_REGISTER`: User registration agreement
- `MEMBER_USAGE`: User usage terms
- `PRIVACY`: Privacy policy
- `SALE_PUSH`: Marketing communications consent

## Revocation Types

- `All`: Complete account deletion
- `NO_NOTIFY`: Keep profile but opt out of notifications
- `NOTIFY`: Keep profile and allow notifications

## JSON-RPC API

The bundle provides JSON-RPC procedures for external integration:

- `ApiAgreeSystemProtocol`: Record user agreement to protocols
- `ApiGetSystemProtocolContent`: Retrieve protocol content

## Admin Interface

Access the admin interface through EasyAdmin:

- Protocol management: `/admin/protocol`
- Agreement logs: `/admin/agree-log`
- Deletion requests: `/admin/revoke-request`

## Compliance Features

- **IP Tracking**: Automatically records IP addresses for audit trails
- **Version Control**: Protocol versioning for regulatory compliance
- **Consent Records**: Immutable logs of user agreements
- **Data Retention**: Proper handling of user data deletion requests

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
