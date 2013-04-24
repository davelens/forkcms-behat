<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Context\Step\Given;

class ForkAPIContext extends BehatContext
{
	/**
	 * @var string
	 */
	protected $command;

	/**
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * @When /^I do the call$/
	 */
	public function iDoTheCall()
	{
		$parameters = '';
		foreach($this->parameters as $parameter => $value)
		{
			$parameters .= '&' . $parameter . '="' . $value . '"';
		}
		$parameters = str_replace('&', ' --data-urlencode ', $parameters);

		$command = $this->command . '?format=json' . $parameters;
		exec($command, $this->output);
		$this->output = implode(PHP_EOL, $this->output);
	}

	/**
	 * @Given /^I pass invalid login credentials to the API$/
	 */
	public function iPassInvalidLoginCredentialsToTheAPI()
	{
		$loader = $this->getMainContext()->getSubContext('fixtures')->getLoader();
		$user = $loader->getBackendUser(1338);

		$email = $user->getEmail();
		$apiKey = $user->getSetting('api_key');
		$nonce = 'demo';
		$secret = 'invalid';

		return $this->iPassLoginCredentialsToTheAPI($email, $nonce, $secret);
	}

	/**
	 * @Given /^I pass valid login credentials to the API$/
	 */
	public function iPassValidLoginCredentialsToTheAPI()
	{
		$loader = $this->getMainContext()->getSubContext('fixtures')->getLoader();
		$user = $loader->getBackendUser(1338);

		$email = $user->getEmail();
		$apiKey = $user->getSetting('api_key');
		$nonce = 'demo';
		$secret = BackendAuthentication::getEncryptedString($email . $apiKey, $nonce);

		return $this->iPassLoginCredentialsToTheAPI($email, $nonce, $secret);
	}

	/**
	 * @param string $email
	 * @param string $nonce
	 * @param string $secret
	 */
	public function iPassLoginCredentialsToTheAPI($email, $nonce, $secret)
	{
		return array(
			new Given('I pass parameter "email" with value "' . $email . '"'),
			new Given('I pass parameter "nonce" with value "' . $nonce . '"'),
			new Given('I pass parameter "secret" with value "' . $secret . '"'),
		);
	}

	/**
     * @Given /^I pass an image, located in "([^"]*)", in parameter "([^"]*)"$/
     */
    public function iPassAnImageLocatedInInParameter($file, $parameter)
    {
		if(!file_exists($file))
		{
			throw new Exception('The fixture image was not found!');
		}

		$this->parameters[$parameter] = base64_encode(file_get_contents($file));
    }

	/**
	 * @When /^I pass parameter "([^"]*)" with value "([^"]*)"$/
	 */
	public function iPassParameterWithValue($parameter, $value)
	{
		$this->parameters[$parameter] = $value;
	}

	/**
	 * @Given /^I prepare a (\w+) request to the API$/
	 */
	public function iPrepareARequestToTheAPI($method)
	{
		$url = SITE_URL . '/api/1.0/index.php';
		$method = strtoupper($method);
		$command = 'curl -s --globoff';

		if($method !== 'POST')
		{
			$command .= ' --get';
		}

		$command .= ' -X'. $method;
		$this->command = $command . ' ' . $url;
	}

	/**
	 * @Then /^I should see "([^"]*)" in the output$/
	 */
	public function iShouldSeeInTheOutput($string)
	{
		if(!preg_match('/'. $string . '/', $this->output))
		{
			throw new \Exception(sprintf('Did not see "%s" in the output', $string));
		}
	}
}
