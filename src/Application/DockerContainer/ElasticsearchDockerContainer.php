<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;
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
        return DockerContainerInterface::PACKAGE_MANAGER_TYPE_APT;
    }

    /**
     * {@inheritdoc}
     */
    public function preExecute(): void
    {
        parent::preExecute();

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
        $project = $service->getProject();

        $this->setFrom(sprintf('elasticsearch:%s', $service->getOptions()['version']));

        // Commands.
        if (true === $service->getOptions()['with_management']) {
            $this
                ->addCommand('echo "http.cors.enabled: true" >> /usr/share/elasticsearch/config/elasticsearch.yml')
                ->addCommand(
                    'echo "http.cors.allow-origin: \\"*\\"" >> /usr/share/elasticsearch/config/elasticsearch.yml'
                );
        }

        $project
            ->addEnv('ELASTIC_SEARCH_DATA_LOCATION', Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/data/elasticsearch');

        // Volumes.
        $this
            ->addVolume([
                'local' => '${ELASTIC_SEARCH_DATA_LOCATION}',
                'remote' => '/usr/share/elasticsearch/data',
                'type' => 'rw',
            ]);

        // Envs.
        $this->addEnv('ES_JAVA_OPTS', '-Xms2g -Xmx2g');

        // Ports.
        $this->addEnvPort('ELASTIC_SEARCH', ['from' => '8020', 'to' => '9200']);
    }

    /**
     * {@inheritdoc}
     */
    public function postExecute(): void
    {
        // UI.
        $port = reset($this->getService()->getDockerContainer()->getPorts());
        $this->getService()->addUIAccess([
            'port' => $port['from'],
            'label' => 'ElasticSearch Main Endpoint',
        ]);
    }
}
