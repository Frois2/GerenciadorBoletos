<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Boleto;
use Illuminate\Http\Request;

class BoletoController extends Controller
{
    public function index()
    {
        return response()->json(
            Boleto::with('cliente')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'desc' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'vencimento' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'status' => ['required', 'in:pendente,pago'],
        ], $this->mensagensValidacao());

        $boleto = Boleto::create($dados);

        return response()->json($boleto->load('cliente'), 201);
    }

    public function show(Boleto $boleto)
    {
        return response()->json($boleto->load('cliente'));
    }

    public function update(Request $request, Boleto $boleto)
    {
        $dados = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'desc' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0.01'],
            'vencimento' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'status' => ['required', 'in:pendente,pago'],
        ], $this->mensagensValidacao());

        $boleto->update($dados);

        return response()->json($boleto->load('cliente'));
    }

    public function destroy(Boleto $boleto)
    {
        $boleto->delete();

        return response()->json([
            'message' => 'Boleto excluído com sucesso.'
        ]);
    }

    private function mensagensValidacao(): array
    {
        return [
            'cliente_id.required' => 'Selecione um cliente.',
            'cliente_id.exists' => 'O cliente selecionado não existe.',
            'desc.required' => 'A descrição é obrigatória.',
            'desc.max' => 'A descrição pode ter no máximo 255 caracteres.',
            'valor.required' => 'O valor é obrigatório.',
            'valor.numeric' => 'Informe um valor decimal válido.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'vencimento.required' => 'O vencimento é obrigatório.',
            'vencimento.date_format' => 'Digite o vencimento no formato dd/mm/aaaa.',
            'vencimento.after_or_equal' => 'O vencimento não pode ser uma data passada.',
            'status.required' => 'Selecione o status.',
            'status.in' => 'O status deve ser pendente ou pago.',
        ];
    }
}
