<?php

namespace Ph3\DockerArch\Application;

use Ph3\DockerArch\Domain\Project\Model\ProjectInterface;
use Ph3\DockerArch\Domain\TemplatedFile\Model\TemplatedFile;
use Ph3\DockerArch\Infrastructure\Common\Persistence\Exception\NoPersisterFileException;
use Ph3\DockerArch\Infrastructure\Common\Persistence\Persister;
use Ph3\DockerArch\Infrastructure\Common\Persistence\PersisterInterface;
use Ph3\DockerArch\Infrastructure\Project\Repository\ProjectRepository;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Cédric Dugat <cedric@dugat.me>
 */
class Architect implements ArchitectInterface
{
    public const PROJECT_NAME = 'Docker Arch';
    public const PROJECT_URL = 'https://github.com/ph3nol/docker-arch';
    public const PROJECT_CONFIG_FILENAME = '/.docker-arch.yml';
    public const PROJECT_CONFIG_DIRECTORY = '/.docker-arch';
    public const PROJECT_TMP_CONFIG_DIRECTORY = '/.docker-arch.tmp';
    public const GLOBAL_ABSOLUTE_TMP_DIRECTORY = '/tmp/.docker-arch';

    /**
     * @var TemplatedFileGeneratorInterface
     */
    protected $templatedFileGenerator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var string
     */
    private $projectPath;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var ProjectInterface
     */
    private $project;

    /**
     * @var PersisterInterface
     */
    private $persister;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $tmpBuildDir;

    /**
     * @var string
     */
    private $generatedUIPath;

    /**
     * @param TemplatedFileGeneratorInterface $templatedFileGenerator
     * @param LoggerInterface                 $logger
     */
    public function __construct(TemplatedFileGeneratorInterface $templatedFileGenerator, LoggerInterface $logger = null)
    {
        define('PROJECT_ROOT_DIR', __DIR__.'/../..');
        define('PROJECT_APP_DIR', PROJECT_ROOT_DIR.'/app');
        define('PROJECT_SRC_DIR', PROJECT_ROOT_DIR.'/src');

        $this->templatedFileGenerator = $templatedFileGenerator;
        $this->logger = $logger ?: new NullLogger();
        $this->fs = new Filesystem();
    }

