<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query  = Produto::with('estoques');

        if ($search) {
            $query->where('nome', 'like', "%{$search}%")
                ->orWhereRaw("JSON_CONTAINS(variacoes, '\"{$search}\"')");
        }

        $produtos = $query->latest()->paginate(10)->withQueryString();

        return view('produtos.index', compact('produtos','search'));
    }

    public function create()
    {
        return view('produtos.form');
    }

    public function store(ProdutoRequest $request)
    {
        DB::transaction(function() use ($request) {

            $produto = Produto::create($request->only(['nome','preco']));

            $variacoes = $request->input('variacoes', []);
            $produto->variacoes = $variacoes;
            $produto->save();

            foreach ($variacoes as $i => $var) {
                $produto->estoques()->create([
                    'variacao'   => $var ?: 'default',
                    'quantidade' => $request->estoque[$i] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('produtos.index')
            ->with('success','Produto cadastrado com sucesso.');
    }

    public function edit(Produto $produto)
    {
        $produto->load('estoques');
        return view('produtos.form', compact('produto'));
    }

    public function update(ProdutoRequest $request, Produto $produto)
    {
        DB::transaction(function() use ($request, $produto) {
            $produto->update($request->only(['nome','preco']));

            $variacoes = $request->input('variacoes', []);
            $produto->variacoes = $variacoes;
            $produto->save();
            $produto->estoques()->delete();
            foreach ($variacoes as $i => $var) {
                $produto->estoques()->create([
                    'variacao'   => $var ?: 'default',
                    'quantidade' => $request->estoque[$i] ?? 0,
                ]);
            }
        });

        return redirect()
            ->route('produtos.index')
            ->with('success','Produto atualizado com sucesso.');
    }

    public function destroy(Produto $produto)
    {
        $produto->estoques()->delete();
        $produto->delete();

        return redirect()->route('produtos.index')
                         ->with('success','Produto removido.');
    }
}
