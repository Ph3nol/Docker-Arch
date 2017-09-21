<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MailcatcherDockerContainer extends DockerContainer
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
        $this->setFrom('tophfr/mailcatcher');

        // Ports.
        $clientPort = $this->addEnvPort('MAILCATCHER_CLIENT', ['from' => '8880', 'to' => '80']);
        $this->addEnvPort('MAILCATCHER_SMTP', ['from' => '8825', 'to' => '25']);

        // UI.
        $this->getService()->addUIAccess([
            'port' => $clientPort['from'],
            'label' => 'Web Client',
        ]);
    }
}
