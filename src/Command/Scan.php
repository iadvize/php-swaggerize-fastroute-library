<?php

namespace Iadvize\SwaggerizeFastRoute\Command;

use Iadvize\SwaggerizeFastRoute\OperationParser\LumenControllerOperationParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Scan
 *
 * @package Iadvize\SwaggerizeFastRoute\Command
 */
class Scan extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('swagger:scan')
            ->setDescription('Scan swagger JSON file ')
            ->addArgument(
                'swaggerFile',
                InputArgument::REQUIRED,
                'Give JSON file to scan'
            )
            ->addArgument(
                'controllerNamespace',
                InputArgument::REQUIRED,
                'Controllers namespace that will handle route'
            )
            ->addOption(
                'routeFile',
                null,
                InputOption::VALUE_REQUIRED,
                'Where FastRoute cache file should be write ? Default to output'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controllerNamespace = $input->getArgument('controllerNamespace');
        $swaggerFile         = $input->getArgument('swaggerFile');
        $routeStream         = $input->getOption('routeFile');

        if (!$routeStream) {
            $routeStream = 'php://output';
        }

        $operationParser = new LumenControllerOperationParser($controllerNamespace);

        $routes = \Iadvize\SwaggerizeFastRoute\scan($swaggerFile, $operationParser);

        \Iadvize\SwaggerizeFastRoute\cacheRoutes($routes, $routeStream);

        if ($routeStream !== 'php://output') {
            $output->writeln('route file available at ' . $routeStream);
        }
    }
}
