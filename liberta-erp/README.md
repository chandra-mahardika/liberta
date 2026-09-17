# liberta-erp

> Complete ERP modules (Accounting, Inventory, Purchasing, Sales, HR, CRM).

## Installation

Each module is independent. Use only what you need.

## Modules

### Accounting

```php
// Chart of Accounts
$accounts = $db->table('coa')->get();

// Journal Entries
$db->table('journal_entries')->insert([
    'date' => '2026-07-20',
    'description' => 'Sales invoice',
    'debit_account' => 1100, // Accounts Receivable
    'credit_account' => 4100, // Sales Revenue
    'amount' => 1000000,
]);

// Trial Balance
$trialBalance = $db->table('coa')
    ->select('account_name', DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
    ->groupBy('account_name')
    ->get();
```

### Inventory

```php
// Products
$products = $db->table('products')->where('active', '=', true)->get();

// Stock Movements
$db->table('stock_movements')->insert([
    'product_id' => 1,
    'warehouse_id' => 1,
    'quantity' => 100,
    'type' => 'in',
    'reference' => 'PO-001',
]);

// Stock Adjustment
$db->table('stock_adjustments')->insert([
    'product_id' => 1,
    'warehouse_id' => 1,
    'adjustment' => -5,
    'reason' => 'Damaged',
]);
```

### Purchasing

```php
// Purchase Orders
$po = $db->table('purchase_orders')->insert([
    'supplier_id' => 1,
    'order_date' => '2026-07-20',
    'status' => 'pending',
]);

// Purchase Receipts
$db->table('purchase_receipts')->insert([
    'po_id' => $po,
    'receipt_date' => '2026-07-25',
    'items' => json_encode([
        ['product_id' => 1, 'quantity' => 100, 'unit_price' => 10000],
    ]),
]);
```

### Sales

```php
// Customers
$customers = $db->table('customers')->get();

// Sales Orders
$so = $db->table('sales_orders')->insert([
    'customer_id' => 1,
    'order_date' => '2026-07-20',
    'status' => 'confirmed',
]);

// Sales Invoices
$db->table('sales_invoices')->insert([
    'so_id' => $so,
    'invoice_date' => '2026-07-20',
    'due_date' => '2026-08-20',
    'total' => 1000000,
]);
```

### HR

```php
// Employees
$employees = $db->table('employees')->get();

// Attendance
$db->table('attendance')->insert([
    'employee_id' => 1,
    'date' => '2026-07-20',
    'check_in' => '08:00:00',
    'check_out' => '17:00:00',
]);

// Payroll
$db->table('payroll')->insert([
    'employee_id' => 1,
    'period' => '2026-07',
    'basic_salary' => 5000000,
    'allowances' => 1000000,
    'deductions' => 500000,
    'net_salary' => 5500000,
]);
```

### CRM

```php
// Contacts
$contacts = $db->table('contacts')->get();

// Leads
$leads = $db->table('leads')
    ->where('status', '=', 'new')
    ->get();

// Opportunities
$opportunities = $db->table('opportunities')
    ->where('stage', '=', 'proposal')
    ->get();

// Activities
$db->table('activities')->insert([
    'contact_id' => 1,
    'type' => 'call',
    'subject' => 'Follow up on proposal',
    'scheduled_at' => '2026-07-25 10:00:00',
]);
```

## API Routes

Each module registers its own routes:

| Module | Routes |
|--------|--------|
| Accounting | `/chart-of-accounts`, `/journal-entries`, `/trial-balance` |
| Inventory | `/products`, `/categories`, `/warehouses`, `/stock-movements` |
| Purchasing | `/suppliers`, `/purchase-orders`, `/purchase-receipts` |
| Sales | `/customers`, `/sales-orders`, `/delivery-notes`, `/invoices` |
| HR | `/employees`, `/departments`, `/positions`, `/attendance`, `/payroll` |
| CRM | `/contacts`, `/leads`, `/opportunities`, `/activities`, `/notes` |

## Database Tables

### Accounting
- `coa` - Chart of Accounts
- `journal_entries` - Journal entries
- `ledger` - General ledger

### Inventory
- `products` - Products
- `categories` - Product categories
- `warehouses` - Warehouses
- `stock_movements` - Stock movements
- `stock_adjustments` - Stock adjustments

### Purchasing
- `suppliers` - Suppliers
- `purchase_orders` - Purchase orders
- `purchase_receipts` - Purchase receipts
- `purchase_invoices` - Purchase invoices

### Sales
- `customers` - Customers
- `sales_orders` - Sales orders
- `delivery_notes` - Delivery notes
- `sales_invoices` - Sales invoices

### HR
- `employees` - Employees
- `departments` - Departments
- `positions` - Positions
- `attendance` - Attendance
- `payroll` - Payroll

### CRM
- `contacts` - Contacts
- `leads` - Leads
- `opportunities` - Opportunities
- `activities` - Activities
- `notes` - Notes
