<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NodeJSDockerContainer extends DockerContainer
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
        $service = $this->getService();

        $this->setFrom(sprintf('node:%s-alpine', $service->getOptions()['version']));

        $this->applyWebServiceConfiguration();
        $this->applyShellConfiguration();

        // Volumes.
        if (true === $service->getOptions()['dotfiles']) {
            $this->applyDotfiles();
        }

        // NPM packages.
        if ($npmPackages = $service->getOptions()['npm_packages'] ?? []) {
            $this->addCommand('npm install -g '.implode(' ', $npmPackages));
        }

        // Ports.
        $this->addEnvPort('NODEJS', ['from' => '8090', 'to' => '9000']);
    }
}
