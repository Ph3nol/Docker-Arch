<?php

namespace Ph3\DockerArch\Infrastructure\Common;

use Ph3\DockerArch\Infrastructure\Common\Persistence\PersisterInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var PersisterInterface
     */
    protected $persister;

    /**
     * @param PersisterInterface $persister
     */
    public function __construct(PersisterInterface $persister)
    {
        $this->persister = $persister;
    }

    /**
     * {@inheritdoc}
     */
    public function getPersister(): PersisterInterface
    {
        return $this->persister;
    }
}
