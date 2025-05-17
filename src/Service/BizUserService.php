<?php

namespace WechatWorkStaffBundle\Service;

use AppBundle\Entity\BizUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\WechatWorkExternalContactModel\ExternalContactInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

class BizUserService
{
    public function __construct(
        private readonly UserLoaderInterface $userLoader,
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * 转换企微外部联系人，为系统用户
     */
    public function transformFromExternalUser(ExternalContactInterface $user): BizUser
    {
        $bizUser = $this->userLoader->loadUserByIdentifier($user->getExternalUserId());
        if (!$bizUser) {
            $bizUser = new BizUser();
            $bizUser->setUsername($user->getExternalUserId());
            $bizUser->setUnionid($user->getUnionid());
            if (empty($user->getNickname())) {
                $bizUser->setNickName($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] ?? '企微外部联系人');
            } else {
                $bizUser->setNickName($user->getNickname());
            }
        }

        if (!$bizUser->getUnionid() && $user->getUnionId()) {
            $bizUser->setUnionid($user->getUnionid());
        }

        if (!$bizUser->getNickName() && $user->getNickname()) {
            $bizUser->setNickName($user->getNickname());
        }

        if (!$bizUser->getAvatar() && $user->getAvatar()) {
            $bizUser->setAvatar($user->getAvatar());
        }

        $bizUser->setType('wechat-work-external-user');
        $bizUser->setValid(true);
        $this->entityManager->persist($bizUser);
        $this->entityManager->flush();

        return $bizUser;
    }

    /**
     * 转换企微的员工/接待，为系统用户
     */
    public function transformFromWorkUser(User $user): UserInterface
    {
        $bizUser = $this->userLoader->loadUserByIdentifier($user->getUserId());
        if (!$bizUser) {
            $bizUser = new BizUser();
            $bizUser->setUsername($user->getUserId());
            if (empty($user->getName())) {
                $bizUser->setNickName($_ENV['WECHAT_WORK_EXTERNAL_CONTRACT_DEFAULT_NICK_NAME'] ?? '企业微信用户');
            } else {
                $bizUser->setNickName($user->getName());
            }
        }

        if (!$bizUser->getNickName()) {
            $bizUser->setNickName($user->getName());
        }

        if (!$bizUser->getAvatar() && $user->getAvatarUrl()) {
            $bizUser->setAvatar($user->getAvatarUrl());
        }

        $bizUser->setType('wechat-work-user');
        $bizUser->setValid(true);
        $this->entityManager->persist($bizUser);
        $this->entityManager->flush();

        return $bizUser;
    }

    /**
     * 将系统用户转化为企微用户
     */
    public function transformToWorkUser(BizUser $bizUser): ?User
    {
        return $this->userRepository->findOneBy(['userId' => $bizUser->getUsername()]);
    }
}
