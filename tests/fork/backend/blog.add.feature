Feature:
	As an admin
	I need to be able to add blogposts to my website

	Background:
		Given I am logged in as an admin
		And I am on "/private/en/blog/add"

	@javascript
	Scenario: Adding a new blogpost with valid data should show a message
		Given I fill in "title" with "TestPost"
		And I fill in the editor "text" with "Dit is de inhoud"
		And I select "Default" from "categoryId"
		When I follow "Publish"
		Then I should see "The article \"TestPost\" was added"
