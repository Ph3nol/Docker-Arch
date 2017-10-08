<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class CustomDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function getPackageManager(): string
    {
        return $this->getService()->getOptions()['package_manager'];
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $this->setFrom($this->getService()->getOptions()['image']);
    }
}
