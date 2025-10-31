# 企业微信员工管理组件

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-staff-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-staff-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg?style=flat-square)](https://php.net/)
[![Symfony](https://img.shields.io/badge/symfony-%3E%3D6.4-green.svg?style=flat-square)](https://symfony.com/)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-staff-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-staff-bundle)

一个用于企业微信员工管理的 Symfony 组件，提供全面的用户、部门和标签管理 API。

## 功能特性

- **用户管理**：创建、更新、删除和同步企业微信用户
- **部门管理**：处理部门结构和层级关系
- **标签管理**：为组织管理创建和管理用户标签
- **头像管理**：自动用户头像同步和存储
- **身份验证**：与企业微信的 OAuth 集成
- **定时任务**：自动化数据同步任务
- **JSON-RPC API**：用于外部集成的 RESTful API 端点

## 安装

```bash
composer require tourze/wechat-work-staff-bundle
```

## 快速开始

### 基本配置

```php
<?php
// config/packages/wechat_work_staff.yaml
wechat_work_staff:
    # 配置将从其他组件加载
```

### 用户管理

```php
<?php

use WechatWorkStaffBundle\Service\BizUserService;
use WechatWorkStaffBundle\Repository\UserRepository;

// 注入服务
public function __construct(
    private BizUserService $userService,
    private UserRepository $userRepository
) {}

// 根据 ID 获取用户
$user = $this->userRepository->findByUserId('user123');

// 从企业微信同步用户数据
$this->userService->syncUserData($agentId);
```

## 控制台命令

组件提供了多个用于数据同步的控制台命令：

### 用户管理命令

```bash
# 同步指定代理的用户列表
php bin/console wechat-work:sync-user-list <agentId>

# 检查和更新用户头像（每8小时自动运行）
php bin/console wechat-work:check-user-avatar
```

## 标签管理命令

```bash
# 同步用户标签（每8小时自动运行）
php bin/console wechat-work:sync-user-tags

# 同步与标签关联的用户（每20分钟自动运行）
php bin/console wechat-work:sync-tag-users
```

## API 端点

组件提供了多个控制器端点：

- **身份验证**：`/wechat-work/auth/*` - OAuth 流程处理
- **用户连接**：`/wechat-work/connect/*` - 用户账户链接
- **测试端点**：用于开发的各种测试端点

## JSON-RPC 过程

可用的 JSON-RPC 过程：

- `GetWechatWorkDepartmentTree` - 检索部门层级结构
- `GetWechatWorkUserByAuthCode` - 根据授权码获取用户信息

## 实体

- **User**：企业微信用户实体
- **Department**：部门/组织结构
- **UserTag**：用户分类标签
- **AgentUser**：代理与用户之间的关联

## 高级用法

### 定时任务

自动化任务（通过 `AsCronTask` 属性配置）：

- **头像检查**：每8小时的第14分钟执行
- **标签同步**：每8小时的第30分钟执行
- **标签用户同步**：每20分钟执行

### 自定义事件监听器

组件会分发可以监听的事件：

```php
<?php

use WechatWorkStaffBundle\EventSubscriber\UserTagListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomUserTagListener implements EventSubscriberInterface
{
    // 您的自定义逻辑
}
```

## 依赖要求

此组件需要：

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM
- Bundle Dependency Bundle
- HTTP Client Bundle
- WeChat Work Contracts Bundle
- League Flysystem（用于头像存储）

## 贡献指南

请查看 [CONTRIBUTING.md](CONTRIBUTING.md) 了解详细信息。

## 安全性

### 安全考虑

- 所有外部 API 调用都包含审计日志记录
- 用户数据经过适当验证和清理
- OAuth 流程遵循安全最佳实践
- 敏感信息经过适当加密

### 报告安全问题

请将安全漏洞报告至 [security@example.com](mailto:security@example.com)。

## 许可证

MIT 许可证。请查看 [许可证文件](LICENSE) 获取更多信息。
