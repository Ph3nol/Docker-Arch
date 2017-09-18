<?php

namespace Ph3\DockerArch\UI\Command;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Ph3\DockerArch\Application\Architect;
use Ph3\DockerArch\Application\TemplatedFileGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class GenerateCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('generate')
            ->setDescription('Generate Docker configuration.')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                sprintf(
                    'Project local path, containing %s configuration file.',
                    Architect::PROJECT_CONFIG_FILENAME
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templatedFileGenerator = new TemplatedFileGenerator();

        $logger = new Logger('Docker-Arch.command.generate');
        $simpleLineFormatter = new LineFormatter("    • %message%\n");

        $logger->pushHandler(
            new StreamHandler(Architect::GLOBAL_ABSOLUTE_TMP_DIRECTORY.'/docker-arch.log', Logger::DEBUG)
        );

        $stdOutHandler = new StreamHandler('php://stdout', Logger::INFO);
        $stdOutHandler->setFormatter($simpleLineFormatter);
        $logger->pushHandler($stdOutHandler);

        $output->writeln("Docker-Arch - Environment generator\n");

        $architect = new Architect($templatedFileGenerator, $logger);
        $architect->generate($input->getArgument('path') ? : getcwd());

        $output->writeln("\n<info>Your Docker environment has been successfully generated!</info>\n");
        $output->writeln(sprintf("Configuration path: <comment>%s</comment>", $architect->getProjectDir()));
        $output->writeln(sprintf("UI HTML file: <comment>%s</comment>\n", $architect->getGeneratedUIPath()));

        $output->writeln(sprintf(
            "Let's use <comment>%s/do</comment> script for doing amazing things! :)",
            substr(Architect::PROJECT_CONFIG_DIRECTORY, 1)
        ));
    }
}
