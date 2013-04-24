Feature:
	As an admin
	I need to be able to edit blogposts to my website

	Background:
		Given I am logged in as an admin
		And I am on "/private/en/blog/edit?id=1337"

	@javascript @fixtures
	Scenario: Editing an existing blogpost with valid data should show a message
		Given I fill in "title" with "TestPost #2"
		When I follow "Publish"
		Then I should see "The article \"TestPost #2\" was saved"
