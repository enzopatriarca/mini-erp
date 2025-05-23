<?php
namespace App\Http\Controllers;

use App\Http\Requests\CupomRequest;
use App\Models\Cupom;
use Illuminate\Http\Request;

class CupomController extends Controller
{
    public function index()
    {
        $cupons = Cupom::all();
        return view('cupons.index', compact('cupons'));
    }

    public function create()
    {
        return view('cupons.form');
    }

    public function store(CupomRequest $request)
    {
        Cupom::create($request->validated());
        return redirect()->route('cupons.index')
                         ->with('success','Cupom criado.');
    }

    public function edit(Cupom $cupom)
    {
        return view('cupons.form', compact('cupom'));
    }

    public function update(CupomRequest $request, Cupom $cupom)
    {
        $cupom->update($request->validated());
        return redirect()->route('cupons.index')
                         ->with('success','Cupom atualizado.');
    }

    public function destroy(Cupom $cupom)
    {
        $cupom->delete();
        return redirect()->route('cupons.index')
                         ->with('success','Cupom removido.');
    }
}
