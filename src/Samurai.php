<?php
namespace Samurai;

use Pimple\Container;
use Puppy\Config\Config;
use Samurai\Alias\AliasManager;
use Samurai\alias\Task\Handle;
use Samurai\Command\Alias;
use Samurai\Command\Generate;
use Samurai\Composer\Composer;
use Samurai\Composer\Project;
use Samurai\Composer\Task\Factory\ProjectCreationTaskFactory;
use Samurai\Task\Planner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TRex\Cli\Executor;

/**
 * Class Samurai
 * main console
 *
 * @package Samurai
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class Samurai
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var Executor
     */
    private $executor;

    /**
     * @var Container
     */
    private $services;

    /**
     * @param Application $application
     * @param Container $services
     * @param Executor $executor
     */
    public function __construct(Application $application, Container $services = null, Executor $executor = null)
    {
        $application->setName('Samurai console');
        $application->setVersion('0.0.0');

        $this->setApplication($application);
        $this->setServices($services ? : $this->buildServices());
        $this->setExecutor($executor ? : new Executor());

        $this->initCommands();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return $this->getApplication($input, $output)->run();
    }

    /**
     * Getter of $application
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Getter of $services
     *
     * @return Container
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * Setter of $services
     *
     * @param Container $services
     */
    public function setServices(Container $services)
    {
        $this->services = $services;
    }

    /**
     *
     */
    private function initCommands()
    {
        $this->getApplication()->add(new Generate(new Planner([ //todo déclarer ceci dans la classe
            ProjectCreationTaskFactory::create($this->getServices())
        ])));

        $this->getApplication()->add(new Alias(new Handle($this->getServices())));
    }

    /**
     * Setter of $application
     *
     * @param Application $application
     */
    private function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Getter of $executor
     *
     * @return Executor
     */
    private function getExecutor()
    {
        return $this->executor;
    }

    /**
     * Setter of $executor
     *
     * @param Executor $executor
     */
    private function setExecutor(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @return Container
     */
    private function buildServices()
    {
        $application = $this->getApplication();
        $executor = $this->getExecutor();

        $services = new Container();

        $services['composer'] = function () use ($executor) {
            return new Composer(new Project(), $executor);
        };

        $services['question'] = function () use ($application) {
            return $application->getHelperSet()->get('question');
        };

        $services['config'] = function () {
            return new Config('');
        };

        $services['alias_manager'] = function (Container $services) {
            return new AliasManager($services['config']);
        };

        return $services;
    }
}