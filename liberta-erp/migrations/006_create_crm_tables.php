<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Migration\Schema;
use Liberta\Sql\DB;

class CreateCrmTables implements Migration
{
    public function name(): string
    {
        return '006_create_crm_tables';
    }

    public function up(DB $db): void
    {
        $schema = new Schema($db);

        $schema->create('crm_contacts', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('company', 150)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        $schema->create('leads', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('contact_id')->unsigned();
            $table->string('source', 50)->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'unqualified', 'converted'])->default('new');
            $table->decimal('expected_value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('contact_id');
        });

        $schema->create('opportunities', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('lead_id')->unsigned()->nullable();
            $table->integer('contact_id')->unsigned();
            $table->string('name', 150);
            $table->enum('stage', ['prospecting', 'qualification', 'proposal', 'negotiation', 'closed_won', 'closed_lost'])->default('prospecting');
            $table->date('expected_close_date')->nullable();
            $table->decimal('value', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('lead_id');
            $table->index('contact_id');
        });

        $schema->create('activities', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('contact_id')->unsigned()->nullable();
            $table->integer('lead_id')->unsigned()->nullable();
            $table->integer('opportunity_id')->unsigned()->nullable();
            $table->enum('type', ['call', 'email', 'meeting', 'task', 'note']);
            $table->string('subject', 200);
            $table->text('description')->nullable();
            $table->datetime('due_date')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->index('contact_id');
            $table->index('lead_id');
            $table->index('opportunity_id');
        });

        $schema->create('notes', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('contact_id')->unsigned()->nullable();
            $table->integer('lead_id')->unsigned()->nullable();
            $table->integer('opportunity_id')->unsigned()->nullable();
            $table->string('title', 200);
            $table->text('content');
            $table->timestamps();
            $table->index('contact_id');
            $table->index('lead_id');
            $table->index('opportunity_id');
        });
    }

    public function down(DB $db): void
    {
        $schema = new Schema($db);
        $schema->drop('notes');
        $schema->drop('activities');
        $schema->drop('opportunities');
        $schema->drop('leads');
        $schema->drop('crm_contacts');
    }
}
