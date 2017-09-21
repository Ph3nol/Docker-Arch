<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Domain\Project\Model\Project;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\InvalidDataFileFormatException;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ProjectDataTransformer
{
    /**
     * @param array   $data
     * @param boolean $yamlString
     *
     * @return ProjectInterface
     */
    public function toModel($data, $yamlString = true): ProjectInterface
    {
        if (true === $yamlString) {
            $data = Yaml::parse($data);
        }

        if (null === $data || false === is_array($data)) {
            throw new InvalidDataFileFormatException(
                sprintf(
                    'Project `%s` YAML content seem to be invalid',
                    Architect::PROJECT_CONFIG_FILENAME
                )
            );
        }

        $project = new Project($data['name'] ?? uniqid());

        // Project properties.
        if ($data['locale'] ?? null) {
            $project->setLocale($data['locale']);
        }
        if ($data['user'] ?? null) {
            $project->setUser($data['user']);
        }
        if ($data['logs_path'] ?? false) {
            $project->setLogsPath($data['logs_path']);
        }
        foreach ($data['envs'] ?? [] as $key => $value) {
            $project->addEnv($key, $value);
        }

        // Services.
        foreach ($data['services'] ?? [] as $serviceData) {
            $service = (new ServiceDataTransformer())->toModel($project, $serviceData);
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
