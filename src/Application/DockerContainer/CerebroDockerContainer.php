<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 * @see https://hub.docker.com/r/yannart/cerebro
 */
class CerebroDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this->setPackageManager(DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK);

        parent::init();

        $service = $this->getService();

        $this->setFrom('openjdk:8-jre-alpine');

        $service
            ->addTemplatedFile(new TemplatedFile(
                'cerebro/application.conf',
                'Service/Cerebro/application.conf.twig'
            ));

        $this
            ->addCopyEntry([
                'local' => 'cerebro/application.conf',
                'remote' => '/tmp/cerebro-application.conf',
            ])
            ->addEnv('CEREBRO_VERSION', '0.6.6')
            ->addConsecutiveCommands([
                'cd /usr/share',
                'wget -qO cerebro-${CEREBRO_VERSION}.tgz https://github.com/lmenezes/cerebro/releases/download/v${CEREBRO_VERSION}/cerebro-${CEREBRO_VERSION}.tgz',
                'tar zxf cerebro-${CEREBRO_VERSION}.tgz',
                'rm cerebro-${CEREBRO_VERSION}.tgz',
                'mkdir cerebro-${CEREBRO_VERSION}/logs',
                'mv cerebro-${CEREBRO_VERSION} cerebro',
            ])
            ->addConsecutiveCommands([
                'rm /usr/share/cerebro/conf/application.conf',
                'mv /tmp/cerebro-application.conf /usr/share/cerebro/conf/application.conf',
            ])
            ->setWorkingDir('/usr/share')
            ->setEntryPoint('/usr/share/cerebro/bin/cerebro');

        // Ports.
        $port = $this->addEnvPort('CEREBRO', ['from' => '8021', 'to' => '9000']);

        // UI.
        $service->getParentService()->addUIAccess([
            'url' => 'localhost',
            'port' => $port['from'],
            'label' => 'Cerebro',
        ]);
    }
}
