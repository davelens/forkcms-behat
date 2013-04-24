Feature:
	As an API user
	in order to authenticate and query data
	I need to provide valid authentication credentials

	Scenario: Doing a call without all required authentication credentials should trigger an error.
		Given I prepare a GET request to the API
		And I pass parameter "method" with value "analytics.keywordsGetData"
		And I pass parameter "language" with value "en"
		And I do the call
		Then I should see "Not authorized" in the output

	Scenario: Providing an invalid email-address should trigger an error.
		Given I pass parameter "email" with value "bogus.email@fork-cms.be"
		And I pass parameter "nonce" with value "nonce"
		And I pass parameter "secret" with value "secret"
		And I prepare a GET request to the API
		And I pass parameter "method" with value "analytics.keywordsGetData"
		And I pass parameter "language" with value "en"
		And I do the call
		Then I should see "This account does not exist" in the output

	Scenario: Providing an invalid secret key should trigger an error.
		Given I pass invalid login credentials to the API
		And I prepare a GET request to the API
		And I pass parameter "method" with value "analytics.keywordsGetData"
		And I pass parameter "language" with value "en"
		And I do the call
		Then I should see "Invalid secret" in the output

	@fixtures
	Scenario: Querying with valid authentication params should let us proceed with the call.
		Given I pass valid login credentials to the API
		And I prepare a GET request to the API
		And I pass parameter "method" with value "analytics.keywordsGetData"
		And I pass parameter "language" with value "en"
		And I do the call
		Then I should see "Analytics-module not configured correctly" in the output
