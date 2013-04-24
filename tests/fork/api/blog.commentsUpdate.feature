Feature:
	As an API user
	in order to update a specific blog comment
	I need to provide valid parameters

	Background:
		Given I pass valid login credentials to the API

	@fixtures
	Scenario: commentsUpdate without the correct request method should trigger an error.
		Given I prepare a GET request to the API
		And I pass parameter "method" with value "blog.commentsUpdate"
		And I pass parameter "language" with value "en"
		And I pass parameter "id" with value "9999"
		And I do the call
		Then I should see "Illegal request method" in the output

	@fixtures
	Scenario: commentsUpdate without a valid ID should trigger an error.
		Given I prepare a POST request to the API
		And I pass parameter "method" with value "blog.commentsUpdate"
		And I pass parameter "language" with value "en"
		And I do the call
		Then I should see "No id-parameter provided" in the output

	@fixtures
	Scenario: commentsUpdate with a valid ID should trigger a "no data provided" error.
		Given I prepare a POST request to the API
		And I pass parameter "method" with value "blog.commentsUpdate"
		And I pass parameter "language" with value "en"
		And I pass parameter "id" with value "1"
		And I do the call
		Then I should see "No data provided" in the output

	@fixtures
	Scenario: commentsUpdate with a valid ID and valid status should not throw errors.
		Given I prepare a POST request to the API
		And I pass parameter "method" with value "blog.commentsUpdate"
		And I pass parameter "language" with value "en"
		And I pass parameter "id" with value "1"
		And I pass parameter "status" with value "published"
		And I do the call
		Then I should see "200" in the output
