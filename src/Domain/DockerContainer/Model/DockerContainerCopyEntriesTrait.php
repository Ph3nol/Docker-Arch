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
    protected $copy_entries = [];

    /**
     * @return array
     */
    public function getCopyEntries(): array
    {
        return $this->copy_entries;
    }

    /**
     * @param array $copyEntry
     *
     * @return self
     */
    public function addCopyEntry(array $copyEntry): self
    {
        $this->copy_entries[] = $copyEntry;

        return $this;
    }

    /**
     * @param array $copy_entries
     *
     * @return self
     */
    public function setCopyEntries(array $copy_entries): self
    {
        $this->copy_entries = $copy_entries;

        return $this;
    }
}
