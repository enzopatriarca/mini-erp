@extends('layouts.app')
@section('title', isset($produto) ? 'Editar Produto' : 'Novo Produto')

@section('content')
  <div class="card">
    <div class="card-header">
      {{ isset($produto) ? 'Editar' : 'Novo' }} Produto
    </div>
    <div class="card-body">
      @if($errors->produto->any())
        <div class="alert alert-danger">
          <strong>Ops! Corrija os erros abaixo:</strong>
          <ul class="mb-0">
            @foreach($errors->produto->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ isset($produto)
           ? route('produtos.update', $produto)
           : route('produtos.store') }}"
            method="POST">
        @csrf @if(isset($produto)) @method('PUT') @endif

        {{-- Nome --}}
        <div class="mb-3">
          <label for="nome" class="form-label">Nome</label>
          <input id="nome" type="text" name="nome"
                 class="form-control {{ $errors->produto->has('nome')?'is-invalid':'' }}"
                 value="{{ old('nome', $produto->nome ?? '') }}">
          @error('nome','produto')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Preço --}}
        <div class="mb-3">
          <label for="preco" class="form-label">Preço (R$)</label>
          <input id="preco" type="number" step="0.01" name="preco"
                 class="form-control {{ $errors->produto->has('preco')?'is-invalid':'' }}"
                 value="{{ old('preco', $produto->preco ?? '') }}">
          @error('preco','produto')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- Variações + Estoque --}}
        <div class="mb-4">
          <label class="form-label">Variações e Estoque</label>
          <table class="table" id="table-variacoes">
            <thead>
              <tr>
                <th>Variação <small class="text-muted">(opcional)</small></th>
                <th>Estoque</th>
                <th style="width:1%"></th>
              </tr>
            </thead>
            <tbody>
              @php
                $oldVars   = old('variacoes', $produto->variacoes ?? []);
                $oldStocks = old('estoque', isset($produto) ? $produto->estoques->pluck('quantidade')->toArray() : []);
              @endphp

              {{-- Se não houver nenhuma linha, já desenha uma vazia --}}
              @if(count($oldVars) === 0)
                @php
                  $oldVars[]   = '';
                  $oldStocks[] = old('estoque.0', 0);
                @endphp
              @endif

              @foreach($oldVars as $i => $v)
                <tr>
                  {{-- Variação (pode ficar vazia) --}}
                  <td>
                    <input type="text" name="variacoes[]"
                           class="form-control {{ $errors->produto->has("variacoes.$i")?'is-invalid':'' }}"
                           value="{{ $v }}">
                    @error("variacoes.$i",'produto')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </td>

                  {{-- Estoque sempre obrigatório --}}
                  <td>
                    <input type="number" name="estoque[]" min="0"
                           class="form-control {{ $errors->produto->has("estoque.$i")?'is-invalid':'' }}"
                           value="{{ $oldStocks[$i] ?? 0 }}">
                    @error("estoque.$i",'produto')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </td>

                  <td>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">×</button>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <button type="button" id="btn-add-variacao" class="btn btn-sm btn-outline-secondary">
            + Adicionar variação
          </button>
        </div>

        {{-- Botões --}}
        <div class="d-flex justify-content-between">
          <a href="{{ route('produtos.index') }}" class="btn btn-secondary">← Voltar</a>
          <button type="submit" class="btn btn-primary">
            {{ isset($produto) ? 'Atualizar Produto' : 'Cadastrar Produto' }}
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  
  document.getElementById('btn-add-variacao').onclick = () => {
    const tbody = document.querySelector('#table-variacoes tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input type="text" name="variacoes[]" class="form-control"></td>
      <td><input type="number" name="estoque[]" min="0" value="0" class="form-control"></td>
      <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-row">×</button></td>
    `;
    tbody.append(row);
  };

  
  document.querySelector('#table-variacoes tbody')
    .addEventListener('click', e => {
      if (e.target.matches('.btn-remove-row')) {
        e.target.closest('tr').remove();
      }
    });
</script>
@endpush
