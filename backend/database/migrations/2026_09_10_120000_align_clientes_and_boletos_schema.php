<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nome', 150)->change();
            $table->string('cpf_cnpj', 20)->change();
            $table->string('email', 150)->change();
        });

        if (Schema::hasColumn('boletos', 'descricao')) {
            Schema::table('boletos', function (Blueprint $table) {
                $table->renameColumn('descricao', 'desc');
            });
        }

        Schema::table('boletos', function (Blueprint $table) {
            $table->string('status', 20)->default('pendente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('boletos', function (Blueprint $table) {
            $table->enum('status', ['pendente', 'pago', 'vencido'])->default('pendente')->change();
        });

        Schema::table('boletos', function (Blueprint $table) {
            $table->renameColumn('desc', 'descricao');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->string('nome')->change();
            $table->string('cpf_cnpj', 18)->change();
            $table->string('email')->change();
        });
    }
};
