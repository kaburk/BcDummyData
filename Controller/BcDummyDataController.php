<?php
class BcDummyDataController extends AppController {

	public $name = 'BcDummyData';

	public $uses = array(
		'Blog.BlogContent',
		'Blog.BlogPost',
		'Blog.BlogCategory',
		'Blog.BlogTag',
		'Blog.BlogComment',
		'Content',
		'SiteConfig',
	);

	public $components = array(
		'BcAuth',
		'Cookie',
		'BcAuthConfigure'
	);

	public $crumbs = array(
		array(
			'name' => 'BcDummyData',
			'url' => array(
				'plugin' => 'bc_dummy_data',
				'controller' => 'bc_dummy_data',
				'action' => 'index'
			)
		)
	);

	public $pageTitle = 'ダミーデータ作成';

	public $blogContent;
	public $userList;

	/**
	 * [ADMIN]
	 *
	 * @return    void
	 * @access    public
	 */
	public function admin_index() {

		if ($this->request->data) {
			if ($this->makeData($this->request->data('BcDummyData'))) {
				$this->setMessage('ダミーテータの作成に成功しました。');
			} else {
				$this->setMessage('ダミーテータの作成に失敗しました。', true);
			}
		}

		if (intval(getVersion()) < 4) {
			// baserCMS3系までの対応
			$blogContents = $this->BlogContent->find('list', array(
				'fields' => array(
					'id', 'title',
				),
				'order' => array(
					'BlogContent.id'
				),
				'recursive' => -1,
			));
		} else {
			// baserCMS4系対応
			$contents = $this->Content->find('all', array(
				'conditions' => array(
					'Content.plugin' => 'Blog',
					'Content.type' => 'BlogContent',
					'OR' => array(
						array('Content.alias_id' => ''),
						array('Content.alias_id' => NULL),
					),
				),
				'order' => array(
					'Content.entity_id'
				),
			));
			// 名サイトとサブサイトで同じ名称のブログが有る可能性があるので、
			// プルダウンにサイト名を付加
			foreach ($contents as $content) {
				if ($content['Site']['id']) {
					$siteName = $content['Site']['name'];
				} else {
					$bcSite = Configure::read('BcSite');
					$siteName = $bcSite['main_site_display_name'];
				}
				$blogContents[$content['Content']['entity_id']] = sprintf(
					'%s : %s',
					$siteName,
					$content['Content']['title']
				);
			}
		}
		$this->set('blogContents', $blogContents);

	}

