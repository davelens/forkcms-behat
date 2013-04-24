Feature:
	As an API user
	in order to retrieve a specific blog comment
	I need to provide valid parameters

	Background:
		Given I pass valid login credentials to the API
		And I pass parameter "method" with value "blog.commentsGetById"

	@fixtures
	Scenario: commentsGetById without a valid ID should trigger an error.
		Given I prepare a GET request to the API
		And I do the call
		Then I should see "No id-parameter provided" in the output

	@fixtures
	Scenario: commentsGetById without a valid ID should return the requested data.
		Given I prepare a GET request to the API
		And I pass parameter "id" with value "1"
		And I do the call
		Then I should see "afton82@yahoo.com" in the output
