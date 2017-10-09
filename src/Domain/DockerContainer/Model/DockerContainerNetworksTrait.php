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
     *
     * @return self
     */
    public function addNetwork($name): self
    {
        if (false === array_key_exists($name, $this->networks)) {
            $this->networks[$name] = [];
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $alias
     *
     * @return self
     */
    public function addNetworkAlias($name, string $alias): self
    {
        if (false === array_key_exists($name, $this->networks)) {
            $this->addNetwork($name);
        }

        if (false === in_array($alias, $this->networks[$name])) {
            $this->networks[$name][] = $alias;
        }

        return $this;
    }

    /**
     * @param string $name
     * @param array  $aliases
     *
     * @return self
     */
    public function addNetworkAliases($name, array $aliases = []): self
    {
        foreach ($aliases as $alias) {
            $this->addNetworkAlias($name, $alias);
        }

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
