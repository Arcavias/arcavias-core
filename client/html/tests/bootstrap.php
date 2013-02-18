<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: bootstrap.php 1320 2012-10-19 19:57:38Z nsendetzky $
 */


/*
 * Set error reporting to maximum
 */
error_reporting( -1 );
ini_set('display_errors', true);

date_default_timezone_set('UTC');

/*
 * Set locale settings to reasonable defaults
 */
setlocale(LC_ALL, 'en_US.UTF-8');
setlocale(LC_NUMERIC, 'POSIX');
setlocale(LC_CTYPE, 'en_US.UTF-8');
setlocale(LC_TIME, 'POSIX');


require_once 'TestHelper.php';
TestHelper::bootstrap();
