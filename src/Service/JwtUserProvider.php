<?php

namespace WechatWorkStaffBundle\Service;

use App\Service\JwtUserProviderInterface;
use AppBundle\Entity\BizUser;
use Lcobucci\JWT\UnencryptedToken;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Repository\UserRepository;

/**
 * 企业微信小程序 JWT 用户提供者
 */
class JwtUserProvider implements JwtUserProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly BizUserService $bizUserService,
    ) {
    }

    /**
     * 从 JWT 令牌中获取业务用户
     */
    public function getUserFromToken(UnencryptedToken $token): ?BizUser
    {
        $claims = $token->claims();
        $uid = $claims->get('uid');
        $userId = $claims->get('user_id');
        $corpId = $claims->get('corp_id');

        if (!$uid || !$userId || !$corpId) {
            return null;
        }

        $workUser = $this->userRepository->findOneBy([
            'id' => $uid,
            'userId' => $userId,
            'corp' => $corpId,
        ]);

        if (!$workUser) {
            return null;
        }

        // 转换为 BizUser
        return $this->bizUserService->transformFromWorkUser($workUser);
    }

    /**
     * 获取此提供者支持的用户类型
     */
    public function supports(string $type): bool
    {
        return 'wechat_work' === $type;
    }

    /**
     * 为用户生成 JWT 声明
     *
     * @param object $user 子模块用户实体
     */
    public function createClaimsForUser(object $user): array
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('User must be an instance of WechatWorkBundle\Entity\User');
        }

        return [
            'uid' => $user->getId(),
            'type' => 'wechat_work',
            'user_id' => $user->getUserId(),
            'corp_id' => $user->getCorp()->getId(),
        ];
    }
}
