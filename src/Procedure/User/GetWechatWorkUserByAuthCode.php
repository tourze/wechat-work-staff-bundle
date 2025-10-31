<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Procedure\User;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\AccessTokenContracts\TokenServiceInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\WechatWorkContracts\AgentInterface;
use Tourze\WechatWorkContracts\CorpInterface;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;
use WechatWorkStaffBundle\Request\User\GetUserRequest;
use WechatWorkStaffBundle\Service\BizUserService;

#[Autoconfigure(public: true)]
#[MethodTag(name: '企业微信')]
#[Log]
#[MethodExpose(method: 'GetWechatWorkUserByAuthCode')]
#[MethodDoc(summary: '根据企业微信返回的Code来获取用户信息')]
#[WithMonologChannel(channel: 'procedure')]
class GetWechatWorkUserByAuthCode extends BaseProcedure
{
    #[MethodParam(description: '企业ID')]
    public string $corpId;

    #[MethodParam(description: '应用ID')]
    public string $agentId;

    #[MethodParam(description: '授权Code')]
    public string $code;

    public function __construct(
        private readonly ?CorpRepository $corpRepository = null,
        private readonly ?AgentRepository $agentRepository = null,
        private readonly ?UserLoaderInterface $userLoader = null,
        private readonly ?BizUserService $bizUserService = null,
        private readonly ?TokenServiceInterface $accessTokenService = null,
        private readonly ?WorkServiceInterface $workService = null,
        private readonly ?LoggerInterface $logger = null,
        private readonly ?EntityManagerInterface $entityManager = null,
    ) {
    }

    public function execute(): array
    {
        return $this->getResult($this->corpId, $this->agentId, $this->code);
    }

    /**
     * @return array<string, mixed>
     */
    public function getResult(?string $corpId, ?string $agentId, ?string $code): array
    {
        $agent = $this->findAgent($corpId, $agentId);
        $userInfo = $this->getUserInfoByCode($agent, $code);
        $userDetail = $this->getUserDetail($agent, $userInfo);
        $sensitiveData = $this->getSensitiveUserData($agent, $userInfo);

        $result = array_merge($userDetail, $sensitiveData);
        $corp = $agent->getCorp();
        if (null === $corp) {
            throw new NotFoundHttpException('找不到指定企业');
        }
        $workUser = $this->saveWorkUser($corp, $agent, $result);

        if ($workUser instanceof User && null !== $this->bizUserService && null !== $this->accessTokenService) {
            $bizUser = $this->bizUserService->transformFromWorkUser($workUser);
            $result['jwt'] = $this->accessTokenService->createToken($bizUser);
        }

        return $result;
    }

    private function findAgent(?string $corpId, ?string $agentId): AgentInterface
    {
        if (null === $this->corpRepository || null === $this->agentRepository) {
            throw new NotFoundHttpException('服务不可用');
        }

        $corp = $this->corpRepository->findOneBy(['corpId' => $corpId]);
        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);

        if (null === $agent) {
            throw new NotFoundHttpException('找不到指定应用');
        }

        return $agent;
    }

    /**
     * @return array{userid: string, user_ticket?: string}
     */
    private function getUserInfoByCode(AgentInterface $agent, ?string $code): array
    {
        if (null === $this->workService) {
            throw new NotFoundHttpException('服务不可用');
        }

        $request = new GetUserInfoByCodeRequest();
        $request->setAgent($agent);
        $request->setCode($code ?? '');

        $response = $this->workService->request($request);
        assert(is_array($response));

        if (null !== $this->logger) {
            $this->logger->info('企业微信获取访问用户身份返回', $response);
        }

        if (!isset($response['userid'])) {
            throw new BadRequestException('找不到userId');
        }

        /** @var array{userid: string, user_ticket?: string} $response */
        return $response;
    }

    /**
     * @param array<string, mixed> $userInfo
     * @return array<string, mixed>
     */
    private function getUserDetail(AgentInterface $agent, array $userInfo): array
    {
        if (null === $this->workService) {
            throw new NotFoundHttpException('服务不可用');
        }

        $userId = $userInfo['userid'];
        assert(is_string($userId));

        $request = new GetUserRequest();
        $request->setAgent($agent);
        $request->setUserId($userId);

        $response = $this->workService->request($request);
        assert(is_array($response));

        if (null !== $this->logger) {
            $this->logger->info('企业微信读取成员返回', $response);
        }

        /** @var array<string, mixed> $response */
        return $response;
    }

    /**
     * @param array<string, mixed> $userInfo
     * @return array<string, mixed>
     */
    private function getSensitiveUserData(AgentInterface $agent, array $userInfo): array
    {
        if (!isset($userInfo['user_ticket'])) {
            return [];
        }

        if (null === $this->workService) {
            return [];
        }

        $userTicket = $userInfo['user_ticket'];
        assert(is_string($userTicket));

        $request = new GetUserDetailByTicketRequest();
        $request->setAgent($agent);
        $request->setUserTicket($userTicket);

        $response = $this->workService->request($request);
        assert(is_array($response));

        if (null !== $this->logger) {
            $this->logger->info('企业微信获获取访问用户敏感信息返回', $response);
        }

        /** @var array<string, mixed> $response */
        return $this->filterSensitiveData($response);
    }

    /**
     * @param array<string, mixed> $sensitiveData
     * @return array<string, mixed>
     */
    private function filterSensitiveData(array $sensitiveData): array
    {
        $filtered = [];

        foreach (['avatar', 'mobile', 'email'] as $field) {
            if (isset($sensitiveData[$field]) && is_string($sensitiveData[$field]) && '' !== $sensitiveData[$field]) {
                $filtered[$field] = $sensitiveData[$field];
            }
        }

        return $filtered;
    }

    /**
     * @param array<string, mixed> $userData
     */
    private function saveWorkUser(CorpInterface $corp, AgentInterface $agent, array $userData): ?User
    {
        if (null === $this->userLoader || null === $this->entityManager) {
            return null;
        }

        $userId = $userData['userid'];
        assert(is_string($userId));

        $workUser = $this->userLoader->loadUserByUserIdAndCorp($userId, $corp);

        if (null === $workUser) {
            $workUser = new User();
            $workUser->setCorp($corp);
            $workUser->setUserId($userId);
            $workUser->setAgent($agent);
        }

        if (!$workUser instanceof User) {
            return null;
        }

        $this->updateWorkUserData($workUser, $userData);
        $this->entityManager->persist($workUser);
        $this->entityManager->flush();

        return $workUser;
    }

    /**
     * @param array<string, mixed> $userData
     */
    private function updateWorkUserData(User $workUser, array $userData): void
    {
        $name = $userData['name'];
        assert(is_string($name));
        $workUser->setName($name);

        if (isset($userData['avatar'])) {
            $avatar = $userData['avatar'];
            if (is_string($avatar)) {
                $workUser->setAvatarUrl($avatar);
            }
        }

        if (isset($userData['email'])) {
            $email = $userData['email'];
            if (is_string($email)) {
                $workUser->setEmail($email);
            }
        }

        if (isset($userData['mobile'])) {
            $mobile = $userData['mobile'];
            if (is_string($mobile)) {
                $workUser->setMobile($mobile);
            }
        }
    }
}
