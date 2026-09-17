<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Migration\Schema;
use Liberta\Sql\DB;

class CreateInventoryTables implements Migration
{
    public function name(): string
    {
        return '002_create_inventory_tables';
    }

    public function up(DB $db): void
    {
        $schema = new Schema($db);

        $schema->create('categories', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('description', 255)->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->timestamps();
        });

        $schema->create('products', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('name', 150);
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('unit', 20)->default('pcs');
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->index('category_id');
        });

        $schema->create('warehouses', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('address', 255)->nullable();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        $schema->create('stock_movements', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('product_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();
            $table->enum('type', ['in', 'out', 'transfer', 'adjustment']);
            $table->decimal('quantity', 15, 2);
            $table->string('reference', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('product_id');
            $table->index('warehouse_id');
        });

        $schema->create('stock_adjustments', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('product_id')->unsigned();
            $table->integer('warehouse_id')->unsigned();
            $table->decimal('quantity_before', 15, 2);
            $table->decimal('quantity_after', 15, 2);
            $table->string('reason', 255);
            $table->timestamps();
            $table->index('product_id');
            $table->index('warehouse_id');
        });
    }

    public function down(DB $db): void
    {
        $schema = new Schema($db);
        $schema->drop('stock_adjustments');
        $schema->drop('stock_movements');
        $schema->drop('warehouses');
        $schema->drop('products');
        $schema->drop('categories');
    }
}
