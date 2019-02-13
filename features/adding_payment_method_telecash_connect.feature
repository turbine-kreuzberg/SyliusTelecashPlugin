@managing_payment_method_telecash_connect
Feature: Adding a new payment method
  In order to pay for orders in different ways
  As an Administrator
  I want to add a new payment method to the registry

  Background:
    Given the store operates on a single channel in "United States"
    And adding a new channel "Germany" with code "DE" and currency "EUR"
    And I am logged in as an administrator

  @ui
  Scenario: Adding a new Telecash payment method with result successfully
    Given I want to create a new payment method with Telecash Connect gateway factory
    When I name it "Telecash Connect" in "English (United States)"
    And I specify its code as "telecash_connect"
    And make it available in channel "Germany"
    And I configure it with test Telecash credentials
    And I add it
    Then I should be notified that it has been successfully created
    And the payment method "Telecash Connect" should appear in the registry
    And the payment method "Telecash Connect" should be available in channel "Germany"