<?php

namespace WechatWorkStaffBundle\Tests\Procedure\User;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use WechatWorkStaffBundle\Entity\Department;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkDepartmentTree;
use WechatWorkStaffBundle\Repository\DepartmentRepository;

class GetWechatWorkDepartmentTreeTest extends TestCase
{
    /**
     * 测试构造函数正确设置依赖项
     */
    public function testConstructor(): void
    {
        /** @var DepartmentRepository&MockObject $departmentRepository */
        $departmentRepository = $this->createMock(DepartmentRepository::class);
        /** @var NormalizerInterface&MockObject $normalizer */
        $normalizer = $this->createMock(NormalizerInterface::class);

        $procedure = new GetWechatWorkDepartmentTree($departmentRepository, $normalizer);

        $this->assertInstanceOf(GetWechatWorkDepartmentTree::class, $procedure);
    }

    /**
     * 测试execute方法返回空树结构
     */
    public function testExecuteWithEmptyDepartments(): void
    {
        /** @var DepartmentRepository&MockObject $departmentRepository */
        $departmentRepository = $this->createMock(DepartmentRepository::class);
        /** @var NormalizerInterface&MockObject $normalizer */
        $normalizer = $this->createMock(NormalizerInterface::class);

        $departmentRepository->expects($this->once())
            ->method('findBy')
            ->with(['parent' => null])
            ->willReturn([]);

        $procedure = new GetWechatWorkDepartmentTree($departmentRepository, $normalizer);
        $result = $procedure->execute();

        $this->assertEquals(['tree' => []], $result);
    }

    /**
     * 测试execute方法返回部门树结构
     */
    public function testExecuteWithDepartments(): void
    {
        /** @var DepartmentRepository&MockObject $departmentRepository */
        $departmentRepository = $this->createMock(DepartmentRepository::class);
        /** @var NormalizerInterface&MockObject $normalizer */
        $normalizer = $this->createMock(NormalizerInterface::class);

        $department1 = new Department();
        $department2 = new Department();
        $departments = [$department1, $department2];

        $normalizedData1 = ['id' => 1, 'name' => '技术部'];
        $normalizedData2 = ['id' => 2, 'name' => '市场部'];

        $departmentRepository->expects($this->once())
            ->method('findBy')
            ->with(['parent' => null])
            ->willReturn($departments);

        // 验证normalizer被正确调用并使用正确的上下文
        $expectedContext = [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s',
            'groups' => 'api_tree',
        ];

        $normalizer->expects($this->exactly(2))
            ->method('normalize')
            ->willReturnMap([
                [$department1, 'array', $expectedContext, $normalizedData1],
                [$department2, 'array', $expectedContext, $normalizedData2],
            ]);

        $procedure = new GetWechatWorkDepartmentTree($departmentRepository, $normalizer);
        $result = $procedure->execute();

        $this->assertEquals([
            'tree' => [$normalizedData1, $normalizedData2]
        ], $result);
    }

    /**
     * 测试normalize方法使用正确的上下文参数
     */
    public function testNormalizerContextConfiguration(): void
    {
        /** @var DepartmentRepository&MockObject $departmentRepository */
        $departmentRepository = $this->createMock(DepartmentRepository::class);
        /** @var NormalizerInterface&MockObject $normalizer */
        $normalizer = $this->createMock(NormalizerInterface::class);

        $department = new Department();
        $departments = [$department];

        $departmentRepository->expects($this->once())
            ->method('findBy')
            ->with(['parent' => null])
            ->willReturn($departments);

        // 验证上下文参数是否正确
        $normalizer->expects($this->once())
            ->method('normalize')
            ->with(
                $this->identicalTo($department),
                'array',
                $this->callback(function ($context) {
                    return $context[AbstractObjectNormalizer::ENABLE_MAX_DEPTH] === true
                        && $context[DateTimeNormalizer::FORMAT_KEY] === 'Y-m-d H:i:s'
                        && $context['groups'] === 'api_tree';
                })
            )
            ->willReturn(['normalized_data']);

        $procedure = new GetWechatWorkDepartmentTree($departmentRepository, $normalizer);
        $procedure->execute();
    }
} 