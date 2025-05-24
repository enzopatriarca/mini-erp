@extends('layouts.app')
@section('title',"Pedido #{$pedido->id}")

@section('content')
  <h1>Pedido #{{ $pedido->id }}</h1>
  <p><strong>Data:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</p>
  <p><strong>Status:</strong> {{ ucfirst($pedido->status) }}</p>
  <p><strong>Endereço:</strong> {{ $pedido->endereco }} (CEP: {{ $pedido->cep }})</p>

  <h2>Itens</h2>
  <table class="table">
    <thead>
      <tr>
        <th>Produto</th><th>Variação</th><th>Qtd</th><th>Unitário</th><th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pedido->itens as $item)
        @php
          $prod  = \App\Models\Produto::find($item['produto_id']);
          $label = $prod
            ? $prod->nome
            : ($item['nome'] ?? 'Produto removido');
        @endphp
        <tr>
          <td>{{ $label }}</td>
          <td>{{ $item['variacao'] ?: '—' }}</td>
          <td>{{ $item['quantidade'] }}</td>
          <td>R$ {{ number_format($item['preco_unitario'],2,',','.') }}</td>
          <td>R$ {{ number_format($item['quantidade'] * $item['preco_unitario'],2,',','.') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <ul class="list-group mb-4">
    <li class="list-group-item">Subtotal: R$ {{ number_format($pedido->subtotal,2,',','.') }}</li>
    <li class="list-group-item">Frete:    R$ {{ number_format($pedido->frete,2,',','.') }}</li>
    <li class="list-group-item fw-bold">Total:   R$ {{ number_format($pedido->total,2,',','.') }}</li>
  </ul>

  <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Voltar</a>
@endsection
