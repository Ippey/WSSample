<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ConnectCommand extends Command
{
    protected static $defaultName = 'app:connect';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('connection_id', InputArgument::OPTIONAL)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connectionId = $input->getArgument('connection_id');
        $db = new \Aws\DynamoDb\DynamoDbClient([
            'version' => 'latest',
            'region' => 'ap-northeast-1',
        ]);
        $marshaller = new \Aws\DynamoDb\Marshaler();
        $item = $marshaller->marshalItem([
            'id' => $connectionId
        ]);
        $db->putItem([
            'TableName' => 'connections',
            'Item' => $item,
        ]);

        $result = [
            'statusCode' => 200,
        ];
        $io->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return 0;
    }
}
