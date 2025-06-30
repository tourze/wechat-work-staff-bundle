<?php

namespace WechatWorkStaffBundle\Procedure\User;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use WechatWorkStaffBundle\Repository\DepartmentRepository;

#[MethodTag(name: '企业微信')]
#[MethodDoc(summary: '获取企业微信组织结构树')]
#[MethodExpose(method: 'GetWechatWorkDepartmentTree')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetWechatWorkDepartmentTree extends BaseProcedure
{
    public function __construct(
        private readonly DepartmentRepository $departmentRepository,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function execute(): array
    {
        $tree = [];
        foreach ($this->departmentRepository->findBy(['parent' => null]) as $item) {
            $context = [
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
                DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
                'groups' => 'api_tree',
            ];

            $tmp = $this->normalizer->normalize($item, 'array', $context);
            $tree[] = $tmp;
        }

        return [
            'tree' => $tree,
        ];
    }

    //    public function getMockResult(): ?array
    //    {
    //        // TODO 生成
    //        return [
    //            'tree' => [
    //                $this->faker->title,
    //            ],
    //        ];
    //    }
}
