<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class RedisDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this->setFrom(sprintf('redis:%s-alpine', $this->getService()->getOptions()['version']));

        $project = $this->getService()->getProject();

        // Ports.
        $portKey = $service->generateEnvKey('REDIS_PORT');
        $project->addEnv($portKey, ('77'.rand(11, 99)));
        $this->addPort('${'.$project->generateEnvKey($portKey).'}', '6379');
    }
}
