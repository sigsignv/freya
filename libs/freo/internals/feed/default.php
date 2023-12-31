<?php

/*********************************************************************

 freo | Feed配信 (2013/01/02)

 Copyright(C) 2009-2013 freo.jp

*********************************************************************/

//外部ファイル読み込み
require_once FREO_MAIN_DIR . 'freo/internals/associate_entry.php';
require_once FREO_MAIN_DIR . 'freo/internals/security_entry.php';
require_once FREO_MAIN_DIR . 'freo/internals/filter_entry.php';

/* メイン処理 */
function freo_main()
{
	global $freo;

	//パラメータ検証
	if (!isset($_GET['category']) or !preg_match('/^[\w\-\/]+$/', $_GET['category'])) {
		$_GET['category'] = null;
	}
	if (!isset($_GET['page']) or !preg_match('/^\d+$/', $_GET['page']) or $_GET['page'] < 1) {
		$_GET['page'] = 1;
	}

	//検索条件設定
	$condition = null;
	if (isset($_GET['word'])) {
		$words = explode(' ', str_replace('　', ' ', $_GET['word']));

		foreach ($words as $word) {
			$condition .= ' AND (title LIKE ' . $freo->pdo->quote('%' . $word . '%') . ' OR text LIKE ' . $freo->pdo->quote('%' . $word . '%') . ')';
		}
	}
	if (isset($_GET['user'])) {
		$condition .= ' AND user_id = ' . $freo->pdo->quote($_GET['user']);
	}
	if (isset($_GET['tag'])) {
		$condition .= ' AND (tag = ' . $freo->pdo->quote($_GET['tag']) . ' OR tag LIKE ' . $freo->pdo->quote($_GET['tag'] . ',%') . ' OR tag LIKE ' . $freo->pdo->quote('%,' . $_GET['tag']) . ' OR tag LIKE ' . $freo->pdo->quote('%,' . $_GET['tag'] . ',%') . ')';
	}
	if (isset($_GET['date'])) {
		if (preg_match('/^\d\d\d\d$/', $_GET['date'])) {
			if (FREO_DATABASE_TYPE == 'mysql') {
				$condition .= ' AND DATE_FORMAT(datetime, \'%Y\') = ' . $freo->pdo->quote($_GET['date']);
			} else {
				$condition .= ' AND STRFTIME(\'%Y\', datetime) = ' . $freo->pdo->quote($_GET['date']);
			}
		} elseif (preg_match('/^\d\d\d\d\d\d$/', $_GET['date'])) {
			if (FREO_DATABASE_TYPE == 'mysql') {
				$condition .= ' AND DATE_FORMAT(datetime, \'%Y%m\') = ' . $freo->pdo->quote($_GET['date']);
			} else {
				$condition .= ' AND STRFTIME(\'%Y%m\', datetime) = ' . $freo->pdo->quote($_GET['date']);
			}
		} elseif (preg_match('/^\d\d\d\d\d\d\d\d$/', $_GET['date'])) {
			if (FREO_DATABASE_TYPE == 'mysql') {
				$condition .= ' AND DATE_FORMAT(datetime, \'%Y%m%d\') = ' . $freo->pdo->quote($_GET['date']);
			} else {
				$condition .= ' AND STRFTIME(\'%Y%m%d\', datetime) = ' . $freo->pdo->quote($_GET['date']);
			}
		}
	}

	//制限されたエントリーを一覧に表示しない
	if (!$freo->config['view']['restricted_display'] and ($freo->user['authority'] != 'root' and $freo->user['authority'] != 'author')) {
		$entry_filters = freo_filter_entry('user', array_keys($freo->refer['entries']));
		$entry_filters = array_keys($entry_filters, true);
		$entry_filters = array_map('intval', $entry_filters);
		if (!empty($entry_filters)) {
			$condition .= ' AND id NOT IN(' . implode(',', $entry_filters) . ')';
		}

		$entry_securities = freo_security_entry('user', array_keys($freo->refer['entries']), array('password'));
		$entry_securities = array_keys($entry_securities, true);
		$entry_securities = array_map('intval', $entry_securities);
		if (!empty($entry_securities)) {
			$condition .= ' AND id NOT IN(' . implode(',', $entry_securities) . ')';
		}
	}

	//エントリー取得
	if (isset($_GET['category'])) {
		$stmt = $freo->pdo->prepare('SELECT id, user_id, created, modified, approved, restriction, password, status, display, comment, trackback, code, title, tag, datetime, close, file, image, memo, text, category_id, entry_id FROM ' . FREO_DATABASE_PREFIX . 'entries LEFT JOIN ' . FREO_DATABASE_PREFIX . 'category_sets ON id = entry_id WHERE approved = \'yes\' AND (status = \'publish\' OR (status = \'future\' AND datetime <= :now1)) AND (close IS NULL OR close >= :now2) AND category_id = :category ' . $condition . ' ORDER BY datetime DESC LIMIT :start, :limit');
		$stmt->bindValue(':now1',     date('Y-m-d H:i:s'));
		$stmt->bindValue(':now2',     date('Y-m-d H:i:s'));
		$stmt->bindValue(':category', $_GET['category']);
		$stmt->bindValue(':start',    intval($freo->config['view']['feed_limit']) * ($_GET['page'] - 1), PDO::PARAM_INT);
		$stmt->bindValue(':limit',    intval($freo->config['view']['feed_limit']), PDO::PARAM_INT);
	} else {
		if ($condition == null) {
			$condition .= ' AND display = \'publish\'';
		}

		$stmt = $freo->pdo->prepare('SELECT * FROM ' . FREO_DATABASE_PREFIX . 'entries WHERE approved = \'yes\' AND (status = \'publish\' OR (status = \'future\' AND datetime <= :now1)) AND (close IS NULL OR close >= :now2) ' . $condition . ' ORDER BY datetime DESC LIMIT :start, :limit');
		$stmt->bindValue(':now1',  date('Y-m-d H:i:s'));
		$stmt->bindValue(':now2',  date('Y-m-d H:i:s'));
		$stmt->bindValue(':start', intval($freo->config['view']['feed_limit']) * ($_GET['page'] - 1), PDO::PARAM_INT);
		$stmt->bindValue(':limit', intval($freo->config['view']['feed_limit']), PDO::PARAM_INT);
	}
	$flag = $stmt->execute();
	if (!$flag) {
		freo_error($stmt->errorInfo());
	}

	$entries = array();
	while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$entries[$data['id']] = $data;
	}

	//エントリーID取得
	$entry_keys = array_keys($entries);

	//エントリー関連データ取得
	$entry_associates = freo_associate_entry('get', $entry_keys);

	//エントリーフィルター取得
	$entry_filters = freo_filter_entry('nobody', $entry_keys);

	foreach ($entry_filters as $id => $filter) {
		if (!$filter) {
			continue;
		}

		$entries[$id]['comment']   = 'closed';
		$entries[$id]['trackback'] = 'closed';
		$entries[$id]['title']     = str_replace('[$title]', $entries[$id]['title'], $freo->config['entry']['filter_title']);
		$entries[$id]['file']      = null;
		$entries[$id]['image']     = null;
		$entries[$id]['memo']      = null;
		$entries[$id]['text']      = str_replace('[$text]', $entries[$id]['text'], $freo->config['entry']['filter_text']);

		if ($freo->config['entry']['filter_option']) {
			$entry_associates[$id]['option'] = array();
		}
	}

	//エントリー保護データ取得
	$entry_securities = freo_security_entry('nobody', $entry_keys);

	foreach ($entry_securities as $id => $security) {
		if (!$security) {
			continue;
		}

		$entries[$id]['comment']   = 'closed';
		$entries[$id]['trackback'] = 'closed';
		$entries[$id]['title']     = str_replace('[$title]', $entries[$id]['title'], $freo->config['entry']['restriction_title']);
		$entries[$id]['file']      = null;
		$entries[$id]['image']     = null;
		$entries[$id]['memo']      = null;
		$entries[$id]['text']      = str_replace('[$text]', $entries[$id]['text'], $freo->config['entry']['restriction_text']);

		if ($freo->config['entry']['restriction_option']) {
			$entry_associates[$id]['option'] = array();
		}
	}

	//エントリータグ取得
	$entry_tags = array();
	foreach ($entry_keys as $entry) {
		if (!$entries[$entry]['tag']) {
			continue;
		}

		$entry_tags[$entry] = explode(',', $entries[$entry]['tag']);
	}

	//エントリーファイル取得
	$entry_files = array();
	foreach ($entry_keys as $entry) {
		if (!$entries[$entry]['file']) {
			continue;
		}

		list($width, $height, $size) = freo_file(FREO_FILE_DIR . 'entry_files/' . $entry . '/' . $entries[$entry]['file']);

		$entry_files[$entry] = array(
			'width'  => $width,
			'height' => $height,
			'size'   => $size
		);
	}

	//エントリーサムネイル取得
	$entry_thumbnails = array();
	foreach ($entry_keys as $entry) {
		if (!$entries[$entry]['file']) {
			continue;
		}
		if (!file_exists(FREO_FILE_DIR . 'entry_thumbnails/' . $entry . '/' . $entries[$entry]['file'])) {
			continue;
		}

		list($width, $height, $size) = freo_file(FREO_FILE_DIR . 'entry_thumbnails/' . $entry . '/' . $entries[$entry]['file']);

		$entry_thumbnails[$entry] = array(
			'width'  => $width,
			'height' => $height,
			'size'   => $size
		);
	}

	//エントリーイメージ取得
	$entry_images = array();
	foreach ($entry_keys as $entry) {
		if (!$entries[$entry]['image']) {
			continue;
		}

		list($width, $height, $size) = freo_file(FREO_FILE_DIR . 'entry_images/' . $entry . '/' . $entries[$entry]['image']);

		$entry_images[$entry] = array(
			'width'  => $width,
			'height' => $height,
			'size'   => $size
		);
	}

	//エントリーテキスト取得
	$entry_texts = array();
	foreach ($entry_keys as $entry) {
		if (!$entries[$entry]['text']) {
			continue;
		}

		if (isset($entry_associates[$entry]['option'])) {
			list($entries[$entry]['text'], $entry_associates[$entry]['option']) = freo_option($entries[$entry]['text'], $entry_associates[$entry]['option'], FREO_FILE_DIR . 'entry_options/' . $entry . '/');
		}
		list($excerpt, $more) = freo_divide($entries[$entry]['text']);

		$entry_texts[$entry] = array(
			'excerpt' => $excerpt,
			'more'    => $more
		);
	}

	//エントリー数・ページ数取得
	if (isset($_GET['category'])) {
		$stmt = $freo->pdo->prepare('SELECT COUNT(*) FROM ' . FREO_DATABASE_PREFIX . 'entries LEFT JOIN ' . FREO_DATABASE_PREFIX . 'category_sets ON id = entry_id WHERE approved = \'yes\' AND (status = \'publish\' OR (status = \'future\' AND datetime <= :now1)) AND (close IS NULL OR close >= :now2) AND category_id = :category ' . $condition);
		$stmt->bindValue(':now1',     date('Y-m-d H:i:s'));
		$stmt->bindValue(':now2',     date('Y-m-d H:i:s'));
		$stmt->bindValue(':category', $_GET['category']);
	} else {
		$stmt = $freo->pdo->prepare('SELECT COUNT(*) FROM ' . FREO_DATABASE_PREFIX . 'entries WHERE approved = \'yes\' AND (status = \'publish\' OR (status = \'future\' AND datetime <= :now1)) AND (close IS NULL OR close >= :now2) ' . $condition);
		$stmt->bindValue(':now1', date('Y-m-d H:i:s'));
		$stmt->bindValue(':now2', date('Y-m-d H:i:s'));
	}
	$flag = $stmt->execute();
	if (!$flag) {
		freo_error($stmt->errorInfo());
	}

	$data        = $stmt->fetch(PDO::FETCH_NUM);
	$entry_count = $data[0];
	$entry_page  = ceil($entry_count / $freo->config['view']['entry_limit']);

	//最終更新日時取得
	if (isset($_GET['category'])) {
		$stmt = $freo->pdo->prepare('SELECT MAX(datetime) FROM ' . FREO_DATABASE_PREFIX . 'entries LEFT JOIN ' . FREO_DATABASE_PREFIX . 'category_sets ON id = entry_id WHERE approved = \'yes\' AND (status = \'publish\' OR (status = \'future\' AND datetime <= :now1)) AND (close IS NULL OR close >= :now2) AND category_id = :category ' . $condition . ' ORDER BY datetime DESC LIMIT :start, :limit');
		$stmt->bindValue(':now1',     date('Y-m-d H:i:s'));
		$stmt->bindValue(':now2',     date('Y-m-d H:i:s'));
		$stmt->bindValue(':category', $_GET['category']);
		$stmt->bindValue(':start',    intval($freo->config['view']['feed_limit']) * ($_GET['page'] - 1), PDO::PARAM_INT);
		$stmt->bindValue(':limit',    intval($freo->config['view']['feed_limit']), PDO::PARAM_INT);
	} else {
		$stmt = $freo->pdo->prepare('SELECT MAX(datetime) FROM ' . FREO_DATABASE_PREFIX . 'entries WHERE approved = \'yes\' AND (status = \'publish\' OR (status = \'future\' AND datetime <= :now1)) AND (close IS NULL OR close >= :now2) ' . $condition . ' ORDER BY datetime DESC LIMIT :start, :limit');
		$stmt->bindValue(':now1',  date('Y-m-d H:i:s'));
		$stmt->bindValue(':now2',  date('Y-m-d H:i:s'));
		$stmt->bindValue(':start', intval($freo->config['view']['feed_limit']) * ($_GET['page'] - 1), PDO::PARAM_INT);
		$stmt->bindValue(':limit', intval($freo->config['view']['feed_limit']), PDO::PARAM_INT);
	}
	$flag = $stmt->execute();
	if (!$flag) {
		freo_error($stmt->errorInfo());
	}

	$data         = $stmt->fetch(PDO::FETCH_NUM);
	$entry_update = $data[0];

	//データ割当
	$freo->smarty->assign(array(
		'entries'          => $entries,
		'entry_associates' => $entry_associates,
		'entry_filters'    => $entry_filters,
		'entry_securities' => $entry_securities,
		'entry_tags'       => $entry_tags,
		'entry_files'      => $entry_files,
		'entry_thumbnails' => $entry_thumbnails,
		'entry_images'     => $entry_images,
		'entry_texts'      => $entry_texts,
		'entry_count'      => $entry_count,
		'entry_page'       => $entry_page,
		'entry_update'     => $entry_update
	));

	return;
}
