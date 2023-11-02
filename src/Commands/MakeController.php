<?php

namespace Simply\Mvc\Commands;

use Simply\Maker\Command\AbstractMakeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MakeController extends AbstractMakeCommand
{
    protected static $defaultName = 'simply:make:controller';

    public function configure() {
        parent::configure();
        $this->setDescription('Create a controller.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        // the function set the root path for fileManager
        $this->askRootPath($io, $this->fileManager);
        $fullClassName = $io->askQuestion(new Question('Controller class name', 'Namespace\HomeController'));
        $classNameDetails = $this->generator->createClassNameDetails($fullClassName);
        $targetPath = $this->fileManager->getRootPath() . '/src/Controller/' . $classNameDetails->getClassName() . '.php';
        $fileTemplate = $this->getSkeletonPath('/controller/Controller.tpl.php');

        $this->generator->generateClass($classNameDetails, $targetPath, $fileTemplate);
        $this->generator->writeChanges();
        $io->success('Controller created');
        return Command::SUCCESS;
    }

    public function getSkeletonPath($path): string {
        return __DIR__ . '/../Resources/skeleton' . $path;
    }
}
