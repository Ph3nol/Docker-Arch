<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Cocur\Slugify\Slugify;
use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Domain\Project\Model\Project;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\InvalidDataFileFormatException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\ServiceDataTransformer;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ProjectDataTransformer
{
    /**
     * @param array   $data
     * @param boolean $jsonString
     *
     * @return ProjectInterface
     */
    public function toModel($data, $jsonString = true): ProjectInterface
    {
        if (true === $jsonString) {
            $data = Yaml::parse($data);
        }

        if (null === $data || false === is_array($data)) {
            throw new InvalidDataFileFormatException(
                sprintf(
                    'Project `%s` JSON content seem to be invalid',
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
        foreach ($data['services'] ?? [] as $serviceData) {
            $service = (new ServiceDataTransformer())->toModel($serviceData, $project);
            $project->addService($service);
        }
        foreach ($data['envs'] ?? [] as $key => $value) {
            $project->addEnv($key, $value);
        }

        $this->updateProjectServicesIdentifiers($project);

        // Project services Docker containers initialization.
        foreach ($project->getServices() as $service) {
            $service->getDockerContainer()->init();
        }

        return $project;
    }

    /**
     * @param ProjectInterface $project
     *
     * @return void
     */
    private function updateProjectServicesIdentifiers(ProjectInterface $project): void
    {
        foreach ($project->getServices() as $service) {
            $identicalNameServices = $project->getServicesForName($service->getName());
            if (1 >= $identicalNameServices) {
                $service->setIdentifier(
                    (new Slugify())->slugify($service->getName())
                );
            }
        }
    }
}
