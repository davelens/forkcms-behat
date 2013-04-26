<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext;

require_once 'fixtures/FixtureLoader.php';

class FixtureContext extends BehatContext
{
	/**
	 * @var FixtureLoader
	 */
	protected $loader;

	/**
	 * @param array $parameters
	 */
	public function __construct($parameters)
	{
		$this->loader = new FixtureLoader();
	}

	/**
	 * @AfterScenario @fixtures, @cleanup
	 */
	public function cleanFixtures()
	{
		$this->loader->setContainer(BackendModel::getContainer());
		$this->loader->cleanup();
	}

	/**
	 * @return FixtureLoader
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * @BeforeScenario @fixtures
	 */
	public function loadFixtures()
	{
		$this->loader->setContainer(BackendModel::getContainer());
		$this->loader->cleanup();
		$this->loader->loadBackendUser(1337);
		$this->loader->loadBackendUser(1338, false);
		$this->loader->loadBlogPost(1337);
		$this->loader->loadBlogPostComments(1337);
	}
}
