<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerPackagesTrait
{
    /**
     * @var array
     */
    protected $packages = [];

    /**
     * @return array
     */
    public function getPackages(): array
    {
        return $this->packages;
    }

    /**
     * @param array $names
     *
     * @return self
     */
    public function addPackages(array $names): self
    {
        foreach ($names as $name) {
            $this->addPackage($name);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function addPackage(string $name): self
    {
        if (false === in_array($name, $this->packages)) {
            $this->packages[] = $name;
        }

        return $this;
    }
}
