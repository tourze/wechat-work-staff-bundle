<?php

namespace WechatWorkStaffBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;
use WechatWorkStaffBundle\Controller\AuthCallbackController;
use WechatWorkStaffBundle\Controller\AuthRedirectController;
use WechatWorkStaffBundle\Controller\ConnectCallbackController;
use WechatWorkStaffBundle\Controller\ConnectRedirectController;
use WechatWorkStaffBundle\Controller\TestDepartmentListController;
use WechatWorkStaffBundle\Controller\TestSimpleUserListController;
use WechatWorkStaffBundle\Controller\TestUserDetailController;
use WechatWorkStaffBundle\Controller\TestUserIdListController;

#[AutoconfigureTag('routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    private AttributeRouteControllerLoader $controllerLoader;

    public function __construct()
    {
        parent::__construct();
        $this->controllerLoader = new AttributeRouteControllerLoader();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        $collection->addCollection($this->controllerLoader->load(AuthCallbackController::class));
        $collection->addCollection($this->controllerLoader->load(AuthRedirectController::class));
        $collection->addCollection($this->controllerLoader->load(ConnectCallbackController::class));
        $collection->addCollection($this->controllerLoader->load(ConnectRedirectController::class));
        $collection->addCollection($this->controllerLoader->load(TestDepartmentListController::class));
        $collection->addCollection($this->controllerLoader->load(TestSimpleUserListController::class));
        $collection->addCollection($this->controllerLoader->load(TestUserDetailController::class));
        $collection->addCollection($this->controllerLoader->load(TestUserIdListController::class));
        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }
}