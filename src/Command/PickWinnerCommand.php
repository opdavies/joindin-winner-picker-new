<?php

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PickWinnerCommand extends Command
{
    protected static $defaultName = 'app:pick-winner';

    private $guzzle;

    public function __construct()
    {
      parent::__construct();
        $this->guzzle = new Client();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('tag', InputArgument::REQUIRED, 'The joind.in tag')
            ->addArgument('start', InputArgument::OPTIONAL, '', 'first day of last month')
            ->addArgument('end', InputArgument::OPTIONAL, '', 'last day of this month')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $tag = $input->getArgument('tag');
        $startDate = (new \DateTime($input->getArgument('start')))->format('Y-m-d');
        $endDate = (new \DateTime($input->getArgument('end')))->format('Y-m-d');

        var_dump([
          'tag' => $tag,
          'start date' => $startDate,
          'end date' => $endDate,
        ]);

//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
