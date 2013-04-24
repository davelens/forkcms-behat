Feature:
	As an admin
	In order to access the backend
	I need to be able to login with a valid email and password

	Scenario: Logging in with an invalid email or password
		Given I am on "/private/en/authentication/index?token=true"
		When I fill in "backendEmail" with "bogus.email@fork-cms.be"
		And I fill in "backendPassword" with "bogus.password"
		And I press "login"
		Then I should see "Your e-mail and password combination is incorrect"

	@fixtures
	Scenario: Logging in with a valid email and password
		Given I am logged in as an admin