    /**
     * @return TemplatedFileGeneratorInterface
     */
    public function getTemplatedFileGenerator(): TemplatedFileGeneratorInterface
    {
        return $this->templatedFileGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($projectPath): void
    {
        $this->logger->info('Initializing project...');
        $this->initProject($projectPath);

        $this->tmpBuildDir = $this->projectPath.self::PROJECT_TMP_CONFIG_DIRECTORY;
        $this->fs->remove($this->tmpBuildDir);

        $this->logger->info('Generating project files...');
        $this->generateMainProjectFiles();
        $this->logger->info('Dumping project files...');
        $this->dumpMainProjectFiles();
        $this->logger->info('Generating/Dumping project services files...');
        $this->generateAndDumpProjectServicesFiles();

        $this->projectDir = $this->projectPath.self::PROJECT_CONFIG_DIRECTORY;

        // Backup some resources.
        $dotEnvFilePath = $this->projectDir.'/.env';
        $initialDotEnvContent = null;
        if (file_exists($dotEnvFilePath)) {
            $initialDotEnvContent = file_get_contents($dotEnvFilePath);
        }

        $this->logger->info('Exporting configuration...');
        // Export Docker configuration and clean.
        $this->fs->remove($this->projectDir);
        $this->fs->mirror($this->tmpBuildDir, $this->projectDir);
        $this->fs->remove($this->tmpBuildDir);

        $this->logger->info('Creating .env/.env.dist files...');
        // Recreate backuped resources.
        if (null !== $initialDotEnvContent) {
            $this->fs->dumpFile($dotEnvFilePath, $initialDotEnvContent);
        } else {
            $this->fs->dumpFile($dotEnvFilePath, file_get_contents($this->projectDir.'/.env.dist'));
        }

        $this->logger->info('Generating UI...');
        $this->generatedUIPath = $this->projectDir.'/index.html';
        $this->generateUI();
    }

    /**
     * @return ProjectInterface
     */
    public function getProject(): ?ProjectInterface
    {
        return $this->project;
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return $this->projectDir;
    }

    /**
     * @return string
     */
    public function getGeneratedUIPath(): string
    {
        return $this->generatedUIPath;
    }

    /**
     * @param string $projectPath
     *
     * @return void
     */
    private function initProject($projectPath): void
    {
        $this->projectPath = $projectPath;

        try {
            $this->persister = new Persister($this->projectPath);
        } catch (NoPersisterFileException $e) {
            $this->persister = Persister::init($this->projectPath);
        }

        $this->project = (new ProjectRepository($this->persister))->getProject();
        $this->project->setPath($this->projectPath);
    }

    /**
     * @return void
     */
    private function generateMainProjectFiles(): void
    {
        $templatedFiles = [
            new TemplatedFile('.gitignore', 'Base/gitignore.twig'),
            new TemplatedFile('.env.dist', 'Base/env.dist.twig'),
            new TemplatedFile('Makefile', 'Base/Makefile.twig'),
            new TemplatedFile('docker-compose.yml', 'Base/docker-compose.yml.twig'),
            new TemplatedFile('do', 'Base/do.twig', [], 0755),
        ];

        foreach ($templatedFiles as $templatedFile) {
            $this->project->addTemplatedFile($templatedFile);
            $this->logger->debug('    + '.$templatedFile->getRemotePath().' file added');
        }
        if (0 < count($this->project->getDockerSynchedServices())) {
            $templatedFile = new TemplatedFile('docker-sync.yml', 'Base/docker-sync.yml.twig');
            $this->project->addTemplatedFile($templatedFile);
            $this->logger->debug('    + '.$templatedFile->getRemotePath().' file added (Docker-Sync enabled)');
        }
    }

    /**
     * @return void
     */
    private function dumpMainProjectFiles(): void
    {
        foreach ($this->project->getTemplatedFiles() as $templatedFile) {
            $fileContent = $this->getTemplatedFileGenerator()->render($templatedFile->getViewPath(), array_merge(
                ['project' => $this->project],
                $templatedFile->getParameters()
            ));
            $this->fs->dumpFile($this->tmpBuildDir.'/'.$templatedFile->getRemotePath(), $fileContent);
            if ($chmod = $templatedFile->getChmod()) {
                $this->fs->chmod($this->tmpBuildDir.'/'.$templatedFile->getRemotePath(), $chmod);
            }
            $this->logger->debug('    + '.$templatedFile->getRemotePath().' dumped');
        }
    }

    /**
     * @return void
     */
    private function generateAndDumpProjectServicesFiles(): void
    {
        foreach ($this->project->getServices() as $service) {
            $this->logger->debug('    @ Service '.$service);
            $serviceTmpBuildDir = sprintf('%s/%s', $this->tmpBuildDir, $service->getIdentifier());

            $templatedFile = new TemplatedFile('Dockerfile', 'Base/Service/Dockerfile.twig');
            $service->addTemplatedFile($templatedFile);
            $this->logger->debug('        + '.$templatedFile->getRemotePath().' added');

            foreach ($service->getTemplatedFiles() as $templatedFile) {
                $fileContent = $this->getTemplatedFileGenerator()->render($templatedFile->getViewPath(), array_merge(
                    ['project' => $this->project, 'service' => $service],
                    $templatedFile->getParameters()
                ));
                $this->fs->dumpFile($serviceTmpBuildDir.'/'.$templatedFile->getRemotePath(), $fileContent);
                if ($chmod = $templatedFile->getChmod()) {
                    $this->fs->chmod($serviceTmpBuildDir.'/'.$templatedFile->getRemotePath(), $chmod);
                }
                $this->logger->debug('        + '.$templatedFile->getRemotePath().' dumped');
            }
        }
    }

    /**
     * @return void
     */
    private function generateUI(): void
    {
        $indexContent = $this->getTemplatedFileGenerator()->render('UI/index.html.twig', [
            'projectDir' => $this->projectDir,
            'project' => $this->project,
            'dotEnvFileContent' => file_get_contents($this->projectDir.'/.env'),
        ]);

        $this->fs->dumpFile($this->getGeneratedUIPath(), $indexContent);
        $this->logger->debug('    + Dumped into '.$this->getGeneratedUIPath());
    }
}
