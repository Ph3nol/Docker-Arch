<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Service\NginxService;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ElasticsearchHeadDockerContainer extends DockerContainer
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

        $this->setFrom('mobz/elasticsearch-head:5-alpine');

        // Ports.
        $port = $this->addEnvPort('ELASTIC_SEARCH_HEAD', ['from' => '8022', 'to' => '9100']);

        // UI.
        $elasticSearchPorts = $service->getParentService()->getDockerContainer()->getPorts();
        $elasticSearchPort = reset($elasticSearchPorts)['from'];
        $service->getParentService()->addUIAccess([
            'url' => 'localhost',
            'uri' => '?base_uri=http://localhost:'.$elasticSearchPort.'/',
            'port' => $port['from'],
            'label' => 'Mobz Head',
        ]);
    }
}
