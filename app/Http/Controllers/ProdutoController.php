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
        // tudo em transação para segurança
        DB::transaction(function() use ($request) {
            // 1) cria o produto
            $produto = Produto::create($request->only(['nome','preco']));

            // 2) atualiza a coluna JSON 'variacoes'
            $variacoes = $request->input('variacoes', []);
            $produto->variacoes = $variacoes;
            $produto->save();

            // 3) persiste os estoques (uma linha por variação)
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
            // 1) atualiza nome e preço
            $produto->update($request->only(['nome','preco']));

            // 2) atualiza JSON de variações
            $variacoes = $request->input('variacoes', []);
            $produto->variacoes = $variacoes;
            $produto->save();

            // 3) apaga estoques antigos e recria
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
