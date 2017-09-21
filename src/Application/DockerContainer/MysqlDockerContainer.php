<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class MysqlDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $service = $this->getService();
        $project = $service->getProject();

        $this->setFrom(sprintf('mysql:%s', $this->getService()->getOptions()['version']));

        $project
            ->addEnv('MYSQL_ROOT_PASSWORD', 'docker')
            ->addEnv('MYSQL_USER', 'docker')
            ->addEnv('MYSQL_PASSWORD', 'docker')
            ->addEnv('MYSQL_DATABASE', 'docker')
            ->addEnv('MYSQL_ALLOW_EMPTY_PASSWORD', 'true')
            ->addEnv('MYSQL_DATA_LOCATION', Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/data/mysql');

        // Service Docker envs.
        $this
            ->addEnvFromProject('MYSQL_ROOT_PASSWORD')
            ->addEnvFromProject('MYSQL_USER')
            ->addEnvFromProject('MYSQL_PASSWORD')
            ->addEnvFromProject('MYSQL_DATABASE')
            ->addEnvFromProject('MYSQL_ALLOW_EMPTY_PASSWORD');

        // Volumes.
        $this
            ->addVolume([
                'local' => '${'.$project->generateEnvKey('MYSQL_DATA_LOCATION').'}',
                'remote' => '/var/lib/mysql',
                'type' => 'rw',
            ]);

        // Ports.
        $this->addEnvPort('MYSQL', ['from' => '8006', 'to' => '3306']);
    }
}
