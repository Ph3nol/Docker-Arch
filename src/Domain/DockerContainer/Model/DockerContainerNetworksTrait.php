<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerNetworksTrait
{
    /**
     * @var array
     */
    protected $networks = [];

    /**
     * @return array
     */
    public function getNetworks(): array
    {
        return $this->networks;
    }

    /**
     * @param string $name
     * @param array  $aliases
     *
     * @return self
     */
    public function addNetwork($name, array $aliases = []): self
    {
        $this->networks[$name] = $aliases;

        return $this;
    }

    /**
     * @param array $networks
     *
     * @return self
     */
    public function setNetworks(array $networks): self
    {
        $this->networks = $networks;

        return $this;
    }
}
