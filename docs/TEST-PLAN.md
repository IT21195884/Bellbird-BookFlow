# Bellbird BookFlow Test Plan

## 1. Purpose

This test plan verifies that Bellbird BookFlow satisfies the stock-management and customer-order-management requirements identified in the Bellbird Books case study.

## 2. Test Environment

- Application: Bellbird BookFlow
- Version: 1.0.0
- Frontend: HTML, CSS and JavaScript
- Backend: PHP
- Database: SQLite
- Local server: PHP development server
- Supported browser: Current version of Google Chrome, Microsoft Edge, Firefox or Safari

## 3. Test Approach

The application will be tested using:

- PHP syntax checking
- Database initialisation testing
- Functional testing
- Input-validation testing
- Negative testing
- Basic browser compatibility testing
- User acceptance testing

## 4. Entry Criteria

Testing may begin when:

- All STK-01 to STK-08 requirements are implemented.
- All ORD-01 to ORD-08 requirements are implemented.
- The database can be initialised.
- The application can run locally.
- The release branch has been created.

## 5. Exit Criteria

Testing is complete when:

- All critical functions have been tested.
- No critical defects remain unresolved.
- Invalid customer information is rejected.
- Stock quantities cannot become negative.
- Order lifecycle functions operate correctly.
- Installation instructions have been verified.

## 6. Functional Test Cases

| ID | Requirement | Test | Expected Result | Status |
|---|---|---|---|---|
| TC-01 | STK-01 | Add a valid new-book title | Book is saved and displayed | Passed |
| TC-02 | STK-01 | Submit new book without a title | Validation message is displayed | Passed |
| TC-03 | STK-02 | Increase new-book quantity | Quantity increases correctly | Passed  |
| TC-04 | STK-02 | Reduce quantity below zero | Update is rejected | Passed |
| TC-05 | STK-03 | Add a second-hand copy | Individual copy is displayed | Passed |
| TC-06 | STK-04 | Edit a new-book record | Updated information is displayed | Passed |
| TC-07 | STK-04 | Edit a second-hand copy | Updated information is displayed | Passed  |
| TC-08 | STK-05 | Mark second-hand copy unavailable | Copy no longer appears as available | Passed |
| TC-09 | STK-06 | Search across both stock types | Matching new and second-hand stock appears | Passed  |
| TC-10 | STK-07 | Filter stock by section | Only matching section is displayed | Passed  |
| TC-11 | STK-08 | Record intake and purchase details | Intake information is saved | Passed  |
| TC-12 | ORD-01 | Add a valid customer | Customer is saved | Passed  |
| TC-13 | ORD-01 | Enter an invalid email | Customer is not saved and an error appears | Passed  |
| TC-14 | ORD-01 | Enter an invalid telephone number | Customer is not saved and an error appears | Passed  |
| TC-15 | ORD-02 | Create a valid customer order | Order is saved | Passed |
| TC-16 | ORD-03 | Search by customer | Matching orders are displayed | Passed  |
| TC-17 | ORD-03 | Search by requested book | Matching orders are displayed | Passed  |
| TC-18 | ORD-04 | Change an order status | New status is displayed | Passed  |
| TC-19 | ORD-05 | View outstanding orders | Only outstanding orders are shown | Passed  |
| TC-20 | ORD-06 | Correct an order | Updated order details are displayed | Passed  |
| TC-21 | ORD-06 | Cancel an order | Order status becomes Cancelled | Passed  |
| TC-22 | ORD-07 | Record a customer contact attempt | Contact history is displayed | Passed  |
| TC-23 | ORD-08 | Process an eligible uncollected order | Order is updated and deposit credit is recorded | Passed  |
| TC-24 | Security | Submit a form with an invalid CSRF token | Request is rejected | Passed  |
| TC-25 | Persistence | Restart the application | Existing records remain available | Passed  |




