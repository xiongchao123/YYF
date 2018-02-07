<?php

namespace App\Console\Command;

use Psr\Log\LogLevel;
use Resque\Resque;
use Resque\Resque\ResqueLog;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueController extends Command
{
    protected function configure()
    {
        $this->addOption(
            'queue',
            '',
            InputArgument::OPTIONAL,
            'queue name,Multiple queue separate ,,default is * '
        );
        $this->addOption(
            'interval',
            '',
            InputArgument::OPTIONAL,
            'Round robin interval default 5 seconds'
        );
        $this->addOption(
            'blocking',
            'block',
            InputArgument::OPTIONAL,
            'Whether blocking(0,1)'
        );
        $this->addOption(
            'process',
            '',
            InputArgument::OPTIONAL,
            'process num'
        );
        $this->addOption(
            'loglevel',
            'log',
            InputArgument::OPTIONAL,
            'log level (0,1)'
        );
        $this->addOption(
            'pidfile',
            'pid',
            InputArgument::OPTIONAL,
            'pidfile'
        );
        $this->setName('queue:work');
        $this->setHelp("queue:work --queue=queue_name --interval=time --blocking=0/1 --process=num --loglevel=0/1 --pidfile=filepath");
        $this->setDescription("start worker to listen queue jobs");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //php version > 7.0
        $QUEUE = $input->getOption('queue') ?? "*";
        $interval = $input->getOption('interval') ?? 5;
        $BLOCKING = $input->getOption('blocking') ?? false;
        $logLevel = $input->getOption('loglevel') ?? true;
        $count = $input->getOption('process') ?? 1;
        $PIDFILE = $input->getOption('pidfile') ?? null;
        $queue_conf=require_once APP_PATH."/conf/queue.php";
        $host=$queue_conf['host'] ?? '127.0.0.1';
        $port=$queue_conf['port'] ?? 6379;
        $password=$queue_conf['password'] ?? null;
        $database =  $queue_conf['database'] ?? 0;
        //url  or  array=>cluster  or another param for client
        Resque::setBackend("redis://:$password@$host:$port",$database);
        $logger = new ResqueLog($logLevel);
        if(isset($queue_conf['prefix']) && $queue_conf['prefix']){
            $logger->log(LogLevel::INFO, 'Prefix set to {prefix}', array('prefix' => $queue_conf['prefix']));
            Resque\ResqueRedis::prefix($queue_conf['prefix']);
        }


        if ((int)$count > 1) {
            for ($i = 0; $i < $count; ++$i) {
                $pid = Resque::fork();
                if ($pid === false || $pid === -1) {
                    $logger->log(LogLevel::EMERGENCY, 'Could not fork worker {count}', array('count' => $i));
                    die();
                } // Child, start the worker
                else if (!$pid) {
                    $queues = explode(',', $QUEUE);
                    $worker = new Resque\ResqueWorker($queues);
                    $worker->setLogger($logger);
                    $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
                    $worker->work($interval, $BLOCKING);
                    break;
                }
            }
        } else {
            // Start a single worker
            $queues = explode(',', $QUEUE);
            $worker = new Resque\ResqueWorker($queues);
            $worker->setLogger($logger);
            if ($PIDFILE) {
                file_put_contents($PIDFILE, getmypid()) or
                die('Could not write PID information to ' . $PIDFILE);
            }

            $logger->log(LogLevel::NOTICE, 'Starting worker {worker}', array('worker' => $worker));
            $worker->work($interval, $BLOCKING);
        }
    }

}