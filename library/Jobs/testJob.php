<?php

namespace Jobs;

class testJob
{
	public function perform()
	{
        fwrite(STDOUT, 'Start job! -> ');
        fwrite(STDOUT, 'job argus: '.var_export($this->args,true));
		sleep(1);
		fwrite(STDOUT, 'Job ended!' . PHP_EOL);
	}

	public function tearDown(){
        sleep(1);
        fwrite(STDOUT, 'tearDown!' . PHP_EOL);
    }
}
