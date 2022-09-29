<?php

defined('MOODLE_INTERNAL') || die();
$functions = array(
    'local_custom_service_do_unsuspend_user' => array(
        'classname' => 'local_custom_service_external',
        'methodname' => 'do_unsuspend_user',
        'classpath' => 'local/user_unsuspension_api/externallib.php',
        'description' => 'Un-suspend RBTC Student Users',
        'type' => 'write',
        'ajax' => true,
    ),
);

$services = array(
    'Custom Un-suspend User Account' => array(
        'functions' => array(
            'local_custom_service_do_unsuspend_user',
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);