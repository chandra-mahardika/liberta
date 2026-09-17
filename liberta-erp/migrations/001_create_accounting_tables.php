<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Migration\Schema;
use Liberta\Sql\DB;

class CreateAccountingTables implements Migration
{
    public function name(): string
    {
        return '001_create_accounting_tables';
    }

    public function up(DB $db): void
    {
        $schema = new Schema($db);

        $schema->create('chart_of_accounts', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->enum('normal_balance', ['debit', 'credit']);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        $schema->create('journal_entries', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->date('date');
            $table->string('description', 255);
            $table->string('reference', 50)->nullable();
            $table->enum('status', ['draft', 'posted', 'voided'])->default('draft');
            $table->timestamps();
        });

        $schema->create('journal_lines', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('journal_entry_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->timestamps();
            $table->index('journal_entry_id');
            $table->index('account_id');
        });
    }

    public function down(DB $db): void
    {
        $schema = new Schema($db);
        $schema->drop('journal_lines');
        $schema->drop('journal_entries');
        $schema->drop('chart_of_accounts');
    }
}
