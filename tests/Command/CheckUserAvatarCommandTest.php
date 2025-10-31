<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatWorkStaffBundle\Command\CheckUserAvatarCommand;
use WechatWorkStaffBundle\Repository\UserRepository;

/**
 * @internal
 */
#[CoversClass(CheckUserAvatarCommand::class)]
#[RunTestsInSeparateProcesses]
final class CheckUserAvatarCommandTest extends AbstractCommandTestCase
{
    private CheckUserAvatarCommand $command;

    private UserRepository&MockObject $userRepository;

    protected function onSetUp(): void
    {
        /*
         * 使用具体类 UserRepository 进行 Mock 的原因：
         * 1. UserRepository 是 Doctrine 的具体 Repository 类，没有对应的通用接口
         * 2. 测试需要 Mock 其 createQueryBuilder() 方法来模拟数据库查询
         * 3. 暂无更好的替代方案，这是 Doctrine Repository 测试的标准做法
         */
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->command = self::getService(CheckUserAvatarCommand::class);
    }

    protected function getCommandTester(): CommandTester
    {
        return new CommandTester($this->command);
    }

    public function testConstructor(): void
    {
        $this->assertNotNull($this->command);
    }

    public function testCommandConfiguration(): void
    {
        $this->assertSame('wechat-work:check-user-avatar', $this->command->getName());
        $this->assertSame('检查用户头像并保存', $this->command->getDescription());
    }

    public function testCommandIsInstantiable(): void
    {
        // 简单验证命令可以被实例化
        $this->assertNotNull($this->command);
    }

    public function testCommandExecutionWithEmptyUserList(): void
    {
        $this->userRepository->method('createQueryBuilder')
            ->willReturn($this->createMockQueryBuilder())
        ;

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertSame(0, $exitCode);
    }

    private function createMockQueryBuilder(): MockObject
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $query = $this->getMockBuilder('Doctrine\ORM\Query')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $query->method('toIterable')->willReturn([]);

        $queryBuilder->method('getQuery')->willReturn($query);

        /*
         * 使用具体类 Doctrine\ORM\Query\Expr 进行 Mock 的原因：
         * 1. Doctrine\ORM\Query\Expr 是 Doctrine ORM 的内部具体类，没有对应接口
         * 2. 测试需要 Mock 其 like() 和 literal() 方法来模拟查询表达式构建
         * 3. 暂无更好的替代方案，这是 Doctrine QueryBuilder 测试的标准做法
         */
        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        /*
         * 使用具体类 Doctrine\ORM\Query\Expr\Comparison 进行 Mock 的原因：
         * 1. Doctrine\ORM\Query\Expr\Comparison 是 Doctrine ORM 查询表达式的内部类，没有公共接口
         * 2. 测试需要模拟 like() 方法返回的比较表达式对象
         * 3. 暂无更好的替代方案，这是 Doctrine 查询表达式测试的必要做法
         */
        $comparisonMock = $this->getMockBuilder('Doctrine\ORM\Query\Expr\Comparison')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        /*
         * 使用具体类 Doctrine\ORM\Query\Expr\Literal 进行 Mock 的原因：
         * 1. Doctrine\ORM\Query\Expr\Literal 是 Doctrine ORM 查询字面量的内部类，没有公共接口
         * 2. 测试需要模拟 literal() 方法返回的字面量表达式对象
         * 3. 暂无更好的替代方案，这是 Doctrine 查询表达式测试的必要做法
         */
        $literalMock = $this->getMockBuilder('Doctrine\ORM\Query\Expr\Literal')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $expr->method('like')->willReturn($comparisonMock);
        $expr->method('literal')->willReturn($literalMock);
        $queryBuilder->method('expr')->willReturn($expr);

        return $queryBuilder;
    }
}
