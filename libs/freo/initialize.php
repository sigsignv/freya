<?php

/*********************************************************************

 freo | 初期化処理 (2012/09/30)

 Copyright(C) 2009-2012 freo.jp

*********************************************************************/

ini_set('default_charset', 'UTF-8');
ini_set('mbstring.language', 'Japanese');
ini_set('mbstring.input_encoding', 'pass');
ini_set('mbstring.output_encoding', 'pass');
ini_set('mbstring.substitute_character', 'none');

ini_set('session.use_trans_sid', 0);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 0);
ini_set('session.auto_start', 0);
ini_set('session.cache_limiter', 'none');

if (ob_get_level()) {
	ob_end_clean();
}
