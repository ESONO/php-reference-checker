<?php


namespace umulmrum\PhpReferenceChecker\Command;


use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use umulmrum\PhpReferenceChecker\Checker\ReferenceAssignmentChecker;
use umulmrum\PhpReferenceChecker\Runner\Runner;

class CheckReferenceAssignmentsCommand extends Command
{
    /**
     * @var Runner
     */
    private $runner;

    /**
     * @param Runner $runner
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;

        parent::__construct();
    }


    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('reference-checker:check')
            ->setDefinition(array(
                new InputArgument('targetFilePath', InputArgument::REQUIRED, 'The path to check (file or directory)'),
                new InputArgument('classRepositoryPath', InputArgument::REQUIRED, 'The root path of the files containing method calls definitions.'),
            ))
            ->setDescription('Searches for assignments by reference of method return values that are not references but values.')
            ->setHelp(<<<EOF
The <info>%command.name%</info> searches for assignments by reference of method return values that are not references but values.
EOF
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $targetFilePath = $input->getArgument('targetFilePath')) {
            throw new InvalidArgumentException('targetFilePath argument is required.');
        }

        if (null === $classRepositoryPath = $input->getArgument('classRepositoryPath')) {
            throw new InvalidArgumentException('classRepositoryPath argument is required.');
        }

        $warnings = $this->runner->runCheck($targetFilePath, $classRepositoryPath);

        if (0 === count($warnings)) {
            $output->writeln('No invalid reference assignments found.');

            return;
        }

        $table = new Table($output);
        $table->setHeaders(['File', 'Line', 'Probability']);
        foreach ($warnings as $warning) {
            $table->addRow([
                $warning->getFile(),
                $warning->getLine(),
                number_format($warning->getProbability(), 2),
            ]);
        }

        $table->render();
    }
}