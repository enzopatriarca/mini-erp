@extends('layouts.app')

@section('title','Produtos')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Produtos</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#produtoModal">Novo Produto</button>
  </div>

  <table class="table table-striped">
    <thead>
      <tr><th>Nome</th><th>Preço</th><th>Estoque</th><th>Ações</th></tr>
    </thead>
    <tbody>
        @foreach($produtos as $p)
        <tr>
            <td>{{ $p->nome }}</td>
            <td>R$ {{ number_format($p->preco,2,',','.') }}</td>
            <td>{{ $p->estoques->sum('quantidade') }}</td>
            <td>
            <button
                class="btn btn-sm btn-secondary"
                data-produto='@json($p)'
                onclick="editProduto(JSON.parse(this.dataset.produto))"
                data-bs-toggle="modal"
                data-bs-target="#produtoModal"
                >
                Editar
            </button>
            </td>
        </tr>
        @endforeach
    </tbody>
  </table>

  {{ $produtos->links() }}

  @include('produtos.form')
@endsection

@push('scripts')
<script>
    function editProduto(produto) {
    // produto já é um objeto JS
    document.querySelector('#produtoForm').action = `/produtos/${produto.id}`;
    document.querySelector('#nome').value   = produto.nome;
    document.querySelector('#preco').value  = produto.preco;
    // …
    }
</script>
@endpush
