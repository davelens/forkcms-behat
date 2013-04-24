Feature:
	As an admin
	I need to be able to delete blogposts from my website

	Background:
		Given I am logged in as an admin
		And I am on "/private/en/blog/edit?id=1337"

	@javascript @fixtures
	Scenario: Deleting an existing blogpost should show a message
		Given I follow "Delete"
		And I press "OK"
		Then I should see "The selected articles were deleted"
