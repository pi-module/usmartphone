<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */
return array(
    // route name
    'usmartphone' => array(
        'name' => 'usmartphone',
        'type' => 'Module\Usmartphone\Route\Usmartphone',
        'options' => array(
            'route' => '/usmartphone',
            'defaults' => array(
                'module' => 'usmartphone',
                'controller' => 'index',
                'action' => 'index'
            )
        ),
    )
);