<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Service\CerebroService;
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
    public function init(): void
    {
        $this->setPackageManager(DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK);

        parent::init();

        $this->setFrom(sprintf('blacktop/elasticsearch:%s', $this->getService()->getOptions()['version']));

        $service = $this->getService();
        $project = $service->getProject();

        // Commands.
        if (true === $service->getOptions()['with_management']) {
            $cerebroService = CerebroService::getInstanceForParentService($this->getService());
            $project->addService($cerebroService);
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
