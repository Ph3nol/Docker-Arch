<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\DockerContainerNotFoundException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\ServiceNotFoundException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\ServiceWithNoTypeException;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ServiceDataTransformer
{
    /**
     * @param array            $data
     * @param ProjectInterface $project
     *
     * @return ServiceInterface
     */
    public function toModel(array $data, ProjectInterface $project): ServiceInterface
    {
        if (false === ($data['type'] ?? false)) {
            throw new ServiceWithNoTypeException();
        }

        $serviceFqcn = $this->getServiceFqcn($data['type']);
        $containerFqcn = $this->getDockerContainerFqcn($data['type']);

        $service = new $serviceFqcn($project, $data['options'] ? : []);
        if ($data['localPath'] ?? null) {
            $service->setLocalPath($data['localPath']);
        }
        if ($data['dockerSync'] ?? null) {
            $service->withDockerSync();
        }
        if ($data['host'] ?? null) {
            $service->setHost($data['host']);
        }

        $dockerContainer = new $containerFqcn($service);
        $dockerContainer = (new DockerContainerDataTransformer())->toModel($data['container'] ?? [], $dockerContainer);
        $service->setDockerContainer($dockerContainer);

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

    /**
     * @param string $type
     *
     * @return string
     */
    private function getDockerContainerFqcn(string $type): string
    {
        $fqcn = sprintf(
            '\\Ph3\\DockerArch\\Application\\DockerContainer\\%sDockerContainer',
            ucfirst($type)
        );
        if (false === class_exists($fqcn)) {
            throw new DockerContainerNotFoundException(
                'DockerContainer '.$fqcn.' does not exist'
            );
        }

        return $fqcn;
    }
}
