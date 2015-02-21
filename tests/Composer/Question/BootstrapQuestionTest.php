<?php
namespace Samurai\Composer\Question;

use Pimple\Container;
use Samurai\Composer\Composer;
use Samurai\Composer\Project;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;
use TRex\Cli\Executor;

/**
 * Class BootstrapQuestionTest
 * @package Samurai\Composer\Question
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class BootstrapQuestionTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteEmpty()
    {
        $input = $this->provideInput([]);
        $output = new BufferedOutput();
        $services = $this->provideServices();

        $question = new BootstrapQuestion($services);
        $this->assertFalse($question->execute($input, $output));
        $this->assertSame('', $services['composer']->getproject()->getBootstrapName());
        $this->assertSame('', $services['composer']->getproject()->getBootstrapVersion());
    }

    public function testExecuteValid()
    {
        $input = $this->provideInput(['bootstrap' => 'vendor/package']);
        $output = new BufferedOutput();
        $services = $this->provideServices();

        $question = new BootstrapQuestion($services);
        $this->assertTrue($question->execute($input, $output));
        $this->assertSame('vendor/package', $services['composer']->getproject()->getBootstrapName());
        $this->assertSame('', $services['composer']->getproject()->getBootstrapVersion());
    }

    public function testExecuteWithVersion()
    {
        $input = $this->provideInput(['bootstrap' => 'vendor/package', 'version' => '1.0.0']);
        $output = new BufferedOutput();
        $services = $this->provideServices();

        $question = new BootstrapQuestion($services);
        $this->assertTrue($question->execute($input, $output));
        $this->assertSame('vendor/package', $services['composer']->getproject()->getBootstrapName());
        $this->assertSame('1.0.0', $services['composer']->getproject()->getBootstrapVersion());
    }

    /**
     * @return Container
     */
    private function provideServices()
    {
        $services = new Container();
        $services['composer'] = function () {
            return new Composer(new Project(), new Executor());
        };
        return $services;
    }

    /**
     * @param array $args
     * @return ArrayInput
     */
    private function provideInput(array $args)
    {
        return new ArrayInput(
            $args,
            new InputDefinition([
                new InputArgument('bootstrap'),
                new InputArgument('version')
            ])
        );
    }
}