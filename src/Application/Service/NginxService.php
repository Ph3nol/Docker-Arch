<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NginxService extends AbstractService implements WebInterface, VhostInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOptionsResolver(): Options
    {
        return new OptionsResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function allowedLinksFqcns(): array
    {
        return [
            PHPService::class,
            PHPNodeService::class,
            NodeJS::class,
            Atmo::class,
            Cebebro::class,
            ElasticsearchHead::class,
        ];
    }
}
