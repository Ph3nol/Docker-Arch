<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
interface PersisterInterface
{
    /**
     * @return string
     */
    public function read(): string;

    /**
     * @param string $data
     *
     * @return void
     */
    public function write($data): void;
}
