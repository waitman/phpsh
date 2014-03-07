<?php
/* save vars */
$phpsh_mc = new Memcached();
$phpsh_mc->addServer('127.0.0.1',11211);
$phpsh_r=get_defined_vars();
$phpsh_nr=array();
foreach ($phpsh_r as $phpsh_k=>$phpsh_v) {
	switch ($phpsh_k) {
                                case '_GET':    /*fall through*/
                                case '_POST':   /*fall through*/
                                case '_COOKIE': /*fall through*/
                                case '_FILES':  /*fall through*/
                                case 'argv':    /*fall through*/
                                case 'argc':    /*fall through*/
                                case '_SERVER': /*fall through*/
                                case 'mc':      /*fall through*/
                                case 'pg':	/*fall through*/
						break;
				default:
						if (!strstr($phpsh_k,'phpsh'))
						$phpsh_nr[$phpsh_k]=$phpsh_v;
						break;
	}
}
$phpsh_mc->set('vars',serialize($phpsh_nr),60*60*24);

