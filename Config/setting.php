<?php
/**
 * [Config] BcDummyData
 *
 */
define('LOG_BCDUMMYDATA', 'bc_dummy_data');

CakeLog::config('bc_dummy_data', array(
	'engine' => 'FileLog',
	'types' => array('bc_dummy_data'),
	'file' => 'bc_dummy_data',
));
