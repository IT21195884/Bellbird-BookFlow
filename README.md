# Bellbird BookFlow

Bellbird BookFlow is a web-based stock and customer-order management system developed for Bellbird Books, an independent bookshop that sells both new and second-hand books.

The system provides a central and reliable record of available stock and customer orders. It is intended to replace the shop’s paper notebooks, second-hand intake records and outdated spreadsheet.

## Project Objectives

Bellbird BookFlow aims to:

- Maintain accurate records of new and second-hand books.
- Help staff find books without manually checking shelves.
- Reduce duplicate stock orders.
- Track customer orders from request to collection.
- Allow staff to search stock and orders quickly.
- Improve customer service at the shop counter.
- Protect customer contact information.
- Provide a simple interface requiring minimal staff training.

## Core Features

### New-Book Management

- Add, view, update and remove new-book records.
- Record title, author, price and shelf location.
- Manage identical new books using stock quantities.
- Increase quantities when stock arrives.
- Reduce quantities when books are sold.

### Second-Hand Book Management

- Record every second-hand copy individually.
- Store each copy’s condition, purchase price, selling price and location.
- Support different prices and conditions for copies of the same title.
- Update or remove one copy without affecting other copies.

Supported condition categories include:

- As New
- Very Good
- Good
- Fair
- Reading Copy

### Stock Search

- Search both new and second-hand stock together.
- Search using a full or partial title.
- Search using a full or partial author name.
- View availability, price, condition and shelf location.
- Display a clear message when no matching stock is found.

### Customer and Order Management

- Record customer names and contact information.
- Store preferred contact methods.
- Create and update customer book orders.
- Search orders by customer or book title.
- Track outstanding and completed orders.
- Correct or cancel an order without affecting other orders.

Planned order statuses include:

- Unfulfilled
- Ordered
- Arrived
- Customer Notified
- Collected
- Cancelled
- Returned to Shelf

## Critical Business Rule

Bellbird Books manages its two types of stock differently:

- New books of the same title and edition are stored as one record with a quantity.
- Every second-hand book is stored as an individual physical copy because each copy may have a different condition, price and location.

Selling or removing one second-hand copy must not affect another copy of the same title.

## Project Scope

The initial release focuses on:

- New-book stock management
- Individual second-hand book management
- Combined stock searching
- Customer record management
- Customer-order tracking
- Stock and outstanding-order views
- Persistent database storage
- Input validation
- Automated testing

## Out of Scope

The initial release does not include:

- Card or payment processing
- Square integration
- Xero or accounting integration
- Supplier-system integration
- Barcode scanning
- Online shopping
- Loyalty programs
- Automatic email or SMS notifications
- Book photographs
- Advanced reporting and analytics

These features may be considered for future development.

## Technology Stack

The planned technology stack includes:

- Python
- Flask
- HTML
- CSS
- Bootstrap
- SQLite
- pytest
- Git and GitHub

## Project Structure

```text
bellbird-bookflow/
├── app/
│   ├── templates/
│   ├── static/
│   ├── models/
│   └── routes/
├── tests/
├── config/
├── .env.example
├── .gitignore
├── requirements.txt
├── README.md
└── run.py
