<?php

namespace Ph3\DockerArch\Domain\Project\Model;

use Ph3\DockerArch\Domain\Service\Model\ServiceCollection;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface ProjectInterface
{
    /**
     * @return ServiceInterface[]
     */
    public function getServices(): ServiceCollection;

    /**
     * @param ServiceInterface $service
     *
     * @return self
     */
    public function addService(ServiceInterface $service): self;
}
