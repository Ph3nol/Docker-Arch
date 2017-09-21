<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class AtmoDockerContainer extends DockerContainer
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

        $this->setFrom('node:8-alpine');

        $this
            ->addPackage('git')
            ->addCommand('git clone https://github.com/Raathigesh/atmo-standalone-example.git /usr/src/app/atmo')
            ->setWorkingDir('/usr/src/app/atmo')
            ->addConsecutiveCommands([
                'cd /usr/src/app/atmo',
                'npm install',
            ])
            ->addVolume([
                'local' => $service->getOptions()['mock_file_path'],
                'remote' => '/usr/src/app/atmo/spec.json',
                'type' => 'ro',

            ])
            ->setEntryPoint(['npm'])
            ->setCmd(['start']);

        // Ports.
        $this->addEnvPort('ATMO', ['from' => '9999', 'to' => '1337']);
    }

    /**
     * {@inheritdoc}
     */
    public function postExecute(): void
    {
        // UI.
        $port = reset($this->getService()->getDockerContainer()->getPorts());
        $this->getService()->addUIAccess([
            'url' => 'localhost',
            'port' => $port['from'],
            'label' => 'Base URL',
        ]);
    }
}
