<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (in_array('CPFCNPJ', Schema::getColumnListing('clientes'), true)) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->renameColumn('CPFCNPJ', 'cpf_cnpj');
            });
        }
    }

    public function down(): void
    {
        if (in_array('cpf_cnpj', Schema::getColumnListing('clientes'), true)) {
            Schema::table('clientes', function (Blueprint $table) {
                $table->renameColumn('cpf_cnpj', 'CPFCNPJ');
            });
        }
    }
};
