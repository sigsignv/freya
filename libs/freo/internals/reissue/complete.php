<?php

/*********************************************************************

 freo | パスワード再発行 | 完了 (2010/09/01)

 Copyright(C) 2009-2010 freo.jp

*********************************************************************/

/* メイン処理 */
function freo_main()
{
	global $freo;

	//データ割当
	$freo->smarty->assign(array(
		'token' => freo_token('create')
	));

	return;
}
