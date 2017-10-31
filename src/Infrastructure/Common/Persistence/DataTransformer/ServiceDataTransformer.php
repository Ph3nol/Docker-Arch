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
     * @var array
     */
    private $servicesFqcns;

    /**
     * @param array $servicesFqcns
     */
    public function __construct(array $servicesFqcns)
    {
        $this->servicesFqcns = $servicesFqcns;
    }

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
        if ($data['remote_path'] ?? null) {
            $service->setRemotePath($data['remote_path']);
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
     * @param string $name
     *
     * @return string
     */
    private function getServiceFqcn(string $name): string
    {
        if (false === array_key_exists($name, $this->servicesFqcns)) {
            throw new ServiceNotFoundException(
                'Service '.$name.' does not exist'
            );
        }

        return $this->servicesFqcns[$name];
    }
}
