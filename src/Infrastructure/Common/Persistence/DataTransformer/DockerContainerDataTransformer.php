<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence\DataTransformer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class DockerContainerDataTransformer
{
    /**
     * @param array                    $data
     * @param DockerContainerInterface $dockerContainer
     *
     * @return DockerContainerInterface
     */
    public function toModel(array $data, DockerContainerInterface $dockerContainer = null): ?DockerContainerInterface
    {
        if (null === $dockerContainer) {
            $dockerContainer = new DockerContainer();
        }

        if ($data['entryPoint'] ?? null) {
            $dockerContainer->setEntryPoint($data['entryPoint']);
        }
        if ($data['workingDir'] ?? null) {
            $dockerContainer->setWorkingDir($data['workingDir']);
        }
        foreach ($data['ports'] ?? [] as $from => $to) {
            $dockerContainer->addPort($from, $to);
        }
        foreach ($data['envs'] ?? [] as $key => $value) {
            $dockerContainer->addEnv($key, $value);
        }
        foreach ($data['commands'] ?? [] as $command) {
            $dockerContainer->addCommand($command);
        }
        foreach ($data['copyEntries'] ?? [] as $copyEntry) {
            $dockerContainer->addCopyEntry($copyEntry);
        }
        foreach ($data['volumes'] ?? [] as $volume) {
            $dockerContainer->addVolume($volume);
        }

        return $dockerContainer;
    }
}
