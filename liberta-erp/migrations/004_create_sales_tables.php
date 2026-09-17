<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Migration\Schema;
use Liberta\Sql\DB;

class CreateSalesTables implements Migration
{
    public function name(): string
    {
        return '004_create_sales_tables';
    }

    public function up(DB $db): void
    {
        $schema = new Schema($db);

        $schema->create('customers', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('contact', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        $schema->create('sales_orders', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('customer_id')->unsigned();
            $table->date('date');
            $table->enum('status', ['draft', 'confirmed', 'delivered', 'invoiced', 'cancelled'])->default('draft');
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('customer_id');
        });

        $schema->create('sales_order_items', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('sales_order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_price', 15, 2);
            $table->timestamps();
            $table->index('sales_order_id');
            $table->index('product_id');
        });

        $schema->create('delivery_notes', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('sales_order_id')->unsigned();
            $table->date('date');
            $table->string('reference', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('sales_order_id');
        });

        $schema->create('sales_invoices', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('sales_order_id')->unsigned();
            $table->string('invoice_number', 50);
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->decimal('total', 15, 2);
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->index('sales_order_id');
        });
    }

    public function down(DB $db): void
    {
        $schema = new Schema($db);
        $schema->drop('sales_invoices');
        $schema->drop('delivery_notes');
        $schema->drop('sales_order_items');
        $schema->drop('sales_orders');
        $schema->drop('customers');
    }
}
