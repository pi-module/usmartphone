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
    'meta'  => array(
        'title'         => _a('Smartphone login'),
        'description'   => _a('Smartphone app login on website'),
        'version'       => '0.0.1',
        'license'       => 'New BSD',
        'demo'          => 'http://demo.pialog.org',
        'icon'          => 'fa-mobile',
    ),
    // Author information
    'author'    => array(
        // Author full name, required
        'Dev'       => 'Hossein Azizabadi',
        // Email address, optional
        'Email'     => 'azizabadi@faragostaresh.com',
    ),

    // Resource
    'resource' => array(
        'config'        => 'config.php',
        'route'         => 'route.php',
    ),
);
