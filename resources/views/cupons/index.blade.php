@extends('layouts.app')
@section('title','Cupons')

@section('content')
  <div class="d-flex justify-content-between mb-3">
    <h1>Cupons</h1>
    <a href="{{ route('cupons.create') }}" class="btn btn-success">+ Novo</a>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr><th>Código</th><th>Desconto</th><th>Mínimo Subtotal</th><th>Validade</th><th>Ações</th></tr>
    </thead>
    <tbody>
      @foreach($cupons as $c)
        <tr>
          <td>{{ $c->codigo }}</td>
          <td>R$ {{ number_format($c->desconto,2,',','.') }}</td>
          <td>R$ {{ number_format($c->minimo_subtotal,2,',','.') }}</td>
          <td>{{ $c->validade->format('d/m/Y') }}</td>
          <td>
            <a href="{{ route('cupons.edit',$c) }}" class="btn btn-sm btn-warning">Editar</a>
            <form action="{{ route('cupons.destroy',$c) }}" method="POST" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Remover?')">Excluir</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection
