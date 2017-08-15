<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerEnvsTrait
{
    /**
     * @var array
     */
    protected $envs = [];

    /**
     * @return array
     */
    public function getEnvs(): array
    {
        return $this->envs;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function addEnv(string $key, string $value): self
    {
        $this->envs[$key] = $value;

        return $this;
    }

    /**
     * @param array $envs
     *
     * @return self
     */
    public function setEnvs(array $envs): self
    {
        $this->envs = $envs;

        return $this;
    }
}
