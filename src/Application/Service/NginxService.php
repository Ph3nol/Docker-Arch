<?php

namespace Ph3\DockerArch\Application\Service;

use Ph3\DockerArch\Domain\Service\Model\AbstractService;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NginxService extends AbstractService
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
    public function allowedLinksExpression(): ?string
    {
        return '(php|nodejs|atmo|cerebro|elasticsearchHead)';
    }

    /**
     * {@inheritdoc}
     */
    public function isWebService(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isVhostService(): bool
    {
        return true;
    }
}
