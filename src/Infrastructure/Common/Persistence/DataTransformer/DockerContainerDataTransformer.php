<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer\Exception\DockerContainerNotFoundException;
use Ph3\DockerArch\Domain\Service\Model\ServiceInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class DockerContainerDataTransformer
{
    /**
     * @param DockerContainerInterface $dockerContainer
     * @param array                    $data
     *
     * @return DockerContainerInterface
     */
    public function updateModel(DockerContainerInterface $dockerContainer, array $data): DockerContainerInterface
    {
        if ($data['entry_point'] ?? null) {
            $dockerContainer->setEntryPoint($data['entry_point']);
        }
        if ($data['cmd'] ?? null) {
            $dockerContainer->setCmd($data['cmd']);
        }
        if ($data['working_dir'] ?? null) {
            $dockerContainer->setWorkingDir($data['working_dir']);
        }
        foreach ($data['ports'] ?? [] as $port) {
            $dockerContainer->addPortFromString($port);
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
}
