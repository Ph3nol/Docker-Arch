<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface HasParentServiceInterface
{
    /**
     * @param ServiceInterface $service
     *
     * @return CerebroService
     */
    public static function getInstanceForParentService(ServiceInterface $service): ServiceInterface;

    /**
     * @return ServiceInterface
     */
    public function getParentService(): ServiceInterface;

    /**
     * @return boolean
     */
    public function hasParentService(): bool;

    /**
     * @param ServiceInterface $parentService
     *
     * @return ServiceInterface
     */
    public function setParentService(ServiceInterface $parentService): ServiceInterface;
}
