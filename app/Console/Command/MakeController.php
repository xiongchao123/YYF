<?php

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Command
{
    protected function configure()
    {
        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Who do you want to greet?'
        );
        $this->addOption(
            'module',
            'm',
            InputArgument::OPTIONAL,
            'which module? default is Index'
        );
        $this->setName('make:controller');
        $this->setHelp("make:controller \$controler name --module module or -m module");
        $this->setDescription("Create a new controller.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $args = $input->getArguments();
        $name = ucfirst($args['name']);
        $module = $input->getOption('module');
        if ($module) {
            $module = ucfirst($module);
            if ($module === 'Index') {
                $dir = APP_PATH . '/app/controllers';
            } else {
                $dir = APP_PATH . "/app/modules/$module/controllers";
            }
        }else{
            $dir = APP_PATH . '/app/controllers';
        }

        if (!is_dir($dir)) {
            //$output->writeln("<error>not found directory $dir</error>");
            mkdir($dir,0755,true);
        }
        $file = $dir . "/" . $name . '.php';
        if (is_file($file)) {
            $output->writeln("<error>Controller[$name] already exists!</error>");
        } elseif (self::init($name, $file)) {
            $output->writeln("<info>success!</info>");
        } else {
            $output->writeln("<error>file_put_content($file) failed.!</error>");
        }
    }

    static function init($name, $file)
    {
        $code = "<?php\n\n";
        $code .= "class $name extends Yaf_Controller_Abstract\n{\n\n}";
        return file_put_contents($file, $code);
    }
}