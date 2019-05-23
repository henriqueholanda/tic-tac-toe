<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OpenApi;

class GenerateDoc extends Command
{
    /** @var string */
    private $path = __DIR__ . '/../../../openapi.json';

    /** @var string */
    protected static $defaultName = 'app:generate-doc';

    /**
     * @return void
     */
    protected function configure() : void
    {
        $this->setDescription('Generate documentation in OpenAPI format.');
        $this->setHelp('This command will help you to generate the API documentation.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $output->writeln([
            'Generating doc',
            '=============='
        ]);
        $this->generate();
        $output->writeln('Done!');
    }

    /**
     * @return void
     */
    private function generate() : void
    {
        $openapi = OpenApi\scan(__DIR__ . '/../');
        file_put_contents($this->path, $openapi->toJson());
    }
}
