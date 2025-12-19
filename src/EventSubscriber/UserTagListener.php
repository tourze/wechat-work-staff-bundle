<?php

declare(strict_types=1);

namespace WechatWorkStaffBundle\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use WechatWorkBundle\Service\WorkServiceInterface;
use WechatWorkStaffBundle\Entity\UserTag;
use WechatWorkStaffBundle\Request\Tag\CreateTagRequest;
use WechatWorkStaffBundle\Request\Tag\DeleteTagRequest;
use WechatWorkStaffBundle\Request\Tag\UpdateTagRequest;

#[Autoconfigure(public: true)]
#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: UserTag::class)]
#[AsEntityListener(event: Events::preRemove, method: 'preRemove', entity: UserTag::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: UserTag::class)]
final class UserTagListener
{
    public function __construct(private readonly ?WorkServiceInterface $workService = null)
    {
    }

    public function prePersist(UserTag $object): void
    {
        if (null !== $object->getTagId() && null !== $object->getName()) {
            return;
        }

        if (null === $this->workService) {
            return;
        }

        $request = new CreateTagRequest();
        $request->setAgent($object->getAgent());
        $request->setName($object->getName() ?? '');
        $request->setId($object->getTagId());
        $response = $this->workService->request($request);
        assert(is_array($response));
        if (isset($response['tagid'])) {
            $tagId = $response['tagid'];
            if (is_int($tagId)) {
                $object->setTagId($tagId);
            }
        }
    }

    public function preRemove(UserTag $object): void
    {
        if (null === $this->workService) {
            return;
        }

        $tagId = $object->getTagId();
        if (null === $tagId) {
            return;
        }

        $request = new DeleteTagRequest();
        $request->setAgent($object->getAgent());
        $request->setId($tagId);
        $this->workService->request($request);
    }

    public function preUpdate(UserTag $object): void
    {
        if (null === $this->workService) {
            return;
        }

        $tagId = $object->getTagId();
        if (null === $tagId) {
            return;
        }

        $request = new UpdateTagRequest();
        $request->setAgent($object->getAgent());
        $request->setId($tagId);
        $request->setName($object->getName() ?? '');
        $this->workService->request($request);
    }
}
