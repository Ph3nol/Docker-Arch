<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NodejsDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->setPackageManager(DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK);

        parent::init();

        $service = $this->getService();

        $this->setFrom(sprintf('node:%s-alpine', $service->getOptions()['version']));

        $this->applyWebServiceConfiguration();
        $this->applyShellConfiguration();

        // Volumes.
        if (true === $service->getOptions()['dotfiles']) {
            $this->applyDotfiles();
        }

        // Ports.
        $this->addEnvPort('NODEJS', ['from' => '8090', 'to' => '9000']);
    }
}
