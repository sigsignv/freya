<?php

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/*********************************************************************

 freo | 起動ファイル (2017/09/03)

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

 @Link http://freo.jp/
 @Copyright(C) 2009-2017 freo.jp
 @Author refirio <info at refirio dot org>

*********************************************************************/

$log = new Logger('ErrorLog');
$log->pushHandler(new StreamHandler(__DIR__ . '/database/errorlog.txt'));

set_error_handler(function($errno, $errstr) {
    global $log;

    $log->alert($errstr);
});

try {
    $log->debug('Reading config...');
    require_once 'config.php';
    $log->debug('Start...');
    require_once FREO_MAIN_DIR . 'freo/freo.php';
    $log->debug('Finished');
} catch (Throwable $ex) {
    $log->alert($ex);
}

exit;
