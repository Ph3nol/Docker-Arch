<?php

namespace Ph3\DockerArch\Domain\Service\Model;

use Cocur\Slugify\Slugify;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class ServiceCollection extends \ArrayObject
{
    /**
     * @return ServiceInterface[]
     */
    public function getDockerSynchedServices(): ServiceCollection
    {
        $synchedServices = [];
        foreach ($this as $service) {
            if (true === $service->isDockerSynched()) {
                $synchedServices[] = $service;
            }
        }

        return new ServiceCollection($synchedServices);
    }

    /**
     * @return ServiceInterface[]
     */
    public function getCliServices(): ServiceCollection
    {
        $cliServices = [];
        foreach ($this as $service) {
            if ($service->isCli()) {
                $cliServices[] = $service;
            }
        }

        return new ServiceCollection($cliServices);
    }

    /**
     * @return ServiceInterface[]
     */
    public function getWebServices(): ServiceCollection
    {
        $webServices = [];
        foreach ($this as $service) {
            if ($service->isWeb()) {
                $webServices[] = $service;
            }
        }

        return new ServiceCollection($webServices);
    }

    /**
     * @return ServiceInterface[]
     */
    public function getVhostServices(): ServiceCollection
    {
        $vhostServices = [];
        foreach ($this as $service) {
            if (true === $service->isVhost()) {
                $vhostServices[] = $service;
            }
        }

        return new ServiceCollection($vhostServices);
    }

    /**
     * @param ServiceInterface $refService
     *
     * @return ServiceInterface[]
     */
    public function getCollectionWithoutService(ServiceInterface $refService): ServiceCollection
    {
        $services = [];
        foreach ($this as $service) {
            if ($refService->getIdentifier() !== $service->getIdentifier()) {
                $services[] = $service;
            }
        }

        return new ServiceCollection($services);
    }

    /**
     * @param string $identifier
     *
     * @return ServiceInterface[]
     */
    public function getServicesForIdentifier(string $identifier): ServiceCollection
    {
        $services = [];
        foreach ($this as $service) {
            if ($service->getIdentifier() === $identifier) {
                $services[] = $service;
            }
        }

        return new ServiceCollection($services);
    }

    /**
     * @return void
     */
    public function updateIdentifiers(): void
    {
        $servicesCount = [];
        foreach ($this as $service) {
            $servicesCount[$service->getIdentifier()] += 1;
            if (1 < count($this->getServicesForIdentifier($service->getIdentifier()))) {
                $serviceIdentifier = sprintf(
                    '%s-%s',
                    $service->getIdentifier(),
                    $service->getHost() ? : $servicesCount[$service->getIdentifier()]
                );
            } else {
                $serviceIdentifier = $service->getIdentifier();
            }

            $service->setIdentifier((new Slugify())->slugify($serviceIdentifier));
        }
    }
}
