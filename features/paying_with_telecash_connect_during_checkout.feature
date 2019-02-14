@paying_with_telecash_connect_for_order
Feature: Paying with Telecash Connect during checkout
  In order to buy products
  As a Customer
  I want to be able to pay with Telecash Connect

  Background:
    Given the store operates on a single channel in "United States"
    And there is a user "roz@turbinekreuzberg.com" identified by "password123"
    And the store has a payment method "Telecash Connect" with a code "telecash_connect" and Telecash Connect payment gateway
    And the store has a product "PHP T-Shirt" priced at "€19.99"
    And the store ships everywhere for free
    And I am logged in as "roz@turbinekreuzberg.com"

  @ui
  Scenario: Successful payment
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Telecash Connect" payment method
    When I confirm my order
    And I sign in to Telecash Connect and pay successfully
    Then I should be notified that my payment has been completed

  @ui
  Scenario: Cancelling the payment
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Telecash Connect" payment method
    When I confirm my order
    And I cancel my Telecash Connect payment
    Then I should be notified that my payment has failed
    And I should be able to pay again

  @ui
  Scenario: Retrying the payment with success
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Telecash Connect" payment method
    And I have confirmed order
    But I have cancelled Telecash Connect payment
    When I try to pay again Telecash Connect payment
    And I sign in to Telecash Connect and pay successfully
    Then I should be notified that my payment has been completed
    And I should see the thank you page

  @ui
  Scenario: Retrying the payment and failing
    Given I added product "PHP T-Shirt" to the cart
    And I have proceeded selecting "Telecash Connect" payment method
    And I have confirmed order
    But I have cancelled Telecash Connect payment
    When I try to pay again Telecash Connect payment
    And I cancel my Telecash Connect payment
    Then I should be notified that my payment has failed
    And I should be able to pay again