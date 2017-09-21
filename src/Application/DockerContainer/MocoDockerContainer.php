<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Alexis NIVON <anivon@alexisnivon.fr>
 */
class MocoDockerContainer extends DockerContainer
{
    const MOCO_INTERNAL_PORT = '8000';

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
        $this->setFrom('rezzza/docker-moco');
        $this->applyWebServiceConfiguration();
        $this->setMaintainer('Alexis NIVON <anivon@alexisnivon.fr>');

        $mocoServerPort = $this->addEnvPort('MOCO_SERVER', ['from' => '8888', 'to' => self::MOCO_INTERNAL_PORT]);
        $this->getService()->addUIAccess([
            'port' => $mocoServerPort['from'],
            'label' => 'Base URL',
        ]);

        $this->setEntryPoint(['/usr/local/bin/moco']);

        $mockFilename = $this->getService()->getOptions()['mock_filename'];
        $this->setCmd([
            'start',
            '-p',
            self::MOCO_INTERNAL_PORT,
            '-c',
            sprintf('%s/%s', $this->getWorkingDir(), $mockFilename)
        ]);
    }
}
