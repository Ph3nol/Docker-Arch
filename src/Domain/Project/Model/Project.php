<?php

namespace Ph3\DockerArch\Domain\Project\Model;

use Ph3\DockerArch\Domain\Service\Model\ServiceCollection;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFilesPropertyTrait;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class Project implements ProjectInterface
{
    use TemplatedFilesPropertyTrait;

    /**
     * @var ServiceInterface[]
     */
    private $services;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->services = new ServiceCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getServices(): ServiceCollection
    {
        return $this->services;
    }

    /**
     * {@inheritdoc}
     */
    public function addService(ServiceInterface $service): ProjectInterface
    {
        $this->services->append($service);

        return $this;
    }

    /**
     * @return ServiceInterface[]
     */
    public function getDockerSynchedServices(): ServiceCollection
    {
        return $this->getServices()->getDockerSynchedServices();
    }

    /**
     * @return ServiceInterface[]
     */
    public function getCliServices(): ServiceCollection
    {
        return $this->getServices()->getCliServices();
    }

    /**
     * @param ServiceInterface $forService
     *
     * @return array
     */
    public function getLinksForService(ServiceInterface $forService): array
    {
        // Some logic to links Service and avoid circular references.
        $isAllowed = function (ServiceInterface $service, ServiceInterface $forService) {
            if (true === in_array($service->getName(), ['nginx'])) {
                return false;
            }

            if ('nginx' === $service->getName() && 'nginx' !== $forService->getName()) {
                return false;
            }

            if ($service->getName() === $forService->getName()) {
                return false;
            }

            return true;
        };

        $links = [];
        foreach ($this->getServices() as $service) {
            if ($service->isSame($forService)) {
                continue;
            }

            if (true === $isAllowed($service, $forService)) {
                $links[] = $service->getIdentifier();
            }
        }

        return $links;
    }

    /**
     * @param ServiceInterface $forService
     *
     * @return array
     */
    public function getVolumesForService(ServiceInterface $forService): array
    {
        $volumes = [];

        foreach ($this->getServices() as $service) {
            if (true === $service->isDockerSynched()) {
                $volume = $this->getDockerSynchedServiceVolume($service);
                $volumes[$volume['remote']] = $volume;
            } else {
                $volume = $this->getClassicServiceVolume($service);
                $volumes[$volume['remote']] = $volume;
            }
        }

        $servicesDockerContainers = array_map(function (ServiceInterface $service) {
            return $service->getDockerContainer();
        }, (array) $this->getServices());

        foreach ($servicesDockerContainers as $container) {
            $volumes += $container->getVolumes();
        }

        return array_filter($volumes);
    }

    /**
     * @param ServiceInterface $service
     *
     * @return array|null
     */
    private function getDockerSynchedServiceVolume(ServiceInterface $service): ?array
    {
        return [
            'local' => 'docker-arch-'.$service->getIdentifier().'-sync',
            'remote' => '/apps/'.($service->getHost() ? : $service->getIdentifier()),
            'type' => 'nocopy',
        ];
    }

    /**
     * @param ServiceInterface $service
     *
     * @return array|null
     */
    private function getClassicServiceVolume(ServiceInterface $service): ?array
    {
        if (null === $localPath = $service->getLocalPath()) {
            return null;
        }

        return [
            'local' => $localPath,
            'remote' => '/apps/'.($service->getHost() ? : $service->getIdentifier()),
            'type' => 'rw',
        ];
    }
}
