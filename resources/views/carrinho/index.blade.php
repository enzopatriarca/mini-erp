@extends('layouts.app')
@section('title','Carrinho')

@section('content')
  <h1>Meu Carrinho</h1>

  @if(count($itens))
    <table class="table">
      <thead>
        <tr>
          <th>Produto</th><th>Variação</th><th>Qtd</th><th>Unitário</th><th>Total</th>
        </tr>
      </thead>
      <tbody>
      @foreach($itens as $item)
        @php $p = \App\Models\Produto::find($item['produto_id']); @endphp
        <tr>
          <td>{{ $p->nome }}</td>
          <td>{{ $item['variacao'] ?: '—' }}</td>
          <td>{{ $item['quantidade'] }}</td>
          <td>R$ {{ number_format($item['preco_unitario'],2,',','.') }}</td>
          <td>R$ {{ number_format($item['quantidade'] * $item['preco_unitario'],2,',','.') }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>

    {{-- Cupom --}}
    <form class="row g-2 mb-3" method="GET">
      <div class="col-auto">
        <input type="text" name="cupom" value="{{ $request->cupom }}" class="form-control" placeholder="Código do cupom">
      </div>
      <div class="col-auto">
        <button class="btn btn-secondary">Aplicar cupom</button>
      </div>
    </form>

    {{-- Totais --}}
    <ul class="list-group mb-4">
      <li class="list-group-item">Subtotal: R$ {{ number_format($subtotal,2,',','.') }}</li>
      <li class="list-group-item">Frete:    R$ {{ number_format($frete,2,',','.') }}</li>
      <li class="list-group-item fw-bold">Total: R$ {{ number_format($subtotal + $frete,2,',','.') }}</li>
    </ul>

    {{-- Finalizar Pedido --}}
    <form action="{{ route('pedido.finalizar') }}" method="POST">
      @csrf

      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">CEP</label>
          <input type="text" id="cep" name="cep" class="form-control" required>
        </div>
        <div class="col-auto align-self-end mb-3">
          <button type="button" id="buscarCep" class="btn btn-outline-secondary">Buscar Endereço</button>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Endereço</label>
        <input type="text" id="endereco" name="endereco" class="form-control" readonly>
      </div>

      <button class="btn btn-primary">Finalizar Pedido</button>
    </form>
  @else
    <p>Seu carrinho está vazio.</p>
  @endif
@endsection

@push('scripts')
<script>
document.getElementById('buscarCep').addEventListener('click', async () => {
  const cep = document.getElementById('cep').value.replace(/\D/g,'');
  const res = await fetch(`{{ route('viacep.buscar','') }}/${cep}`);
  const data = await res.json();
  if (!data.erro) {
    document.getElementById('endereco').value =
      `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
  }
});
</script>
@endpush
