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
        $this->setPackageManager(DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK);

        parent::init();

        $this->setFrom(sprintf('redis:%s-alpine', $this->getService()->getOptions()['version']));

        $service = $this->getService();
        $project = $service->getProject();

        // Ports.
        $portKey = $service->generateEnvKey('REDIS_PORT');
        $project->addEnv($portKey, '8079');
        $this->addPort('${'.$project->generateEnvKey($portKey).'}', '6379');
    }
}
