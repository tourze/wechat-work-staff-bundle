<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Exception;

class UserManagerUnavailableException extends \RuntimeException
{
    public function __construct(string $message = 'UserManager not available in test environment')
    {
        parent::__construct($message);
    }
}
