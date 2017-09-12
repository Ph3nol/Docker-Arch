<?php

namespace Ph3\DockerArch\Domain\DockerContainer\Model;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
trait DockerContainerCopyEntriesTrait
{
    /**
     * @var array
     */
    protected $copyEntries = [];

    /**
     * @return array
     */
    public function getCopyEntries(): array
    {
        return $this->copyEntries;
    }

    /**
     * @param array $copyEntry
     *
     * @return self
     */
    public function addCopyEntry(array $copyEntry): self
    {
        $this->copyEntries[] = $copyEntry;

        return $this;
    }

    /**
     * @param string $copyEntry
     *
     * @return self
     */
    public function addCopyEntryFromString(string $copyEntry): self
    {
        $copyEntry = explode(':', $copyEntry);
        $this->addCopyEntry([
            'local' => $copyEntry[0],
            'remote' => $copyEntry[1],
        ]);

        return $this;
    }

    /**
     * @param array $copyEntries
     *
     * @return self
     */
    public function setCopyEntries(array $copyEntries): self
    {
        $this->copyEntries = $copyEntries;

        return $this;
    }
}
