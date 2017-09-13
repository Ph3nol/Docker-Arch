<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MailcatcherDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->setFrom('schickling/mailcatcher');

        // Ports.
        $port = $this->addEnvPort('MAILCATCHER', ['from' => '8880', 'to' => '1080']);

        // UI.
        $this->getService()->addUIAccess([
            'port' => $port['from'],
            'label' => 'MailCatcher Web Client',
        ]);
    }
}
