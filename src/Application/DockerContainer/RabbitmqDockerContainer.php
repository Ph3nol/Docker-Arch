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
class RabbitmqDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->setPackageManager(DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK);

        parent::init();

        $this->setFrom(sprintf('rabbitmq:%s-alpine', $this->getService()->getOptions()['version']));

        $service = $this->getService();
        $project = $service->getProject();

        $project
            ->addEnv('RABBITMQ_DEFAULT_USER', 'docker')
            ->addEnv('RABBITMQ_DEFAULT_PASS', 'docker')
            ->addEnv('RABBITMQ_DEFAULT_VHOST', 'docker');

        // Service Docker envs.
        $this
            ->addEnvFromProject('RABBITMQ_DEFAULT_USER')
            ->addEnvFromProject('RABBITMQ_DEFAULT_PASS')
            ->addEnvFromProject('RABBITMQ_DEFAULT_VHOST');

        // Ports.
        if (true === $this->getService()->getOptions()['with_management']) {
            $this->addEnvPort('RABBITMQ', ['from' => '18072', 'to' => '15672']);
        }
        $this->addEnvPort('RABBITMQ_MANAGEMENT', [
            'from' => '8072',
            'to' => '5672',
            'ui' => true,
            'label' => 'RabbitMQ Management UI',
        ]);
    }
}
