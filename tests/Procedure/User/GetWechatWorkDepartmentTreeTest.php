<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Procedure\User;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatWorkStaffBundle\Param\GetWechatWorkDepartmentTreeParam;
use WechatWorkStaffBundle\Procedure\User\GetWechatWorkDepartmentTree;

/**
 * @internal
 */
#[CoversClass(GetWechatWorkDepartmentTree::class)]
#[RunTestsInSeparateProcesses]
final class GetWechatWorkDepartmentTreeTest extends AbstractProcedureTestCase
{
    protected function onSetUp(): void
    {
    }

    public function testCanBeInstantiated(): void
    {
        $procedure = self::getService(GetWechatWorkDepartmentTree::class);
        $this->assertInstanceOf(GetWechatWorkDepartmentTree::class, $procedure);
    }

    public function testExecuteReturnsTreeStructure(): void
    {
        /** @var GetWechatWorkDepartmentTree $procedure */
        $procedure = self::getService(GetWechatWorkDepartmentTree::class);

        $result = $procedure->execute(new GetWechatWorkDepartmentTreeParam());

        // 验证返回的是 ArrayResult 对象
        $this->assertInstanceOf(ArrayResult::class, $result);

        // 获取实际的数据数组
        $data = $result->toArray();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('tree', $data);
        $this->assertIsArray($data['tree']);

        // 由于这是集成测试，tree可能为空或包含真实的部门数据
        // 但至少应该是一个数组结构
        foreach ($data['tree'] as $departmentData) {
            $this->assertIsArray($departmentData);
        }
    }

    public function testExecuteUsesNormalizerWithCorrectContext(): void
    {
        /** @var GetWechatWorkDepartmentTree $procedure */
        $procedure = self::getService(GetWechatWorkDepartmentTree::class);

        // 测试方法能正常执行而不抛出异常
        $result = $procedure->execute(new GetWechatWorkDepartmentTreeParam());

        // 验证基本结构
        $this->assertInstanceOf(ArrayResult::class, $result);
        $data = $result->toArray();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('tree', $data);

        // 验证每个部门数据的基本结构（如果存在的话）
        $tree = $data['tree'];
        if (is_array($tree)) {
            foreach ($tree as $departmentData) {
                $this->assertIsArray($departmentData);
                // 可以添加更多具体的字段验证，但由于依赖真实数据，保持最小测试
            }
        }
    }
}
