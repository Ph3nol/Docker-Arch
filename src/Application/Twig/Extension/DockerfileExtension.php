<?php

namespace Ph3\DockerArch\Application\Twig\Extension;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class DockerfileExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('chained_elements_string', [$this, 'getChainedElementsString'], [
                'pre_escape' => 'html',
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * @param array $elements
     *
     * @return string
     */
    public function getChainedElementsString(array $elements): string
    {
        return '["'.implode('", "', $elements).'"]';
    }
}
