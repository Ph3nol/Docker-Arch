<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class RabbitmqDockerContainer extends DockerContainer
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
        $this->addEnvPort('RABBITMQ', ['from' => '18072', 'to' => '15672']);
        if (true === $service->getOptions()['with_management']) {
            $managementPort = $this->addEnvPort('RABBITMQ_MANAGEMENT', ['from' => '8072', 'to' => '5672']);
        }

        // UI.
        if (true === $service->getOptions()['with_management']) {
            $this->getService()->addUIAccess([
                'port' => $managementPort['from'],
                'label' => 'Management UI',
            ]);
        }
    }
}
