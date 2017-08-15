<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Domain\Project\Model\Project;
use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\ServiceDataTransformer;

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
            $data = json_decode($data, true);
        }

        if (null === $data || false === is_array($data)) {
            return new Project();
        }

        $project = new Project();
        foreach ($data['services'] as $serviceData) {
            $service = (new ServiceDataTransformer())->toModel($serviceData, $project);
            $project->addService($service);
        }

        return $project;
    }
}
