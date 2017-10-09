<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\ServiceNotFoundException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\ServiceWithNoTypeException;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ServiceDataTransformer
{
    /**
     * @param ProjectInterface $project
     * @param array            $data
     *
     * @return ServiceInterface
     */
    public function toModel(ProjectInterface $project, array $data): ServiceInterface
    {
        if (false === ($data['type'] ?? false)) {
            throw new ServiceWithNoTypeException();
        }

        $serviceFqcn = $this->getServiceFqcn($data['type']);
        $service = new $serviceFqcn($project, $data['options'] ? : []);
        if ($data['path'] ?? null) {
            $service->setPath($data['path']);
        }
        if ($data['docker_sync'] ?? null) {
            $service->withDockerSync();
        }
        if ($data['host'] ?? null) {
            $service->setHost($data['host']);
        }
        if ($data['identifier'] ?? null) {
            $service->setIdentifier($data['identifier']);
        }

        return $service;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getServiceFqcn(string $type): string
    {
        $fqcn = sprintf(
            '\\Ph3\\DockerArch\\Application\\Service\\%sService',
            ucfirst($type)
        );
        if (false === class_exists($fqcn)) {
            throw new ServiceNotFoundException(
                'Service '.$fqcn.' does not exist'
            );
        }

        return $fqcn;
    }
}
