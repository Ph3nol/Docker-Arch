<?php

namespace Ph3\DockerArch\Infrastructure\Project\Repository;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Infrastructure\Common\AbstractRepository;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\DockerContainerDataTransformer;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\InvalidDataFileFormatException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\ProjectDataTransformer;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\ServiceDataTransformer;
use Ph3\DockerArch\Infrastructure\Common\Persistence\PersisterInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ProjectRepository extends AbstractRepository implements ProjectRepositoryInterface
{
    /**
     * @var array
     */
    private $servicesFqcns;

    /**
     * @param PersisterInterface $persister
     * @param array              $servicesFqcns
     */
    public function __construct(PersisterInterface $persister, array $servicesFqcns)
    {
        parent::__construct($persister);

        $this->servicesFqcns = $servicesFqcns;
    }

    /**
     * {@inheritdoc}
     */
    public function getProject(): ProjectInterface
    {
        $projectData = $this->getPersister()->read();

        $projectData = Yaml::parse($projectData);
        if (null === $projectData || false === is_array($projectData)) {
            throw new InvalidDataFileFormatException(
                sprintf(
                    'Project `%s` YAML content seem to be invalid',
                    Architect::PROJECT_CONFIG_FILENAME
                )
            );
        }

        $project = (new ProjectDataTransformer())->toModel($projectData);

        // Services.
        $serviceDataTransformer = new ServiceDataTransformer($this->servicesFqcns);
        foreach ($projectData['services'] ?? [] as $serviceData) {
            $service = $serviceDataTransformer->toModel($project, $serviceData);
            $project->addService($service);
        }
        $project->updateServicesIdentifiers();

        // Services DockerContainers.
        // Pre.
        foreach ($project->getServices() as $k => $service) {
            $service->getDockerContainer()->preExecute();
        }
        // Execution.
        foreach ($project->getServices() as $k => $service) {
            $dockerContainer = $service->getDockerContainer();
            $dockerContainer->execute();
            (new DockerContainerDataTransformer())
                ->updateModel($dockerContainer, $data['services'][$k]['container'] ?? []);
        }
        // Post.
        foreach ($project->getServices() as $k => $service) {
            $service->getDockerContainer()->postExecute();
        }

        return $project;
    }
}
