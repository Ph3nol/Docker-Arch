<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MysqlDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $service = $this->getService();

        $this->setFrom(sprintf('mysql:%s', $this->getService()->getOptions()['version']));

        // Volumes.
        $dataLocation = $this->getService()->getOptions()['dataLocation'];
        if (null !== $dataLocation) {
            $this->addVolume([
                'local' => $dataLocation,
                'remote' => '/var/lib/mysql',
                'type' => 'rw',
            ]);
        }

        // Envs.
        $this
            ->addEnv('MYSQL_ROOT_PASSWORD', $service->getOptions()['rootPassword'])
            ->addEnv('MYSQL_USER', $service->getOptions()['user'])
            ->addEnv('MYSQL_PASSWORD', $service->getOptions()['password'])
            ->addEnv('MYSQL_DATABASE', $service->getOptions()['database'])
            ->addEnv('MYSQL_ALLOW_EMPTY_PASSWORD', $service->getOptions()['allowEmptyPassword'] ? 'true' : 'false');
    }
}
