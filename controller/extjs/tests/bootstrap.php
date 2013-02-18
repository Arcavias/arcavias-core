<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: bootstrap.php 14386 2011-12-16 13:50:08Z fblasel $
 */


/*
 * Set error reporting to maximum
 */
error_reporting( -1 );

date_default_timezone_set('UTC');

/**
 * Set locale settings to reasonable defaults
 */
setlocale(LC_ALL, 'en_US.UTF-8');
setlocale(LC_NUMERIC, 'POSIX');
setlocale(LC_CTYPE, 'en_US.UTF-8');
setlocale(LC_TIME, 'POSIX');

/*
 * Set include path for tests
 */
define('PATH_TESTS', dirname( __FILE__ ));

require_once 'TestHelper.php';
TestHelper::bootstrap();
