<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArticleStatsCommand extends Command
{
    protected static $defaultName = 'article:stats';

    protected function configure()
    {
        $this
            ->setDescription('Returns some article stats..')
            ->addArgument('slug', InputArgument::REQUIRED, 'The article\'s slug')
            ->addOption('format', null, InputOption::VALUE_REQUIRED, 'The output format', 'text')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $slug = $input->getArgument('slug');
        $data = [
            'slug'=> $slug,
            'heards' => rand(10, 100),
        ];
        
        switch($input->getOption('format')){
            case 'text': 
                //print a list of array items
                // $io->listing($data);


                //print keys and values in table 
                $rows = [];
                foreach ($data as $key => $val){
                    $rows = [ $key, $val];
                }
                $io->table(['Key', 'Value'], $rows);
                
                break;
            case 'json':
                //print json data as raw text
                $io->write(json_encode($data));
                break;
            default:
                throw new \Excetion('What kind of crazy format is that?');
        }
        return 0;
    }
}
