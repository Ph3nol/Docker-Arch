<?php

namespace Ph3\DockerArch\Infrastructure\Common\Persistence;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Infrastructure\Common\Persistence\Exception\NoPersisterFileException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\Exception\PersisterFileAlreadyExistsException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class Persister implements PersisterInterface
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->fs = new Filesystem();
        $this->filePath = self::getFilePath($path);
        if (false === $this->fs->exists($this->filePath)) {
            throw new NoPersisterFileException();
        }
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getFilePath($path = null): string
    {
        return $path.Architect::TYPE_PROJECT_CONFIG_FILENAME;
    }

    /**
     * @param string  $path
     * @param boolean $overwrite
     *
     * @return Persister
     */
    public static function init($path, $overwrite = true): PersisterInterface
    {
        $fs = new Filesystem();
        $filePath = self::getFilePath($path);
        if (false === $overwrite && true === $fs->exists($filePath)) {
            throw new PersisterFileAlreadyExistsException();
        }
        $fs->touch($filePath);

        return new Persister($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read(): string
    {
        return file_get_contents($this->filePath);
    }

    /**
     * {@inheritdoc}
     */
    public function write($data): void
    {
        $this->fs->dumpFile($this->filePath, $data);
    }
}
