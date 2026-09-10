<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('clientes');

        if (in_array('Nome', $columns, true)) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->renameColumn('Nome', 'nome_legado');
            });
            Schema::table('clientes', function (Blueprint $table) {
                $table->renameColumn('nome_legado', 'nome');
            });
        }

        if (in_array('Email', Schema::getColumnListing('clientes'), true)) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->renameColumn('Email', 'email_legado');
            });
            Schema::table('clientes', function (Blueprint $table) {
                $table->renameColumn('email_legado', 'email');
            });
        }

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email', 150)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('email', 150)->nullable(false)->change();
        });
    }
};
