<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Alexis NIVON <anivon@alexisnivon.fr>
 */
class MocoService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired('mock_filename');
        $resolver->setAllowedTypes('mock_filename', 'string');

        return $resolver;
    }
}
