<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Migration\Schema;
use Liberta\Sql\DB;

class CreatePurchasingTables implements Migration
{
    public function name(): string
    {
        return '003_create_purchasing_tables';
    }

    public function up(DB $db): void
    {
        $schema = new Schema($db);

        $schema->create('suppliers', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('contact', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        $schema->create('purchase_orders', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('supplier_id')->unsigned();
            $table->date('date');
            $table->enum('status', ['draft', 'confirmed', 'received', 'cancelled'])->default('draft');
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('supplier_id');
        });

        $schema->create('purchase_order_items', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('purchase_order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->timestamps();
            $table->index('purchase_order_id');
            $table->index('product_id');
        });

        $schema->create('purchase_receipts', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('purchase_order_id')->unsigned();
            $table->date('date');
            $table->string('reference', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('purchase_order_id');
        });

        $schema->create('purchase_invoices', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('purchase_order_id')->unsigned();
            $table->string('invoice_number', 50);
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->decimal('total', 15, 2);
            $table->enum('status', ['draft', 'received', 'paid', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->index('purchase_order_id');
        });
    }

    public function down(DB $db): void
    {
        $schema = new Schema($db);
        $schema->drop('purchase_invoices');
        $schema->drop('purchase_receipts');
        $schema->drop('purchase_order_items');
        $schema->drop('purchase_orders');
        $schema->drop('suppliers');
    }
}
