<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\UserServiceContracts\UserManagerInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Exception\UserManagerUnavailableException;
use WechatWorkStaffBundle\Repository\UserRepository;

#[Autoconfigure(public: true)]
class BizUserService
{
    public function __construct(
        private readonly ?UserManagerInterface $userManager = null,
        private readonly ?UserRepository $userRepository = null,
    ) {
    }

    /**
     * 转换企微外部联系人，为系统用户
     */
    public function transformFromExternalUser(ExternalContactInterface $user): UserInterface
    {
        if (null === $this->userManager) {
            throw new UserManagerUnavailableException();
        }

        $bizUser = $this->userManager->loadUserByIdentifier($user->getExternalUserId() ?? '');
        if (null === $bizUser) {
            $nickname = $user->getNickname();
            $nickName = (null === $nickname || '' === $nickname)
                ? ($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] ?? '企微外部联系人')
                : $nickname;
            \assert(\is_string($nickName));

            $bizUser = $this->userManager->createUser(
                $user->getExternalUserId() ?? '',
                $nickName,
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
        if (null === $this->userManager) {
            throw new UserManagerUnavailableException();
        }

        $bizUser = $this->userManager->loadUserByIdentifier($user->getUserId() ?? '');
        if (null === $bizUser) {
            $name = $user->getName();
            $nickName = ('' === $name)
                ? ($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] ?? '企业微信用户')
                : $name;
            \assert(\is_string($nickName));

            $bizUser = $this->userManager->createUser(
                $user->getUserId() ?? '',
                $nickName,
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
        if (null === $this->userRepository) {
            return null;
        }

        $result = $this->userRepository->findOneBy(['userId' => $bizUser->getUserIdentifier()]);

        return $result instanceof User ? $result : null;
    }
}
