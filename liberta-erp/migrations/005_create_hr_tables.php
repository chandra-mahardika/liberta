<?php

namespace Database\Migrations;

use Liberta\Migration\Migration;
use Liberta\Migration\Schema;
use Liberta\Sql\DB;

class CreateHrTables implements Migration
{
    public function name(): string
    {
        return '005_create_hr_tables';
    }

    public function up(DB $db): void
    {
        $schema = new Schema($db);

        $schema->create('departments', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 100);
            $table->integer('manager_id')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $schema->create('positions', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('level', 50)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        $schema->create('employees', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->string('name', 150);
            $table->integer('department_id')->unsigned()->nullable();
            $table->integer('position_id')->unsigned()->nullable();
            $table->date('hire_date');
            $table->decimal('salary', 15, 2)->default(0);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->timestamps();
            $table->index('department_id');
            $table->index('position_id');
        });

        $schema->create('attendance', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('employee_id')->unsigned();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'leave'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('employee_id');
        });

        $schema->create('payroll', function (\Liberta\Migration\Table $table) {
            $table->id();
            $table->integer('employee_id')->unsigned();
            $table->string('period', 20);
            $table->decimal('base_salary', 15, 2);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->timestamps();
            $table->index('employee_id');
        });
    }

    public function down(DB $db): void
    {
        $schema = new Schema($db);
        $schema->drop('payroll');
        $schema->drop('attendance');
        $schema->drop('employees');
        $schema->drop('positions');
        $schema->drop('departments');
    }
}
