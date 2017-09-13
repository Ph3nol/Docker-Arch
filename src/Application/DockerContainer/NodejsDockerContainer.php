<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

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
        parent::init();

        $service = $this->getService();
        $project = $service->getProject();

        $this->setFrom(sprintf('node:%s', $service->getOptions()['version']));

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
