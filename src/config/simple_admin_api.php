<?php

return [
	'role' => ['user','manager','administrator'],
	'default_registration_role' => 'user',
	'admin_login_access' => ['manager','administrator'],
	'rules' => ['ADMIN_INDEX','ADMIN_CREATE','ADMIN_EDIT','ADMIN_SHOW','ADMIN_DELETE'],
	'rules_access' => [
		'user' => [],
		'manager' => ['ADMIN_INDEX','ADMIN_SHOW'],
		'administrator' => ['ADMIN_INDEX','ADMIN_CREATE','ADMIN_EDIT','ADMIN_SHOW','ADMIN_DELETE'],
	],
	'manage_user' => [
		'user' => [],
		'manager' => ['ADMIN_INDEX','ADMIN_SHOW'],
		'administrator' => ['ADMIN_INDEX','ADMIN_CREATE','ADMIN_EDIT','ADMIN_SHOW','ADMIN_DELETE'],
	]
];