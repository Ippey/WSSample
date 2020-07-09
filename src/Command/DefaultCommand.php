<?php

namespace App\Command;

use Aws\ApiGatewayManagementApi\ApiGatewayManagementApiClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DefaultCommand extends Command
{
    protected static $defaultName = 'app:default';

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('connection_id', InputArgument::REQUIRED)
            ->addArgument('domain', InputArgument::REQUIRED)
            ->addArgument('stage', InputArgument::REQUIRED)
            ->addArgument('message', InputArgument::OPTIONAL, 'Argument description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $connectionId = $input->getArgument('connection_id');
        $message = json_decode($input->getArgument('message') ?? "{data: 'hoge'}");
        $endpoint = 'https://' . $input->getArgument('domain') . '/' . $input->getArgument('stage') . '/';
        $client = new ApiGatewayManagementApiClient([
            'version' => '2018-11-29',
            'endpoint' => $endpoint,
            'region' => 'ap-northeast-1',
        ]);
        $db = new \Aws\DynamoDb\DynamoDbClient([
            'version' => 'latest',
            'region' => 'ap-northeast-1',
        ]);
        $marshaller = new \Aws\DynamoDb\Marshaler();

        $result = $db->scan(['TableName' => 'connections', 'ProjectionExpression' => 'id']);
        foreach ($result['Items'] as $row) {
            $connection = $marshaller->unmarshalItem($row);
            try {
                $client->postToConnection([
                    'ConnectionId' => $connection['id'],
                    'Data' => $message->data,
                ]);
            } catch (\Exception $e) {
                print_r($e->getMessage());
//                $db->deleteItem(['TableName' => 'connection']);
//                $client->deleteConnection(['ConnectionId' => $connection['id']]);
            }
        }
        $result = [
            'statusCode' => 200,
        ];
        $io->writeln(json_encode($result, JSON_PRETTY_PRINT));

        return 0;
    }
}
