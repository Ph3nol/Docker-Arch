<?php

namespace Ph3\DockerArch\Infrastructure\Common;

use Ph3\DockerArch\Infrastructure\Common\Persistence\PersisterInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface RepositoryInterface
{
    /**
     * @return PersisterInterface
     */
    public function getPersister(): PersisterInterface;
}
