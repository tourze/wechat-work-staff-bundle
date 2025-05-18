<?php

namespace WechatWorkStaffBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

class BizUserService
{
    public function __construct(
        private readonly UserManagerInterface $userManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    /**
     * 转换企微外部联系人，为系统用户
     */
    public function transformFromExternalUser(ExternalContactInterface $user): UserInterface
    {
        $bizUser = $this->userManager->loadUserByIdentifier($user->getExternalUserId());
        if (!$bizUser) {
            $bizUser = $this->userManager->createUser(
                $user->getExternalUserId(),
                empty($user->getNickname()) ? ($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] ?? '企微外部联系人') : $user->getNickname(),
                $user->getAvatar(),
            );
        }

        return $bizUser;
    }

    /**
     * 转换企微的员工/接待，为系统用户
     */
    public function transformFromWorkUser(User $user): UserInterface
    {
        $bizUser = $this->userManager->loadUserByIdentifier($user->getUserId());
        if (!$bizUser) {
            $bizUser = $this->userManager->createUser(
                $user->getUserId(),
                empty($user->getName()) ? ($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] ?? '企业微信用户') : $user->getName(),
                $user->getAvatarUrl(),
            );
        }

        return $bizUser;
    }

    /**
     * 将系统用户转化为企微用户
     */
    public function transformToWorkUser(UserInterface $bizUser): ?User
    {
        return $this->userRepository->findOneBy(['userId' => $bizUser->getUserIdentifier()]);
    }
}
