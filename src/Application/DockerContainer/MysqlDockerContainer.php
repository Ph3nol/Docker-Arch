<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;
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
        parent::init();

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
        $portKey = $service->generateEnvKey('MYSQL_PORT');
        $project->addEnv($portKey, ('77'.rand(11, 99)));
        $this->addPort('${'.$project->generateEnvKey($portKey).'}', '3306');
    }
}
