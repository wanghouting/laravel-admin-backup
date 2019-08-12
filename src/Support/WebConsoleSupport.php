<?php


namespace LTBackup\Extension\Support;


use LTBackup\Extension\Exceptions\WebConsoleException;

class WebConsoleSupport
{
    public function execute_command($command){
        if(!wc_is_empty_string($command)){

            $descriptors = array(
                0 => array('pipe', 'r'), // STDIN
                1 => array('pipe', 'w'), // STDOUT
                2 => array('pipe', 'w')  // STDERR
            );

            $process = proc_open($command . ' 2>&1', $descriptors, $pipes,getcwd());
            if (!is_resource($process)) die("Can't execute command.");

            // Nothing to push to STDIN
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $error = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // All pipes must be closed before "proc_close"
            $code = proc_close($process);
            if($code != 0 ){
                 throw new WebConsoleException($output,$code);
            }
            return ['output'=>$output,'error'=>$error,'code'=>$code];
        }

    }
}