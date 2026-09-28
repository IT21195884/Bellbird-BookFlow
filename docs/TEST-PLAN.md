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
| TC-01 | STK-01 | Add a valid new-book title | Book is saved and displayed | Not Tested |
| TC-02 | STK-01 | Submit new book without a title | Validation message is displayed | Not Tested |
| TC-03 | STK-02 | Increase new-book quantity | Quantity increases correctly | Not Tested |
| TC-04 | STK-02 | Reduce quantity below zero | Update is rejected | Not Tested |
| TC-05 | STK-03 | Add a second-hand copy | Individual copy is displayed | Not Tested |
| TC-06 | STK-04 | Edit a new-book record | Updated information is displayed | Not Tested |
| TC-07 | STK-04 | Edit a second-hand copy | Updated information is displayed | Not Tested |
| TC-08 | STK-05 | Mark second-hand copy unavailable | Copy no longer appears as available | Not Tested |
| TC-09 | STK-06 | Search across both stock types | Matching new and second-hand stock appears | Not Tested |
| TC-10 | STK-07 | Filter stock by section | Only matching section is displayed | Not Tested |
| TC-11 | STK-08 | Record intake and purchase details | Intake information is saved | Not Tested |
| TC-12 | ORD-01 | Add a valid customer | Customer is saved | Not Tested |
| TC-13 | ORD-01 | Enter an invalid email | Customer is not saved and an error appears | Not Tested |
| TC-14 | ORD-01 | Enter an invalid telephone number | Customer is not saved and an error appears | Not Tested |
| TC-15 | ORD-02 | Create a valid customer order | Order is saved | Not Tested |
| TC-16 | ORD-03 | Search by customer | Matching orders are displayed | Not Tested |
| TC-17 | ORD-03 | Search by requested book | Matching orders are displayed | Not Tested |
| TC-18 | ORD-04 | Change an order status | New status is displayed | Not Tested |
| TC-19 | ORD-05 | View outstanding orders | Only outstanding orders are shown | Not Tested |
| TC-20 | ORD-06 | Correct an order | Updated order details are displayed | Not Tested |
| TC-21 | ORD-06 | Cancel an order | Order status becomes Cancelled | Not Tested |
| TC-22 | ORD-07 | Record a customer contact attempt | Contact history is displayed | Not Tested |
| TC-23 | ORD-08 | Process an eligible uncollected order | Order is updated and deposit credit is recorded | Not Tested |
| TC-24 | Security | Submit a form with an invalid CSRF token | Request is rejected | Not Tested |
| TC-25 | Persistence | Restart the application | Existing records remain available | Not Tested |

## 7. Defect Severity

- Critical: Application cannot start or important data is lost.
- High: A major stock or order function does not work.
- Medium: A function works incorrectly but has a workaround.
- Low: A visual, wording or minor usability problem.

## 8. Test Evidence

Evidence should include:

- Screenshots of successful tests
- Screenshots of validation errors
- GitHub Actions results
- Relevant Jira issue links
- Defect records where applicable