<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\MockObject;

use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

/**
 * 测试用的Mock链接生成器
 */
class MockLinkGenerator implements LinkGeneratorInterface
{
    private ?string $defaultDashboardFqcn = null;

    /**
     * @param array<string, mixed> $parameters
     */
    public function generate(string $route, array $parameters = []): string
    {
        return '/mock/' . $route;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function generateFromName(object|string $name, array $parameters = []): string
    {
        return '/mock/' . (is_object($name) ? $name::class : $name);
    }

    public function getCurdListPage(string $entityFqcn): string
    {
        return '/mock/crud/' . str_replace('\\', '/', $entityFqcn);
    }

    public function extractEntityFqcn(string $url): ?string
    {
        // 简单的Mock实现，从URL中提取实体类名
        if (str_contains($url, '/crud/')) {
            $parts = explode('/crud/', $url);
            if (isset($parts[1])) {
                return str_replace('/', '\\', $parts[1]);
            }
        }
        return null;
    }

    public function setDashboard(string $dashboardControllerFqcn): void
    {
        $this->defaultDashboardFqcn = $dashboardControllerFqcn;
    }

    public function getDefaultDashboard(): ?string
    {
        return $this->defaultDashboardFqcn;
    }
}
