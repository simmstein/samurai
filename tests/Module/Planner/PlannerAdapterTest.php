<?php
namespace Samurai\Module\Planner;

use Pimple\Container;
use Samurai\Module\Module;
use Samurai\Module\Modules;
use Samurai\Task\ITask;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class PlannerAdapterTest
 * @package Samurai\Module\Planner
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class PlannerAdapterTest extends \PHPUnit_Framework_TestCase
{

    public function testExecute()
    {
        $modules = new Modules();

        $moduleA = new Module();
        $moduleA->setTasks([
            'Samurai\Module\resources\TaskA',
            'Samurai\Module\resources\TaskB',
        ]);
        $modules[] = $moduleA;

        $moduleB = new Module();
        $moduleB->setTasks([
            'Samurai\Module\resources\TaskC',
        ]);
        $modules[] = $moduleB;


        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $adapter = new PlannerAdapter(new ModulesPlannerBuilder(new Container(), $modules));
        $this->assertSame(ITask::NO_ERROR_CODE, $adapter->execute($input, $output));
        $this->assertSame('ABC', $output->fetch());

    }
}
