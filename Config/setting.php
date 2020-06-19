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

/**
 * システムナビ
 */
$config['BcApp.adminNavigation'] = [
	'Plugins' => [
		'menus' => [
			'BcDummyData' => [
				'title' => __d('baser', 'ダミーデータ作成'),
				'url' => [
					'admin' => true,
					'plugin' => 'bc_dummy_data',
					'controller' => 'bc_dummy_data',
					'action' => 'index'
				]
			],
		]
	],
];

$config['BcApp.adminNavi.BcDummyData'] = [
	'name' => __d('baser', 'ダミーデータ作成プラグイン'),
	'contents' => [
		[
			'name' => __d('baser', 'ダミーデータ作成'),
			'url' => [
				'admin' => true,
				'plugin' => 'bc_dummy_data',
				'controller' => 'bc_dummy_data',
				'action' => 'index',
			],
		],
	],
];
