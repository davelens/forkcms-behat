<?php

use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

require_once 'BlogPost.php';
require_once 'BackendUser.php';

class FixtureLoader implements ContainerAwareInterface
{
	/**
	 * @var Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	public function __construct()
	{
		$this->faker = Factory::create('nl_BE');
	}

	public function cleanup()
	{
		$db = $this->container->get('database');
		$db->execute(
			'DELETE a,b,c
			 FROM blog_posts AS a
			 INNER JOIN meta AS b ON b.id = a.meta_id
			 LEFT JOIN blog_comments AS c ON c.post_id = a.id
			 WHERE a.id = 1337 OR a.title REGEXP "TestPost"'
		);

		$db->execute(
			'DELETE a,b,c,d
			 FROM users AS a
			 LEFT JOIN users_settings AS b ON b.user_id = a.id
			 LEFT JOIN users_sessions AS c ON c.user_id = a.id
			 LEFT JOIN users_groups AS d ON d.user_id = a.id
			 WHERE a.id IN(1337, 1338)'
		);
	}

	/**
	 * @param int $seed The unique ID of the blogpost to load comments for
	 */
	protected function existsBlogPost($seed)
	{
		$db = $this->container->get('database');
		return (bool) $db->getVar('SELECT 1 FROM blog_posts WHERE id = ?', $seed);
	}

	/**
	 * @param int $id
	 * @return BackendUser
	 */
	public function getBackendUser($id)
	{
		require_once __DIR__ . '/../../../backend/core/engine/user.php';
		return new BackendUser($id);
	}

	/**
	 * @param int $seed The unique ID of the blogpost to load
	 */
	public function loadBlogPost($seed)
	{
		$db = $this->container->get('database');
		$this->faker->addProvider(new \Faker\Provider\BlogPost($this->faker));
		$this->faker->seed($seed);

		$record['id'] = (int) $seed;
		$record['user_id'] = 1337;
		$record['category_id'] = 1;
		$record['language'] = 'en';
		$record['title'] = $this->faker->title;
		$record['text'] = $this->faker->text;
		$record['publish_on'] = date('Y-m-d H:i:s');
		$record['created_on'] = date('Y-m-d H:i:s');
		$record['edited_on'] = date('Y-m-d H:i:s');
		$record['num_comments'] = 0;
		$meta = array(
			'url' => strtolower(str_replace(' ', '-', $record['title'])),
			'title' => $record['title'],
			'keywords' => $record['title'],
			'description' => $this->faker->text(20),
		);
		$record['meta_id'] = $db->insert('meta', $meta);
		$record['revision_id'] = $db->insert('blog_posts', $record);
	}

	/**
	 * Loads 2 comments for the given blogpost seed.
	 *
	 * @param int $seed
	 */
	public function loadBlogPostComments($seed)
	{
		if(!$this->existsBlogPost($seed))
		{
			$this->loadBlogPost($seed);
		}

		$db = $this->container->get('database');
		$record['id'] = 1;
		$record['post_id'] = $seed;
		$record['language'] = 'en';
		$record['created_on'] = date('Y-m-d H:i:s');
		$record['author'] = $this->faker->name;
		$record['email'] = $this->faker->email;
		$record['text'] = $this->faker->text;
		$record['type'] = 'comment';
		$record['status'] = 'published';
		$db->insert('blog_comments', $record);

		$record['id'] = 2;
		$record['status'] = 'moderation';
		$db->insert('blog_comments', $record);
	}

	public function loadBackendUser($seed, $is_god = true)
	{
		$this->faker->addProvider(new \Faker\Provider\BackendUser($this->faker));
		require_once __DIR__ . '/../../../backend/core/engine/authentication.php';
		require_once __DIR__ . '/../../../backend/modules/users/engine/model.php';

		$db = $this->container->get('database');
		$settings['nickname'] = $this->faker->name;
		$settings['name'] = $this->faker->first_name;
		$settings['surname'] = $this->faker->last_name;
		$settings['interface_language'] = 'en';
		$settings['date_format'] = 'j F Y H:i';
		$settings['time_format'] = 'H:i';
		$settings['datetime_format'] = $settings['date_format'] . ' ' . $settings['time_format'];
		$settings['number_format'] = 'dot_nothing';
		$settings['csv_split_character'] = ';';
		$settings['csv_line_ending'] = "\n";
		$settings['password'] = $this->faker->password;
		$settings['password_key'] = uniqid();
		$settings['current_password_change'] = time();
		$settings['avatar'] = 'god.jpg';
		$settings['api_access'] = true;

		$user['id'] = $seed;
		$user['email'] = $this->faker->email;
		$user['password'] = BackendAuthentication::getEncryptedString(
			$settings['password'],
			$settings['password_key']
		);
		$user['active'] = 'Y';
		$user['deleted'] = 'N';
		$user['is_god'] = $is_god ? 'Y' : 'N';

		BackendUsersModel::insert($user, $settings);
		$db->insert('users_groups', array('group_id' => 1, 'user_id' => $user['id']));
	}

	/**
	 * @param ContainerInterface[optional] $container
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}
}
