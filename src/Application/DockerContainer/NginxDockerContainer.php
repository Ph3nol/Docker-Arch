<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Application\DockerContainerInflector;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
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
        $this->setPackageManager(DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK);

        parent::init();

        $this->setFrom('nginx:1-alpine');
        $this->applyWebServiceWorkingDir();

        $service = $this->getService();
        $project = $service->getProject();

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

        // Volumes.
        $project->addEnv('NGINX_LOGS_LOCATION', Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/logs/nginx');
        $this
            ->addVolume([
                'local' => '${'.$project->generateEnvKey('NGINX_LOGS_LOCATION').'}',
                'remote' => '/var/log/nginx',
                'type' => 'rw',
            ]);

        // Ports.
        $project->addEnv('NGINX_PORT', ('77'.rand(11, 99)));
        $this->addPort('${'.$project->generateEnvKey('NGINX_PORT').'}', '80');
    }

    /**
     * @return boolean
     */
    private function addVhostsTemplatedFiles(): bool
    {
        $dockerContainerService = $this->getService();

        $vhostsServicesByHost = [];
        foreach ($dockerContainerService->getProject()->getServices() as $k => $service) {
            $isCliOnly = $service->getOptions()['cli_only'] ?? false;
            if (false === $isCliOnly && null !== $service->getHost()) {
                $vhostsServicesByHost[$service->getHost()] = $service;
            }
        }

        $hasGeneratedVhosts = false;
        $vhostIndex = 0;
        foreach ($vhostsServicesByHost as $host => $service) {
            $appType = $service->getOptions()['app_type'] ?? null;
            $templatePath = sprintf(
                'Service/Nginx/vhosts/%s%s.conf.twig',
                $service->getName(),
                $appType ? '-'.$appType : null
            );
            $filePath = sprintf(
                'conf.d/%s-%s%s.vhost.conf',
                str_pad($vhostIndex + 10, 3, '0', STR_PAD_LEFT),
                $service->getIdentifier(),
                $appType ? '-'.$appType : null
            );

            $dockerContainerService->addTemplatedFile(new TemplatedFile($filePath, $templatePath, [
                'forService' => $service,
            ]));

            ++$vhostIndex;
            $hasGeneratedVhosts = true;
        }

        return $hasGeneratedVhosts;
    }
}
