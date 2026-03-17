-- This file contains the table schemas that need to be created
-- Based on the Laravel models in app/Models/

-- Clients table
CREATE TABLE IF NOT EXISTS clients (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    email VARCHAR(255),
    address VARCHAR(255),
    city VARCHAR(255),
    country VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Suppliers table  
CREATE TABLE IF NOT EXISTS suppliers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    email VARCHAR(255),
    address VARCHAR(255),
    city VARCHAR(255),
    country VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Currencies table
CREATE TABLE IF NOT EXISTS currencies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(10),
    symbol VARCHAR(10),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Measures table
CREATE TABLE IF NOT EXISTS measures (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Product categories  
CREATE TABLE IF NOT EXISTS products_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    parent_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Product brands
CREATE TABLE IF NOT EXISTS products_brands (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(255),
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    details_ar LONGTEXT,
    details_en LONGTEXT,
    product_category_id BIGINT,
    product_brand_id BIGINT,
    measure_id BIGINT,
    currency_id BIGINT,
    cost_price DECIMAL(10,2),
    price DECIMAL(10,2),
    tax DECIMAL(10,2),
    stock_alert INT,
    img VARCHAR(255),
    comment LONGTEXT,
    parent_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Variants table
CREATE TABLE IF NOT EXISTS variants (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Product combinations
CREATE TABLE IF NOT EXISTS product_combinations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT,
    variant_id BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Product images
CREATE TABLE IF NOT EXISTS product_images (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT,
    path VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Sale statuses
CREATE TABLE IF NOT EXISTS sale_statuses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Sales table
CREATE TABLE IF NOT EXISTS sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    client_id BIGINT,
    warehouse_id BIGINT,
    sale_status_id BIGINT,
    invoice_number VARCHAR(255),
    sale_date DATE,
    total_amount DECIMAL(15,2),
    tax_amount DECIMAL(15,2),
    notes LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Sale details
CREATE TABLE IF NOT EXISTS sale_details (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sale_id BIGINT,
    product_id BIGINT,
    quantity INT,
    unit_price DECIMAL(15,2),
    total_price DECIMAL(15,2),
    tax DECIMAL(15,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Purchase statuses
CREATE TABLE IF NOT EXISTS purchase_statuses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Purchases table
CREATE TABLE IF NOT EXISTS purchases (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    supplier_id BIGINT,
    warehouse_id BIGINT,
    purchase_status_id BIGINT,
    invoice_number VARCHAR(255),
    purchase_date DATE,
    total_amount DECIMAL(15,2),
    tax_amount DECIMAL(15,2),
    notes LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Purchase details
CREATE TABLE IF NOT EXISTS purchase_details (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_id BIGINT,
    product_id BIGINT,
    quantity INT,
    unit_price DECIMAL(15,2),
    total_price DECIMAL(15,2),
    tax DECIMAL(15,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Expenses categories
CREATE TABLE IF NOT EXISTS expenses_categories (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    expenses_category_id BIGINT,
    amount DECIMAL(15,2),
    description LONGTEXT,
    expense_date DATE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payment statuses
CREATE TABLE IF NOT EXISTS payment_statuses (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payment types
CREATE TABLE IF NOT EXISTS payment_types (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255),
    label_en VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    payment_status_id BIGINT,
    payment_type_id BIGINT,
    amount DECIMAL(15,2),
    payment_date DATE,
    reference_number VARCHAR(255),
    notes LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Payment sales (junction table)
CREATE TABLE IF NOT EXISTS payment_sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    payment_id BIGINT,
    sale_id BIGINT,
    amount DECIMAL(15,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Adjustments
CREATE TABLE IF NOT EXISTS adjustments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    warehouse_id BIGINT,
    adjustment_date DATE,
    notes LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Adjustment details
CREATE TABLE IF NOT EXISTS adjustment_details (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    adjustment_id BIGINT,
    product_id BIGINT,
    quantity_before INT,
    quantity_after INT,
    reason VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- Configurations
CREATE TABLE IF NOT EXISTS configurations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    key VARCHAR(255) NOT NULL UNIQUE,
    value LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
