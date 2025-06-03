<?php

namespace WechatWorkStaffBundle\Procedure\User;

use AccessTokenBundle\Service\AccessTokenService;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\WechatWorkContracts\UserLoaderInterface;
use WechatWorkBundle\Repository\AgentRepository;
use WechatWorkBundle\Repository\CorpRepository;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\User;
use WechatWorkStaffBundle\Request\Auth\GetUserDetailByTicketRequest;
use WechatWorkStaffBundle\Request\Auth\GetUserInfoByCodeRequest;
use WechatWorkStaffBundle\Request\User\GetUserRequest;
use WechatWorkStaffBundle\Service\BizUserService;

#[MethodTag('企业微信')]
#[Log]
#[MethodExpose('GetWechatWorkUserByAuthCode')]
#[MethodDoc('根据企业微信返回的Code来获取用户信息')]
#[WithMonologChannel('procedure')]
class GetWechatWorkUserByAuthCode extends LockableProcedure
{
    #[MethodParam('企业ID')]
    public string $corpId;

    #[MethodParam('应用ID')]
    public string $agentId;

    #[MethodParam('授权Code')]
    public string $code;

    public function __construct(
        private readonly CorpRepository $corpRepository,
        private readonly AgentRepository $agentRepository,
        private readonly UserLoaderInterface $userLoader,
        private readonly BizUserService $bizUserService,
        private readonly AccessTokenService $accessTokenService,
        private readonly WorkService $workService,
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        return $this->getResult($this->corpId, $this->agentId, $this->code);
    }

    public function getResult($corpId, $agentId, $code): array
    {
        $corp = $this->corpRepository->findOneBy(['corpId' => $corpId]);
        $agent = $this->agentRepository->findOneBy([
            'corp' => $corp,
            'agentId' => $agentId,
        ]);
        if (!$agent) {
            throw new NotFoundHttpException('找不到指定应用');
        }

        $request = new GetUserInfoByCodeRequest();
        $request->setAgent($agent);
        $request->setCode($code);
        $getUserInfoByCodeResponse = $this->workService->request($request);
        $this->logger->info('企业微信获取访问用户身份返回', $getUserInfoByCodeResponse);
        if (!isset($getUserInfoByCodeResponse['userid'])) {
            throw new BadRequestException('找不到userId');
        }

        $request = new GetUserRequest();
        $request->setAgent($agent);
        $request->setUserId($getUserInfoByCodeResponse['userid']);
        $getUserResponse = $this->workService->request($request);
        $this->logger->info('企业微信读取成员返回', $getUserResponse);

        $result = $getUserResponse;

        // 直接获取敏感信息是不返回的，必须用user_ticket继续请求
        if (isset($getUserInfoByCodeResponse['user_ticket'])) {
            $request = new GetUserDetailByTicketRequest();
            $request->setAgent($agent);
            $request->setUserTicket($getUserInfoByCodeResponse['user_ticket']);
            $getUserDetailByTicketResponse = $this->workService->request($request);
            $this->logger->info('企业微信获获取访问用户敏感信息返回', $getUserDetailByTicketResponse);

            if (empty($result['avatar'])) {
                $result['avatar'] = $getUserDetailByTicketResponse['avatar'];
            }
            if (empty($result['mobile'])) {
                $result['mobile'] = $getUserDetailByTicketResponse['mobile'];
            }
            if (empty($result['email'])) {
                $result['email'] = $getUserDetailByTicketResponse['email'];
            }
        }

        // 保存企微用户信息
        $workUser = $this->userLoader->loadUserByUserIdAndCorp($result['userid'], $corp);
        if (!$workUser) {
            $workUser = new User();
            $workUser->setCorp($corp);
            $workUser->setUserId($result['userid']);
            $workUser->setAgent($agent);
        }

        $workUser->setName($result['name']);

        if (isset($result['avatar'])) {
            $workUser->setAvatarUrl($result['avatar']);
        }
        if (isset($result['email'])) {
            $workUser->setEmail($result['email']);
        }
        if (isset($result['mobile'])) {
            $workUser->setMobile($result['mobile']);
        }

        $this->entityManager->persist($workUser);
        $this->entityManager->flush();

        // 转换为系统用户，并生成JWT
        $bizUser = $this->bizUserService->transformFromWorkUser($workUser);
        $result['jwt'] = $this->accessTokenService->createToken($bizUser);

        return $result;
    }
}
