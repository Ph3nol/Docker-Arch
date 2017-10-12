<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class CapistranoDockerContainer extends DockerContainer
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
        $this->setFrom('ruby:2-alpine3.6');

        $service = $this->getService();

        $this->applyDotfiles();
        $this->applyShellConfiguration();
        $this->setWorkingDir('/apps');

        $this->addCommand('gem install capistrano --version='.$service->getOptions()['version']);
    }
}
