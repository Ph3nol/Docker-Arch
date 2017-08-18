<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\Service\Model\Service;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class NginxDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        $this
            ->setFrom('nginx:1-alpine')
            ->setWorkingDir('/apps');

        $service = $this->getService();

        // Templated files.
        $service->addTemplatedFile(new TemplatedFile(
            'conf.d/000-default.conf',
            'Service/Nginx/configs/default.conf.twig'
        ));
        $hasGeneratedVhosts = $this->addVhostsTemplatedFiles();

        // Commands
        $this->addCommand('rm /etc/nginx/conf.d/default.conf');

        // Copy entries.
        if (true === $hasGeneratedVhosts) {
            $this->addCopyEntry([
                'local' => 'conf.d/*',
                'remote' => '/etc/nginx/conf.d/',
            ]);
        }
    }

    /**
     * @return boolean
     */
    private function addVhostsTemplatedFiles(): bool
    {
        $projectService = $this->getService();

        $vhostsServicesByHost = [];
        foreach ($projectService->getProject()->getServices() as $k => $service) {
            $isCliOnly = $service->getOptions()['cliOnly'] ?? false;
            if (false === $isCliOnly && null !== $service->getHost()) {
                $vhostsServicesByHost[$service->getHost()] = $service;
            }
        }

        $hasGeneratedVhosts = false;
        $vhostIndex = 0;
        foreach ($vhostsServicesByHost as $host => $service) {
            $vhostName = $service->getName();
            $appType = $service->getOptions()['appType'] ?? null;
            $templatePath = sprintf(
                'Service/Nginx/vhosts/%s%s.conf.twig',
                $vhostName,
                $appType ? '-'.$appType : null
            );
            $filePath = sprintf(
                'conf.d/%s-%s.vhost.conf',
                str_pad($vhostIndex + 10, 3, '0', STR_PAD_LEFT),
                $service->getIdentifier()
            );

            $projectService->addTemplatedFile(new TemplatedFile($filePath, $templatePath, [
                'forService' => $service,
            ]));

            ++$vhostIndex;
            $hasGeneratedVhosts = true;
        }

        return $hasGeneratedVhosts;
    }
}
