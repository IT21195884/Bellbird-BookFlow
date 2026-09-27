PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS new_books (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    isbn TEXT,
    cost_price REAL CHECK (
        cost_price IS NULL OR cost_price >= 0
    ),
    selling_price REAL NOT NULL CHECK (
        selling_price >= 0
    ),
    quantity INTEGER NOT NULL DEFAULT 0 CHECK (
        quantity >= 0
    ),
    section TEXT NOT NULL,
    shelf_location TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS second_hand_copies (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    author TEXT NOT NULL,
    condition_grade TEXT NOT NULL CHECK (
        condition_grade IN (
            'As New',
            'Very Good',
            'Good',
            'Fair',
            'Reading Copy'
        )
    ),
    purchase_price REAL CHECK (
        purchase_price IS NULL OR purchase_price >= 0
    ),
    selling_price REAL NOT NULL CHECK (
        selling_price >= 0
    ),
    section TEXT NOT NULL,
    shelf_location TEXT NOT NULL,
    intake_reference TEXT,
    intake_date TEXT,
    acquisition_source TEXT,
    notes TEXT,
    status TEXT NOT NULL DEFAULT 'Available' CHECK (
        status IN ('Available', 'Sold', 'Removed')
    ),
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    phone TEXT,
    email TEXT,
    preferred_contact TEXT NOT NULL CHECK (
        preferred_contact IN (
            'Call',
            'Text',
            'Email',
            'Customer will contact shop'
        )
    ),
    notes TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS customer_orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_id INTEGER NOT NULL,
    book_title TEXT NOT NULL,
    book_author TEXT,
    quantity INTEGER NOT NULL DEFAULT 1 CHECK (
        quantity > 0
    ),
    stock_preference TEXT NOT NULL DEFAULT 'Either' CHECK (
        stock_preference IN (
            'New',
            'Second-hand',
            'Either'
        )
    ),
    status TEXT NOT NULL DEFAULT 'Unfulfilled' CHECK (
        status IN (
            'Unfulfilled',
            'Ordered',
            'Arrived',
            'Customer Notified',
            'Collected',
            'Cancelled',
            'Returned to Shelf'
        )
    ),
    order_date TEXT NOT NULL,
    arrival_date TEXT,
    notification_date TEXT,
    collection_date TEXT,
    deposit_amount REAL CHECK (
        deposit_amount IS NULL OR deposit_amount >= 0
    ),
    store_credit_amount REAL CHECK (
        store_credit_amount IS NULL
        OR store_credit_amount >= 0
    ),
    notes TEXT,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id)
        REFERENCES customers(id)
        ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS contact_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    contact_date TEXT NOT NULL,
    method TEXT NOT NULL CHECK (
        method IN ('Call', 'Text', 'Email')
    ),
    outcome TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id)
        REFERENCES customer_orders(id)
        ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_new_books_title_author
ON new_books(title, author);

CREATE INDEX IF NOT EXISTS idx_second_hand_title_author
ON second_hand_copies(title, author);

CREATE INDEX IF NOT EXISTS idx_customer_name
ON customers(name);

CREATE INDEX IF NOT EXISTS idx_order_title_status
ON customer_orders(book_title, status);