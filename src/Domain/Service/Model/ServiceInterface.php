<?php

namespace Ph3\DockerArch\Domain\Service\Model;

use Symfony\Component\OptionsResolver\Options;

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
