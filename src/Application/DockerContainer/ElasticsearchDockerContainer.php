<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Service\CerebroService;
use Ph3\DockerArch\Application\Service\ElasticsearchHeadService;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ElasticsearchDockerContainer extends DockerContainer
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
    public function preExecute(): void
    {
        $service = $this->getService();
        if (true === $service->getOptions()['with_management']) {
            $service
                ->getProject()
                ->addService(CerebroService::getInstanceForParentService($service))
                ->addService(ElasticsearchHeadService::getInstanceForParentService($service));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $service = $this->getService();

        $this->setFrom(sprintf('blacktop/elasticsearch:%s', $service->getOptions()['version']));

        // Commands.
        if (true === $service->getOptions()['with_management']) {
            $this
                ->addCommand('echo "http.cors.enabled: true" >> /usr/share/elasticsearch/config/elasticsearch.yml')
                ->addCommand(
                    'echo "http.cors.allow-origin: \\"*\\"" >> /usr/share/elasticsearch/config/elasticsearch.yml'
                );
        }

        // Ports.
        $mainPort = $this->addEnvPort('ELASTIC_SEARCH', ['from' => '8020', 'to' => '9200']);

        // UI.
        $this->getService()->addUIAccess([
            'port' => $mainPort['from'],
            'label' => 'ElasticSearch Main Endpoint',
        ]);
    }
}
