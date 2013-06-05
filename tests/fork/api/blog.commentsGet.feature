Feature:
	As an API user
	in order to retrieve blog comments
	I need to provide valid parameters

	Background:
		Given I pass valid login credentials to the API

	@fixtures
	Scenario: commentsGet with valid params should return the request data.
		Given I prepare a GET request to the API
		And I pass parameter "method" with value "blog.commentsGet"
		And I pass parameter "language" with value "en"
		And I do the call
		Then I should see "lina.peeters@gmail.com" in the output
