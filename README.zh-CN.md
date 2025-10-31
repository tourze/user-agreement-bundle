# 用户协议管理包

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version](https://img.shields.io/packagist/php-v/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)
[![License](https://img.shields.io/packagist/l/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)
[![Build Status](https://img.shields.io/github/workflow/status/tourze/php-monorepo/CI.svg)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg)](https://codecov.io/gh/tourze/php-monorepo)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/user-agreement-bundle.svg)](https://packagist.org/packages/tourze/user-agreement-bundle)

一个用于管理用户协议、条款和用户同意记录的 Symfony 包。
该包提供了全面的功能来处理用户服务条款、隐私政策和用户账户注销请求，符合数据保护法规要求。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [配置](#配置)
  - [基础配置](#基础配置)
  - [数据库设置](#数据库设置)
  - [管理界面配置](#管理界面配置)
- [快速开始](#快速开始)
  - [1. 创建协议实体](#1-创建协议实体)
  - [2. 记录用户同意](#2-记录用户同意)
  - [3. 处理注销请求](#3-处理注销请求)
- [高级用法](#高级用法)
  - [自定义协议验证](#自定义协议验证)
  - [事件驱动架构](#事件驱动架构)
  - [服务集成](#服务集成)
- [协议类型](#协议类型)
- [撤销类型](#撤销类型)
- [JSON-RPC API](#json-rpc-api)
- [管理界面](#管理界面)
- [合规功能](#合规功能)
- [系统要求](#系统要求)
- [贡献](#贡献)
- [License](#license)

## 功能特性

- **协议管理**：创建和管理不同类型的用户协议（注册、使用、隐私、营销）
- **同意追踪**：记录和追踪用户对协议的同意，带有 IP 地址追踪
- **账户注销**：处理用户账户注销请求，支持不同的撤销类型
- **管理界面**：EasyAdmin 集成的后台管理功能
- **JSON-RPC API**：程序化访问协议功能的接口
- **GDPR 合规**：内置支持数据保护要求

## 安装

```bash
composer require tourze/user-agreement-bundle
```

在 `config/bundles.php` 中添加包：

```php
return [
    // ...
    UserAgreementBundle\UserAgreementBundle::class => ['all' => true],
];
```

## 配置

### 基础配置

创建 `config/packages/user_agreement.yaml`：

```yaml
user_agreement:
    enabled: true
    ip_tracking: true
    retention_period: 365  # 天数
```

### 数据库设置

运行迁移来创建所需的数据库表：

```bash
php bin/console doctrine:migrations:migrate
```

### 管理界面配置

在 `config/packages/easy_admin.yaml` 中启用 EasyAdmin 集成：

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

## 快速开始

### 1. 创建协议实体

```php
use UserAgreementBundle\Entity\ProtocolEntity;
use UserAgreementBundle\Enum\ProtocolType;

$protocol = new ProtocolEntity();
$protocol->setType(ProtocolType::PRIVACY);
$protocol->setTitle('隐私政策');
$protocol->setVersion('1.0');
$protocol->setContent('您的隐私政策内容...');
$protocol->setRequired(true);
$protocol->setEffectiveTime(new \DateTimeImmutable());

$entityManager->persist($protocol);
$entityManager->flush();
```

### 2. 记录用户同意

```php
use UserAgreementBundle\Entity\AgreeLog;

$agreeLog = new AgreeLog();
$agreeLog->setMemberId($user->getId());
$agreeLog->setProtocolId($protocol->getId());
$agreeLog->setValid(true);

$entityManager->persist($agreeLog);
$entityManager->flush();
```

### 3. 处理注销请求

```php
use UserAgreementBundle\Entity\RevokeRequest;
use UserAgreementBundle\Enum\RevokeType;

$revokeRequest = new RevokeRequest();
$revokeRequest->setUser($user);
$revokeRequest->setType(RevokeType::All);
$revokeRequest->setRemark('用户请求注销账户');

$entityManager->persist($revokeRequest);
$entityManager->flush();
```

## 高级用法

### 自定义协议验证

```php
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Callback]
public function validateProtocol(ExecutionContextInterface $context): void
{
    if ($this->type === ProtocolType::PRIVACY && empty($this->content)) {
        $context->buildViolation('隐私政策内容是必需的')
            ->atPath('content')
            ->addViolation();
    }
}
```

### 事件驱动架构

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
        // 用户同意协议时的自定义逻辑
        $protocol = $event->getProtocol();
        $user = $event->getUser();
        
        // 发送确认邮件、记录活动等
    }
}
```

### 服务集成

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
        // 处理用户协议逻辑
    }
}
```

## 协议类型

- `MEMBER_REGISTER`：用户注册协议
- `MEMBER_USAGE`：用户使用条款
- `PRIVACY`：隐私政策
- `SALE_PUSH`：营销推送同意

## 撤销类型

- `All`：完全账户注销
- `NO_NOTIFY`：保留资料但退出通知
- `NOTIFY`：保留资料并允许通知

## JSON-RPC API

该包提供了用于外部集成的 JSON-RPC 过程：

- `ApiAgreeSystemProtocol`：记录用户对协议的同意
- `ApiGetSystemProtocolContent`：获取协议内容

## 管理界面

通过 EasyAdmin 访问管理界面：

- 协议管理：`/admin/protocol`
- 同意记录：`/admin/agree-log`
- 注销请求：`/admin/revoke-request`

## 合规功能

- **IP 追踪**：自动记录 IP 地址用于审计跟踪
- **版本控制**：协议版本管理用于监管合规
- **同意记录**：不可变的用户同意日志
- **数据保留**：正确处理用户数据注销请求

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM 3.0+

## 贡献

欢迎贡献！请随时提交 Pull Request。

## License

MIT 许可协议。详情参见 [LICENSE](LICENSE) 文件。
