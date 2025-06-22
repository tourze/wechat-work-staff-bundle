<?php

namespace WechatWorkStaffBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use WechatWorkBundle\Service\WorkService;
use WechatWorkStaffBundle\Entity\UserTag;
use WechatWorkStaffBundle\Request\Tag\CreateTagRequest;
use WechatWorkStaffBundle\Request\Tag\DeleteTagRequest;
use WechatWorkStaffBundle\Request\Tag\UpdateTagRequest;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: UserTag::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: UserTag::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: UserTag::class)]
class UserTagListener
{
    public function __construct(private readonly WorkService $workService) {}

    public function prePersist(UserTag $object): void
    {
        if (null !== $object->getTagId() && null !== $object->getName()) {
            return;
        }

        $request = new CreateTagRequest();
        $request->setAgent($object->getAgent());
        $request->setName($object->getName());
        $request->setId($object->getTagId());
        $response = $this->workService->request($request);
        if (isset($response['tagid'])) {
            $object->setTagId($response['tagid']);
        }
    }

    public function preRemove(UserTag $object): void
    {
        $request = new DeleteTagRequest();
        $request->setAgent($object->getAgent());
        $request->setId($object->getTagId());
        $this->workService->request($request);
    }

    public function preUpdate(UserTag $object): void
    {
        $request = new UpdateTagRequest();
        $request->setAgent($object->getAgent());
        $request->setId($object->getTagId());
        $this->workService->request($request);
    }
}
