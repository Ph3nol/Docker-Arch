<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class RedisDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function getPackageManager(): string
    {
        return DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $this->setFrom(sprintf('redis:%s-alpine', $this->getService()->getOptions()['version']));

        // Ports.
        $this->addEnvPort('REDIS', ['from' => '8079', 'to' => '6379']);
    }
}
