{{-- resources/views/pedidos/create.blade.php --}}
@extends('layouts.app')
@section('title','Finalizar Pedido')

@section('content')
  <h1 class="mb-4">Finalizar Pedido</h1>

  {{-- Se por algum motivo chegar aqui sem itens, redireciona de volta --}}
  @if(empty($itens))
    <div class="alert alert-info">
      Seu carrinho está vazio. <a href="{{ route('produtos.index') }}">Voltar aos produtos.</a>
    </div>
  @else

    <div class="row">
      {{-- Resumo do Carrinho --}}
      <div class="col-md-5 mb-4">
        <div class="card">
          <div class="card-header bg-secondary text-white">
            Resumo do Pedido
          </div>
          <ul class="list-group list-group-flush">
            @foreach($itens as $item)
              @php
                $prod = \App\Models\Produto::find($item['produto_id']);
              @endphp
              <li class="list-group-item d-flex justify-content-between">
                {{ $prod->nome }}
                <span>x {{ $item['quantidade'] }}</span>
              </li>
            @endforeach
            <li class="list-group-item d-flex justify-content-between">
              <strong>Subtotal</strong>
              <strong>R$ {{ number_format($subtotal,2,',','.') }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <strong>Frete</strong>
              <strong>R$ {{ number_format($frete,2,',','.') }}</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between">
              <strong>Total</strong>
              <strong>R$ {{ number_format($total,2,',','.') }}</strong>
            </li>
          </ul>
        </div>
      </div>

      {{-- Form de Finalização --}}
      <div class="col-md-7">
        <div class="card">
          <div class="card-header">
            Seus Dados
          </div>
          <div class="card-body">
            <form action="{{ route('pedido.finalizar') }}" method="POST">
              @csrf

              {{-- Nome --}}
              <div class="mb-3">
                <label class="form-label">Nome completo</label>
                <input type="text" name="nome"
                       class="form-control @error('nome') is-invalid @enderror"
                       value="{{ old('nome') }}" required>
                @error('nome')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- E-mail --}}
              <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- CEP --}}
              <div class="mb-3">
                <label class="form-label">CEP</label>
                <input type="text" name="cep" id="cep"
                       class="form-control @error('cep') is-invalid @enderror"
                       placeholder="00000-000" value="{{ old('cep') }}" required>
                @error('cep')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Endereço (auto-preenchido) --}}
              <div class="mb-3">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" id="endereco"
                       class="form-control @error('endereco') is-invalid @enderror"
                       value="{{ old('endereco') }}" readonly required>
                @error('endereco')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              {{-- Cupom --}}
              <input type="hidden" name="cupom" value="{{ request('cupom') }}">

              <button
                type="submit"
                class="btn btn-lg btn-primary w-100"
                id="btnConfirmar"
                disabled
              >
                Confirmar e Pagar
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@push('scripts')
<script>
  const cepInput       = document.getElementById('cep');
  const enderecoInput  = document.getElementById('endereco');
  const btnConfirmar   = document.getElementById('btnConfirmar');

  function toggleButton() {
    btnConfirmar.disabled = ! enderecoInput.value.trim();
  }

  cepInput.addEventListener('blur', async function() {
    const cep = this.value.replace(/\D/g,'');
    enderecoInput.value = '';
    toggleButton();

    if (cep.length !== 8) return;
    try {
      const resp = await fetch(`/viacep/${cep}`);
      if (!resp.ok) throw new Error();
      const data = await resp.json();
      const addr = `${data.logradouro}, ${data.bairro}, ${data.localidade} – ${data.uf}`;
      enderecoInput.value = addr;
    } catch {
      enderecoInput.value = ''; 
      console.warn('Não foi possível buscar o CEP');
    } finally {
      toggleButton();
    }
  });

  
  document.addEventListener('DOMContentLoaded', toggleButton);
</script>
@endpush
