<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerCommandsTrait
{
    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @param string $command
     *
     * @return self
     */
    public function addCommand(string $command): self
    {
        $this->commands[] = $command;

        return $this;
    }

    /**
     * @param array $commands
     *
     * @return self
     */
    public function addConsecutiveCommands(array $commands): self
    {
        return $this->addCommand(implode(" && \\\n    ", $commands));
    }
}
