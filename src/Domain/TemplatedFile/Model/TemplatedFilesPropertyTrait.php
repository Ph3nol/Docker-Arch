<?php

namespace Ph3\DockerArch\Domain\TemplatedFile\Model;

use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait TemplatedFilesPropertyTrait
{
    /**
     * @var TemplatedFileInterface[]
     */
    private $templatedFiles = [];

    /**
     * @return TemplatedFile[]
     */
    public function getTemplatedFiles(): array
    {
        return $this->templatedFiles;
    }

    /**
     * @param array $templatedFiles
     *
     * @return self
     */
    public function setTemplatedFiles(array $templatedFiles): self
    {
        $this->templatedFiles = $templatedFiles;

        return $this;
    }

    /**
     * @param TemplatedFileInterface $templatedFile
     *
     * @return self
     */
    public function addTemplatedFile(TemplatedFileInterface $templatedFile): self
    {
        $this->templatedFiles[] = $templatedFile;

        return $this;
    }
}
