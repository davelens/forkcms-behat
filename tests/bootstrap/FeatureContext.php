<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
	Behat\Behat\Context\Step\Given,
	Behat\Behat\Context\Step\When,
	Behat\Behat\Context\Step\Then,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Behat\Event\SuiteEvent,
	Behat\Behat\Event\ScenarioEvent;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;

require_once 'ForkAPIContext.php';
require_once 'ForkBackendContext.php';

class FeatureContext extends MinkContext
{
	/**
	 * @var KernelInterface $kernel
	 */
	protected static $kernel = null;

	/**
	 * @param array $parameters
	 */
	public function __construct(array $parameters)
	{
		$this->useContext('fixtures', new FixtureContext($parameters));
		$this->useContext('fork_api', new ForkAPIContext($parameters));
		$this->useContext('fork_backend', new ForkBackendContext($parameters));
	}

	/**
	 * @AfterSuite
	 */
	public static function afterSuite(SuiteEvent $event)
	{
		$configPath = __DIR__ . '/../../app/config';
		rename($configPath . '/parameters.yml.bak', $configPath . '/parameters.yml');
	}

	/**
	 * @BeforeScenario
	 */
	public function beforeScenario()
	{
		if($this->getMinkParameter('base_url') !== null) return;

		// Make the base URL for Mink dynamic by loading it from our container
		$this->setMinkParameters(
			array('base_url' => 'http://' . BackendModel::getContainer()->getParameter('site.domain'))
		);
	}

	/**
	 * @BeforeSuite
	 *
	 * @todo Find a good way to load locale for use throughout the tests, without too much overhead.
	 */
	public static function beforeSuite(SuiteEvent $event)
	{
		// We make sure we run on a test database whilst running the tests
		$configFile = __DIR__ . '/../../app/config/parameters.yml';

		// This resets the previous state (probably left this way due to an exception in the tests).
		if(file_exists($configFile . '.bak'))
		{
			rename($configFile . '.bak', $configFile);
		}

		copy($configFile, $configFile . '.bak');

		$config = Yaml::parse($configFile);
		$config['parameters']['database.name'] .= '_test';
		file_put_contents($configFile, Yaml::dump($config));

		// Initialize the Fork autoloader and its kernel
		require_once __DIR__ . '/../../autoload.php';
		require_once __DIR__ . '/../../app/AppKernel.php';
		self::$kernel = new AppKernel();

		// Pass the freshly initialized container to our models
		BackendModel::setContainer(self::$kernel->getContainer());
		FrontendModel::setContainer(self::$kernel->getContainer());
	}


	// CUSTOM DEFINITIONS START BELOW

	/**
	 * @param string $type xpath or css
	 * @param string $query the xpath query or the ID attribute value
	 * @return Behat\Mink\Element\NodeElement
	 */
	protected function findElement($type, $query)
	{
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath($type, $query)
        );

        // errors must not pass silently
		if(null === $element)
		{
			throw new \InvalidArgumentException(
				sprintf('Could not evaluate %s: "%s"', $type, $query)
			);
        }

		return $element;
	}

	/**
     * Click on the element with the provided CSS Selector
     *
     * @When /^I click on the element with css selector "([^"]*)"$/
     */
    public function iClickOnTheElementWithCSSSelector($cssSelector)
    {
		return $this->findElement('css', $cssSelector)->click();
    }

	/**
     * Click on the element with the provided xpath query
     *
     * @When /^I click on the element with xpath "([^"]*)"$/
     */
    public function iClickOnTheElementWithXPath($query)
    {
		return $this->findElement('xpath', $query)->click();
    }

	/**
	 * @When /^I fill in:$/
	 */
	public function iFillIn(TableNode $table)
	{
		$hash = $table->getHash();

		foreach($hash as $record)
		{
			foreach($record as $key => $value)
			{
				$this->fillField($key, $value);
			}
		}
	}

	/**
	 * @When /^I run "([^"]*)"$/
	 */
	public function iRun($command)
	{
		exec($command, $this->output);
		$this->output = implode(PHP_EOL, $this->output);
	}

	/**
	 * @return bool
	 */
	public function isJavascriptScenario()
	{
		return ($this->getSession()->getDriver() instanceof Behat\Mink\Driver\Selenium2Driver);
	}

	/**
	 * @Then /^I wait (\d+) second(s?)$/
	 */
	public function iWait($seconds)
	{
		sleep($seconds);
	}

	/**
	 * @Then /^I wait for ID "([^"]*)" to appear$/
	 */
	public function iWaitForContentToAppear($id)
	{
		$this->getSession()->wait(5000, "$('#$id').children().length > 0");
	}
}
