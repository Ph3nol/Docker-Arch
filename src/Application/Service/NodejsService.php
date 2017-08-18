<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NodejsService extends AbstractService
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'cliOnly' => false,
            'dotfiles' => true,
            'zsh' => true,
            'customZsh' => true,
            'bower' => true,
            'gulp' => true,
            'npmPackages' => [],
            'supervisor' => false,
        ]);
        $resolver->setRequired(['version']);
        $resolver->setAllowedValues('version', ['0', '4', '6', '7', '8']);

        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function allowedLinksExpression(): ?string
    {
        return '(mysql|mariadb|redis)';
    }
}
