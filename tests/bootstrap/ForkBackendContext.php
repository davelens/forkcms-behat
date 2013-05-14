<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
	Behat\Behat\Context\Step\Given,
	Behat\Behat\Context\Step\When,
	Behat\Behat\Context\Step\Then,
	Behat\Behat\Exception\PendingException;

class ForkBackendContext extends BehatContext
{
	/**
	 * @Given /^I am logged in as an? (\w+)$/
	 */
	public function iAmLoggedInAsA($role)
	{
		if(!in_array($role, array('client', 'admin')))
		{
			throw new Exception('Invalid role. Valid roles are client or admin.');
		}

		switch($role)
		{
			case 'admin':
				$userID = 1337;
				break;

			case 'client':
				$userID = 1338;
				break;
		}

		$loader = $this->getMainContext()->getSubContext('fixtures')->getLoader();
		$user = $loader->getBackendUser($userID);

		if(!($user instanceof BackendUser))
		{
			throw new Exception('No valid user was found, check the fixtures!');
		}

		return $this->iAmLoggedInForkAsWithPassword($user->getEmail(), $user->getSetting('password'));
	}

	/**
	 * @Given /^I am logged in as "([^"]*)" with password "([^"]*)"$/
	 */
	public function iAmLoggedInForkAsWithPassword($email, $password)
	{
		$js = $this->getMainContext()->isJavascriptScenario();
		$submitAction = $js ? new When('I follow "Log in"') : new When('I press "login"');

		return array(
			new Given('I am on "/private"'),
			new When('I fill in "backendEmail" with "' . $email . '"'),
			new When('I fill in "backendPassword" with "' . $password . '"'),
			$submitAction,
			new Then('I should see "Dashboard"')
		);
	}

	/**
     * @Given /^I fill in the editor "([^"]*)" with "([^"]*)"$/
     */
    public function iFillInTheEditorWith($id, $body)
	{
		$session = $this->getMainContext()->getSession();
		$session->executeScript("CKEDITOR.instances.$id.setData('$body');");
    }
}
