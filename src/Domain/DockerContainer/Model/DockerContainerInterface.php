<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface DockerContainerInterface
{
    /**
     * @return void
     */
    public function init(): void;
}
