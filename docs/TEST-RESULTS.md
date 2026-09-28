# Bellbird BookFlow Test Results

## Release Information

- Version: 1.0.0
- Branch: release/v1.0.0
- Tester: Project team
- Test environment: Local PHP server and SQLite database
- Test date: 28 September 2026

## Test Summary

| Measurement | Result |
|---|---:|
| Total test cases | 25 |
| Passed | 25 |
| Failed | 0 |
| Not tested | 0 |
| Critical defects | 0 |

## Results

All test cases defined in `TEST-PLAN.md` were completed successfully.

| Test ID | Result | Evidence or Notes |
|---|---|---|
| TC-01 | Pass | A valid new-book title was saved and displayed correctly. |
| TC-02 | Pass | The system rejected a new-book submission without a title and displayed a validation message. |
| TC-03 | Pass | The new-book quantity increased correctly. |
| TC-04 | Pass | The system prevented the stock quantity from being reduced below zero. |
| TC-05 | Pass | An individual second-hand copy was saved and displayed correctly. |
| TC-06 | Pass | Changes to a new-book record were saved and displayed correctly. |
| TC-07 | Pass | Changes to a second-hand copy were saved and displayed correctly. |
| TC-08 | Pass | A second-hand copy marked unavailable no longer appeared as available stock. |
| TC-09 | Pass | Combined search returned matching new and second-hand stock. |
| TC-10 | Pass | The section filter displayed only stock from the selected shop section. |
| TC-11 | Pass | Second-hand intake and purchase information was saved correctly. |
| TC-12 | Pass | A customer with valid details was saved successfully. |
| TC-13 | Pass | An invalid email address was rejected and a validation message was displayed. |
| TC-14 | Pass | An invalid telephone number was rejected and a validation message was displayed. |
| TC-15 | Pass | A valid customer order was created and displayed successfully. |
| TC-16 | Pass | Searching by customer returned the correct matching orders. |
| TC-17 | Pass | Searching by requested book returned the correct matching orders. |
| TC-18 | Pass | The order status was changed and the updated status was displayed correctly. |
| TC-19 | Pass | The outstanding-orders filter displayed only orders that still required action. |
| TC-20 | Pass | Corrected order information was saved and displayed correctly. |
| TC-21 | Pass | The selected order was successfully changed to Cancelled status. |
| TC-22 | Pass | A customer contact attempt was recorded and displayed in the contact history. |
| TC-23 | Pass | An eligible uncollected order was processed and its deposit credit was recorded. |
| TC-24 | Pass | A form request containing an invalid CSRF token was rejected. |
| TC-25 | Pass | Previously saved information remained available after the application was restarted. |

## Defects

| Defect ID | Description | Severity | Status | Related Test |
|---|---|---|---|---|
| None | No unresolved defects were identified during release testing. | — | Closed | — |

## Test Conclusion

All 25 planned test cases passed. No failed or untested cases remain, and no critical or high-severity defects were identified. The application satisfies the tested stock-management, customer-management, order-management, validation, security and data-persistence requirements.

## Release Recommendation

Bellbird BookFlow version 1.0.0 is recommended for release. All critical functions passed testing, and no unresolved defects prevent deployment.