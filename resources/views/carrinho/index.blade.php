{{-- resources/views/carrinho/index.blade.php --}}
@extends('layouts.app')
@section('title','Carrinho')

@section('content')
  <h1 class="mb-4">Seu Carrinho</h1>

  {{-- Feedback gerais --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error') && ! session('cupom_error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if(empty($itens))
    <div class="alert alert-info">Seu carrinho está vazio.</div>
  @else
    {{-- Cartão de Cupom --}}
    @php
      $cupomError   = session('cupom_error');
      $cupomSuccess = session('cupom_success');
    @endphp
    <div class="card mb-4">
      <div class="card-header">Cupom de desconto</div>
      <div class="card-body">
        <form method="GET" action="{{ route('carrinho.index') }}">
          <div class="row g-3">
            <div class="col-md-9">
              <div class="form-floating">
                <input
                  type="text"
                  name="cupom"
                  id="cupom"
                  class="form-control @if($cupomError) is-invalid @endif"
                  placeholder="Código do cupom"
                  value="{{ request('cupom','') }}"
                >
                <label for="cupom">Código do cupom</label>
                @if($cupomError)
                  <div class="invalid-feedback">
                    {{ $cupomError }}
                  </div>
                @endif
              </div>
            </div>
            <div class="col-md-3">
              <button 
                class="btn btn-primary btn-lg w-100" 
                type="submit"
              >
                Aplicar cupom
              </button>
            </div>
          </div>
        </form>

        @if($cupomSuccess)
          <div class="mt-3 text-success">
            ✓ Cupom “{{ request('cupom') }}” aplicado com sucesso!
          </div>
        @endif
      </div>
    </div>

    {{-- Tabela de Itens --}}
    <div class="table-responsive mb-4">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Produto</th>
            <th>Variação</th>
            <th>Preço unit.</th>
            <th>Qtd.</th>
            <th>Subtotal</th>
            <th class="text-end">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach($itens as $i => $item)
            @php
              $prod = \App\Models\Produto::find($item['produto_id']);
              $line = $item['preco_unitario'] * $item['quantidade'];
            @endphp
            <tr>
              <td>{{ $prod->nome }}</td>
              <td>{{ $item['variacao'] ?? '—' }}</td>
              <td>R$ {{ number_format($item['preco_unitario'],2,',','.') }}</td>
              <td style="width:120px;">
                <form action="{{ route('carrinho.atualizar') }}" method="POST" class="d-flex">
                  @csrf
                  <input type="hidden" name="index" value="{{ $i }}">
                  <input
                    type="number"
                    name="quantidade"
                    value="{{ $item['quantidade'] }}"
                    min="1"
                    class="form-control form-control-sm me-2"
                  >
                  <button class="btn btn-sm btn-secondary">OK</button>
                </form>
              </td>
              <td>R$ {{ number_format($line,2,',','.') }}</td>
              <td class="text-end">
                <form action="{{ route('carrinho.remover') }}" method="POST">
                  @csrf
                  <input type="hidden" name="index" value="{{ $i }}">
                  <button class="btn btn-sm btn-danger">×</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Resumo e Finalizar --}}
    <div class="row align-items-end">
      <div class="col-md-8">
        <ul class="list-group list-group-flush w-auto">
          {{-- Subtotal antes do desconto --}}
          <li class="list-group-item d-flex justify-content-between">
            <span>Subtotal</span>
            <strong>
              R$ {{ number_format($subtotal + $desconto,2,',','.') }}
            </strong>
          </li>
          {{-- Frete --}}
          <li class="list-group-item d-flex justify-content-between">
            <span>Frete</span>
            <strong>R$ {{ number_format($frete,2,',','.') }}</strong>
          </li>
          {{-- Desconto aplicado --}}
          @if($desconto > 0)
            <li class="list-group-item d-flex justify-content-between">
              <span>Desconto</span>
              <strong class="text-success">- R$ {{ number_format($desconto,2,',','.') }}</strong>
            </li>
          @endif
          {{-- Total final --}}
          <li class="list-group-item d-flex justify-content-between">
            <span>Total</span>
            <strong>R$ {{ number_format($total,2,',','.') }}</strong>
          </li>
        </ul>
      </div>
      <div class="col-md-4 text-end">
        <form action="{{ route('pedidos.create') }}" method="GET">
          @csrf
          <button
            type="submit"
            class="btn btn-success btn-lg px-5"
            {{ empty($itens) ? 'disabled' : '' }}
          >
            Finalizar Pedido
          </button>
        </form>
      </div>
    </div>
  @endif
@endsection
