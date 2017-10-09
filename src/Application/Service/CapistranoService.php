<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class CapistranoService extends AbstractService implements CliInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['version']);
        $resolver->setDefaults([
            'version' => '3.9.1',
            'zsh' => true,
            'dotfiles' => true,
        ]);

        return $resolver;
    }
}
