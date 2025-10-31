# Wechat Work Staff Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-work-staff-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-staff-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?branch=master&style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg?style=flat-square)](https://php.net/)
[![Symfony](https://img.shields.io/badge/symfony-%3E%3D6.4-green.svg?style=flat-square)](https://symfony.com/)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-work-staff-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-work-staff-bundle)

A Symfony Bundle for enterprise WeChat (DingTalk) staff management, providing
comprehensive APIs for user, department, and tag management.

## Features

- **User Management**: Create, update, delete, and sync enterprise WeChat users
- **Department Management**: Handle department structure and hierarchies
- **Tag Management**: Create and manage user tags for organization
- **Avatar Management**: Automatic user avatar synchronization and storage
- **Authentication**: OAuth integration with enterprise WeChat
- **Cron Jobs**: Automated data synchronization tasks
- **JSON-RPC API**: RESTful API endpoints for external integration

## Installation

```bash
composer require tourze/wechat-work-staff-bundle
```

## Quick Start

### Basic Configuration

```php
<?php
// config/packages/wechat_work_staff.yaml
wechat_work_staff:
    # Configuration will be loaded from other bundles
```

### User Management

```php
<?php

use WechatWorkStaffBundle\Service\BizUserService;
use WechatWorkStaffBundle\Repository\UserRepository;

// Inject the service
public function __construct(
    private BizUserService $userService,
    private UserRepository $userRepository
) {}

// Get user by ID
$user = $this->userRepository->findByUserId('user123');

// Sync user data from WeChat Work
$this->userService->syncUserData($agentId);
```

## Console Commands

The bundle provides several console commands for data synchronization:

### User Management Commands

```bash
# Sync user list for specific agent
php bin/console wechat-work:sync-user-list <agentId>

# Check and update user avatars (runs automatically every 8 hours)
php bin/console wechat-work:check-user-avatar
```

## Tag Management Commands

```bash
# Sync user tags (runs automatically every 8 hours)
php bin/console wechat-work:sync-user-tags

# Sync users associated with tags (runs automatically every 20 minutes)
php bin/console wechat-work:sync-tag-users
```

## API Endpoints

The bundle provides several controller endpoints:

- **Authentication**: `/wechat-work/auth/*` - OAuth flow handling
- **User Connection**: `/wechat-work/connect/*` - User account linking
- **Testing Endpoints**: Various test endpoints for development

## JSON-RPC Procedures

Available JSON-RPC procedures:

- `GetWechatWorkDepartmentTree` - Retrieve department hierarchy
- `GetWechatWorkUserByAuthCode` - Get user information by auth code

## Entities

- **User**: Enterprise WeChat user entity
- **Department**: Department/organization structure
- **UserTag**: User categorization tags
- **AgentUser**: Association between agents and users

## Advanced Usage

### Cron Jobs

Automated tasks (configured via `AsCronTask` attribute):

- **Avatar Check**: Every 8 hours at minute 14
- **Tag Sync**: Every 8 hours at minute 30  
- **Tag Users Sync**: Every 20 minutes

### Custom Event Listeners

The bundle dispatches events that you can listen to:

```php
<?php

use WechatWorkStaffBundle\EventSubscriber\UserTagListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomUserTagListener implements EventSubscriberInterface
{
    // Your custom logic
}
```

## Dependencies

This bundle requires:

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM
- Bundle Dependency Bundle
- HTTP Client Bundle
- WeChat Work Contracts Bundle
- League Flysystem (for avatar storage)

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

### Security Considerations

- All external API calls include audit logging
- User data is properly validated and sanitized
- OAuth flows follow security best practices
- Sensitive information is properly encrypted

### Reporting Security Issues

Please report security vulnerabilities to [security@example.com](mailto:security@example.com).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
