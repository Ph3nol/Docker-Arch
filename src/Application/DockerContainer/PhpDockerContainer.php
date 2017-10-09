<?php

namespace Ph3\DockerArch\Application\DockerContainer;

use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainer;
use Ph3\DockerArch\Domain\DockerContainer\Model\DockerContainerInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class PHPDockerContainer extends DockerContainer
{
    /**
     * {@inheritdoc}
     */
    public function getPackageManager(): string
    {
        return DockerContainerInterface::PACKAGE_MANAGER_TYPE_APK;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): void
    {
        $service = $this->getService();

        $this->setFrom(sprintf('php:%s-alpine', $service->getOptions()['version']));
        $this->applyWebServiceConfiguration();
        $this->applyShellConfiguration();

        $service
            ->addTemplatedFile(new TemplatedFile(
                'entrypoint.sh',
                'Service/Php/entrypoint.sh.twig'
            ));
        $this
            ->addCopyEntry([
                'local' => 'entrypoint.sh',
                'remote' => '/root/entrypoint.sh',
            ])
            ->addCommand('chmod +x /root/entrypoint.sh')
            ->setEntryPoint(['/root/entrypoint.sh']);

        // Volumes.
        if (true === $service->getOptions()['dotfiles']) {
            $this->applyDotfiles();
        }

        // Packages.
        $this
            ->addPackage('autoconf')
            ->addPackage('gcc')
            ->addPackage('make')
            ->addPackage('g++')
            ->addPackage('zlib-dev');
        if (true === $service->getOptions()['composer']) {
            $this
                ->addPackage('zip')
                ->addPackage('unzip');
        }

        // Commands.
        $this->addOwnCommands();

        // Envs.
        if (true === $service->getOptions()['composer']) {
            $this
                ->addEnv('COMPOSER_ALLOW_SUPERUSER', '1')
                ->addEnv('COMPOSER_HOME', '/tmp');
        }
    }

    /**
     * @return void
     */
    private function addOwnCommands(): void
    {
        $service = $this->getService();

        $extensions = $service->getOptions()['extensions'];
        if (true === in_array('mysql', $extensions)) {
            unset($extensions[array_search('mysql', $extensions)]);
            $extensions[] = 'pdo_mysql';
        }
        if (true === in_array('xdebug', $extensions)) {
            unset($extensions[array_search('xdebug', $extensions)]);
            $this
                ->addCommand('pecl install xdebug')
                ->addCommand('docker-php-ext-enable xdebug')
                ->addConsecutiveCommands([
                    'echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
                    'echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
                    'echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
                    'echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
                    'echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini',
                ]);
        }
        if (true === in_array('redis', $extensions)) {
            unset($extensions[array_search('redis', $extensions)]);
            $this
                ->addCommand('pecl install redis')
                ->addCommand('docker-php-ext-enable redis');
        }
        if (true === in_array('pdo_mysql', $extensions)) {
            $this
                ->addPackage('libmcrypt-dev')
                ->addPackage('mysql-client');
        }
        if (true === in_array('intl', $extensions)) {
            $this
                ->addPackage('icu')
                ->addPackage('icu-libs')
                ->addPackage('icu-dev');
        }
        foreach (array_unique($extensions) as $extension) {
            $this->addCommand('docker-php-ext-install '.$extension);
        }

        foreach ($service->getOptions()['config'] as $key => $value) {
            $this->addCommand('echo "'.$key.' = '.$value.'" >> /usr/local/etc/php/conf.d/php.ini');

            if (true === $service->getOptions()['cli']) {
                continue;
            }

            $this->addCommand('echo "php_admin_value['.$key.'] = '.$value.'" >> /usr/local/etc/php-fpm.d/www.conf');
        }

        if (true === $service->getOptions()['composer']) {
            $this
                ->addCommand(
                    'curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/bin'
                )
                ->addCommand('composer global require hirak/prestissimo');
        }
    }
}