	/**
	 * ダミーデータ作成処理（とりあえずブログのみ）
	 */
	private function makeData($params) {

		App::import('Vendor', ['file' => 'BcDummyData.autoload']);
		// App::import('Vendor', 'fzaninotto/faker/src/autoload');

		set_time_limit(0);
		ini_set('memory_limit', -1);
		ini_set("max_execution_time", 0);
		ini_set("max_input_time", 0);
		clearAllCache();

		srand((float) microtime() * 10000000);
		$faker = Faker\Factory::create('ja_JP');

		$blogContentId = $params['blog_content_id'];

		$db = ConnectionManager::getDataSource($this->BlogPost->useDbConfig);
		$dbPrefix = $db->config['prefix'];

		if ($params['clear_data']) {
			$this->BlogPost->deleteAll(['BlogPost.blog_content_id' => $blogContentId], false);
			$this->BlogComment->deleteAll(['BlogComment.blog_content_id' => $blogContentId], false);

			$this->BlogPost->query(    'ALTER TABLE `' . $dbPrefix . 'blog_posts` AUTO_INCREMENT = 1');
			$this->BlogPost->query(    'ALTER TABLE `' . $dbPrefix . 'blog_posts_blog_tags` AUTO_INCREMENT = 1');
			$this->BlogCategory->query('ALTER TABLE `' . $dbPrefix . 'blog_categories` AUTO_INCREMENT = 1');
			$this->BlogTag->query(     'ALTER TABLE `' . $dbPrefix . 'blog_tags` AUTO_INCREMENT = 1');
			$this->BlogComment->query( 'ALTER TABLE `' . $dbPrefix . 'blog_comments` AUTO_INCREMENT = 1');
		}

		$ret = false;
		if (empty($blogContentId)) {
			return $ret;
		}

		$params['num'] = (int) $params['num'];

		$blogContent = $this->BlogContent->find('first', [
			'conditions' => [
				'BlogContent.id' => $blogContentId,
			],
			'recursive' => -1,
		]);

		$blogCategory = $this->BlogCategory->find('list', [
			'fields' => [
				'id'
			],
			'recursive' => -1,
		]);

		$blogTag = $this->BlogTag->find('list', [
			'fields' => [
				'id'
			],
			'recursive' => -1,
		]);

		$blogPostNo = $this->BlogPost->getMax('no', ['blog_content_id' => $blogContentId]) + 1;
		$blogCommentNo = $this->BlogComment->getMax('no', ['blog_content_id' => $blogContentId]) + 1;

		for ($num = 0; $num < $params['num']; $num++ ) {

			// 記事の作成
			$data = [];

			// ブログID
			$data['BlogPost']['blog_content_id'] = $blogContentId;

			// 記事番号
			$data['BlogPost']['no'] = $blogPostNo;
			$blogPostNo++;

			// 記事タイトル
			$data['BlogPost']['name'] = '【テスト記事' . $data['BlogPost']['no'] . '】' . $faker->realText(20);

			// 概要
			if ($blogContent['BlogContent']['use_content']) {
				$data['BlogPost']['content'] = $faker->realText(500);
				$data['BlogPost']['content_draft'] = $faker->realText(500);
			}

			// 本文
			$data['BlogPost']['detail'] = $faker->realText(500);
			$data['BlogPost']['detail_draft'] = $faker->realText(500);

			// BurgerEditor
			$data['BlogPost']['detail'] =
				'<div data-bgb="wysiwyg" class="bgb-wysiwyg">' .
				'<div data-bgt="ckeditor" data-bgt-ver="2.1.0" class="bgt-container bgt-ckeditor-container">' .
				'<div class="bge-ckeditor" data-bge="ckeditor">' .
					$data['BlogPost']['detail'] .
				'</div></div></div>';
			$data['BlogPost']['detail_draft'] =
				'<div data-bgb="wysiwyg" class="bgb-wysiwyg">' .
				'<div data-bgt="ckeditor" data-bgt-ver="2.1.0" class="bgt-container bgt-ckeditor-container">' .
				'<div class="bge-ckeditor" data-bge="ckeditor">' .
					$data['BlogPost']['detail_draft'] .
				'</div></div></div>';

			// 記事カテゴリ
			$data['BlogPost']['blog_category_id'] = null;
			$max = (int) mt_rand(0, count($blogCategory));
			if ($max) {
				$categoryId = array_rand($blogCategory, 1);
				$data['BlogPost']['blog_category_id'] = $categoryId;
			}

			// 投稿ユーザID
			$data['BlogPost']['user_id'] = 1;

			// 公開状態
			$data['BlogPost']['status'] = true;

			// 投稿日を設定
			$data['BlogPost']['posts_date'] = $faker->dateTimeBetween('-10 years', 'today')->format('Y-m-d H:i:s');

			// 表示開始日を設定
			$data['BlogPost']['publish_begin'] = null;

			// 表示終了日を設定
			$data['BlogPost']['publish_end'] = null;

			// 検索除外を設定
			$data['BlogPost']['exclude_search'] = false;

			// TODO: アイキャッチ
			$eyecatch = null;
			$data['BlogPost']['eye_catch'] = $eyecatch;

			// 登録日を設定
			$data['BlogPost']['created'] = date('Y-m-d H:i:s');

			// 更新日を設定
			$data['BlogPost']['modified'] = date('Y-m-d H:i:s');

			// ブログタグ
			if ($blogContent['BlogContent']['tag_use']) {
				$max = (int) mt_rand(0, count($blogTag));
				if ($max) {
					$tagId = array_rand($blogTag, $max);
					if ($tagId) {
						$data['BlogTag']['BlogTag'] = $tagId;
					}
				}
			}

			// コメント
			if ($blogContent['BlogContent']['comment_use']) {
				$max = (int) mt_rand(0, 5);
				for ($c = 0; $c < $max; $c++) {
					$data['BlogComment'][] = [
						'blog_content_id' => $blogContentId,
						'no' => $blogCommentNo,
						'status' => true,
						'name' => $faker->name,
						'email' => $faker->email,
						'url' => $faker->url,
						'message' => $faker->realText(100),
						'created' => date('Y-m-d H:i:s'),
						'modified' => date('Y-m-d H:i:s'),
					];
					$blogCommentNo++;
				}
			}

			unset($this->BlogPost->BlogComment->validate['url']);
			unset($this->BlogPost->BlogComment->validate['email']);
			unset($this->BlogPost->BlogComment->validate['name']);

			$ret = $this->BlogPost->saveAll($data, ['callbacks' => false]);
		}

		$this->BlogPost->query(sprintf(
			'UPDATE `' . $dbPrefix . 'blog_posts` SET created = DATE_ADD(NOW(), INTERVAL  - RAND() * 999 DAY ) WHERE blog_content_id = %d;',
			$blogContentId
		));
		$this->BlogPost->query(sprintf(
			'UPDATE `' . $dbPrefix . 'blog_comments` SET created = DATE_ADD(NOW(), INTERVAL  - RAND() * 999 DAY ) WHERE blog_content_id = %d;',
			$blogContentId
		));

		return $ret;
	}
}
