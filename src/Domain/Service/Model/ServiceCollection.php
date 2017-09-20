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
            if (true === (bool) preg_match('/(php|nodejs)/i', $service->getName())) {
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
            if (true === $service->isWebService()) {
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
            if (true === $service->isVhostService()) {
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
     * @param string $name
     *
     * @return ServiceInterface[]
     */
    public function getServicesForName(string $name): ServiceCollection
    {
        $services = [];
        foreach ($this as $service) {
            if ($service->getName() === $name) {
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
            $servicesCount[$service->getName()] += 1;
            if (1 < count($this->getServicesForName($service->getName()))) {
                $serviceIdentifier = sprintf(
                    '%s-%s',
                    $service->getName(),
                    $service->getHost() ? : $servicesCount[$service->getName()]
                );
            } else {
                $serviceIdentifier = $service->getName();
            }

            $service->setIdentifier((new Slugify())->slugify($serviceIdentifier));
        }
    }
}
