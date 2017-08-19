<?php

namespace Ph3\DockerArch\Domain\Service\Model;

use Ph3\DockerArch\Domain\Service\Model\ServiceCollection;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;

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
}
