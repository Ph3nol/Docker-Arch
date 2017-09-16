<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait ParentServiceTrait
{
    /**
     * @var ServiceInterface
     */
    protected $parentService;

    /**
     * {@inheritdoc}
     */
    public function getParentService(): ServiceInterface
    {
        return $this->parentService;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParentService(): bool
    {
        return (bool) (null !== $this->getParentService());
    }

    /**
     * {@inheritdoc}
     */
    public function setParentService(ServiceInterface $parentService): ServiceInterface
    {
        $this->parentService = $parentService;

        return $this;
    }
}
