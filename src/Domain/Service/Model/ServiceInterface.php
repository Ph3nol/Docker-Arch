<?php

namespace Ph3\DockerArch\Domain\Service\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface ServiceInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string;
}
