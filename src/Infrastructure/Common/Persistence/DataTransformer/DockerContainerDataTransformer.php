<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\Service\Model\DockerContainerNotFoundException;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class DockerContainerDataTransformer
{
    /**
     * @param ServiceInterface $data
     *
     * @return DockerContainerInterface
     */
    public function toModel(ServiceInterface $service, array $data): DockerContainerInterface
    {
        $containerFqcn = $this->getDockerContainerFqcn($service->getName());
        $dockerContainer = new $containerFqcn($service);
        $dockerContainer->init();

        if ($data['entry_point'] ?? null) {
            $dockerContainer->setEntryPoint($data['entry_point']);
        }
        if ($data['cmd'] ?? null) {
            $dockerContainer->setCmd($data['cmd']);
        }
        if ($data['working_dir'] ?? null) {
            $dockerContainer->setWorkingDir($data['working_dir']);
        }
        foreach ($data['ports'] ?? [] as $from => $to) {
            $dockerContainer->addPort([
                'from' => $from,
                'to' => $to,
            ]);
        }
        foreach ($data['envs'] ?? [] as $key => $value) {
            $dockerContainer->addEnv($key, $value);
        }
        foreach ($data['packages'] ?? [] as $package) {
            $dockerContainer->addPackage($package);
        }
        foreach ($data['commands'] ?? [] as $command) {
            $dockerContainer->addCommand($command);
        }
        foreach ($data['copy_entries'] ?? [] as $copyEntry) {
            $dockerContainer->addCopyEntryFromString($copyEntry);
        }
        foreach ($data['volumes'] ?? [] as $volume) {
            $dockerContainer->addVolumeFromString($volume);
        }

        return $dockerContainer;
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
