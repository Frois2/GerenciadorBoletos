<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        return response()->json(
            Cliente::orderBy('nome')->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', 'unique:clientes,email'],
            'cpf_cnpj' => ['required', 'regex:/^(?:\d{3}\.\d{3}\.\d{3}-\d{2}|\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2})$/', Rule::unique('clientes', 'cpf_cnpj')],
        ], $this->mensagensValidacao());

        $cliente = Cliente::create($dados);

        return response()->json($cliente, 201);
    }

    public function show(Cliente $cliente)
    {
        return response()->json($cliente);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('clientes', 'email')->ignore($cliente)],
            'cpf_cnpj' => ['required', 'regex:/^(?:\d{3}\.\d{3}\.\d{3}-\d{2}|\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2})$/', Rule::unique('clientes', 'cpf_cnpj')->ignore($cliente)],
        ], $this->mensagensValidacao());

        $cliente->update($dados);

        return response()->json($cliente);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente excluído com sucesso.',
        ]);
    }

    private function mensagensValidacao(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome pode ter no máximo 150 caracteres.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail pode ter no máximo 150 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'cpf_cnpj.required' => 'O CPF/CNPJ é obrigatório.',
            'cpf_cnpj.regex' => 'Informe um CPF (000.000.000-00) ou CNPJ (00.000.000/0000-00) válido.',
            'cpf_cnpj.unique' => 'Este CPF/CNPJ já está cadastrado.',
        ];
    }
}
