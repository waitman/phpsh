<?php

//$phpsh_db=pg_connect('dbname=test user= password=') or die('no connecto');

$phpsh_mc = new Memcached();
$phpsh_mc->addServer('127.0.0.1',11211);

if (!($phpsh_vars=$phpsh_mc->get('vars'))) {
	/* no vars */
} else {
	$phpsh_d=unserialize($phpsh_vars);
	foreach ($phpsh_d as $phpsh_k=>$phpsh_v) {
		switch ($phpsh_k) {
			case '_GET':	/*fall through*/
			case '_POST': 	/*fall through*/
			case '_COOKIE':	/*fall through*/
			case '_FILES':	/*fall through*/
			case 'argv':	/*fall through*/
			case 'argc':	/*fall through*/
			case '_SERVER':	/*fall through*/
				break;
			default: 
				if (!strstr($phpsh_k,'phpsh')) 
				$$phpsh_k=$phpsh_v;
				break;
		}
	}
}
unset($phpsh_k);
unset($phpsh_v);
unset($phpsh_d);
unset($phpsh_vars);

function help() {
	echo "exit()		Exit the phpsh shell.\n";
	echo "reset_mc()	Erase all stored variables.\n";
	echo "list_vars()	Print all stored variables (with values).\n";
	echo "history()		Show history.\n";
	echo "h(n)		Execute history line n\n";
}

function version() {
	$t=`php --version`;
	echo $t;
}

function history() {
	$history = readline_list_history();
	foreach ($history as $k=>$v) {
		echo '['.($k+1).'] => '.$v."\n";
	}
}

function h($n) {
	$history = readline_list_history();
	$phpsh_line = $history[$n-1];
	echo 'Executing: '.$phpsh_line."\n";
        $phpsh_chk_fun = str_replace(';','',$phpsh_line);
        $phpsh_t = explode('(',$phpsh_chk_fun);
        $phpsh_chk_fun = array_shift($phpsh_t);

        if (function_exists($phpsh_chk_fun)) {
                echo eval($phpsh_line);
        } else {
        /* is it safe? eval will crash the thing if it gets some bad php */
        $phpsh_file = '/tmp/'.rand(0,99999);
        touch($phpsh_file);
        chmod($phpsh_file,600);
        $phpsh_fp=fopen($phpsh_file,'w');
        fwrite($phpsh_fp,'<?php eval("'.str_replace('"',"\\\"",$phpsh_line).'");?>');
        fclose($phpsh_fp);
        $phpsh_t=`/usr/bin/env php -q $phpsh_file 2>&1`;
        unlink($phpsh_file);
        if (stristr($phpsh_t,'fatal')) {
                $phpsh_t = str_replace($phpsh_file.'(1)','the command.',$phpsh_t);
                $phpsh_t = str_replace(": eval()'d code on line 1",'',$phpsh_t);
                echo $phpsh_t;
        } else {
                echo eval($phpsh_line);
        }
        }
}

function reset_mc() {
	$phpsh_mc = new Memcached();
	$phpsh_mc->addServer('127.0.0.1',11211);
	$phpsh_mc->set('vars','',0);
	echo "Warning: Memcached vars erased.\n";
}

function list_vars() {
	$phpsh_mc = new Memcached();
	$phpsh_mc->addServer('127.0.0.1',11211);
	if (!($phpsh_vars=$phpsh_mc->get('vars'))) {
		echo "Nothing Stored.\n";
	} else {
	        $phpsh_d=unserialize($phpsh_vars);
	        foreach ($phpsh_d as $phpsh_k=>$phpsh_v) {
	                switch ($phpsh_k) {
	                        case '_GET':    /*fall through*/
	                        case '_POST':   /*fall through*/
	                        case '_COOKIE': /*fall through*/
	                        case '_FILES':  /*fall through*/
	                        case 'argv':    /*fall through*/
	                        case 'argc':    /*fall through*/
	                        case '_SERVER': /*fall through*/
	                                break;
	                        default:
	                               if (is_array($phpsh_v)) {
						echo 'Array $'.$phpsh_k."\n";
						print_r($phpsh_v);
					} else {
						if (!is_object($phpsh_v)) {
						try {
							echo '$'.$phpsh_k.' = '.($phpsh_v)."\n";
						} catch (Exception $e) {
							echo '$'.$phpsh_k.' is an object.'."\n";
						}
						} else {
							echo '$'.$phpsh_k.' is an object.'."\n";
						}
					}
                                	break;
			}
                }
        }
}
//EOF
