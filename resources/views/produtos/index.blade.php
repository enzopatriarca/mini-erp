@extends('layouts.app')
@section('title','Produtos')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Produtos</h1>
    <a href="{{ route('produtos.create') }}" class="btn btn-success">+ Novo</a>
  </div>

  {{-- Alerta quando não encontrar nada --}}
  @if(request('search') && $produtos->isEmpty())
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
      <div>
        Nenhum produto encontrado para “<strong>{{ request('search') }}</strong>”.
        <a href="{{ route('produtos.index') }}" class="alert-link">Ver todos</a>
      </div>
      <a href="{{ route('produtos.create') }}" class="btn btn-sm btn-primary">
        Cadastrar Produto
      </a>
    </div>
  @endif

  {{-- Tabela sempre mostrada; corpo usa @forelse para vazio --}}
  <table class="table table-bordered align-middle">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Preço</th>
        <th style="min-width:200px">Variações</th>
        <th>Estoque</th>
        <th>Ações</th>
        <th>Comprar</th>
      </tr>
    </thead>
    <tbody>
      @forelse($produtos as $p)
        @php
          $totalStock = $p->estoques->sum('quantidade');
          $hasManyVars = count($p->variacoes) > 1;
        @endphp
        <tr>
          <td>{{ $p->nome }}</td>
          <td>R$ {{ number_format($p->preco,2,',','.') }}</td>
          <td>
            @if($p->variacoes)
              <div class="d-flex flex-wrap" style="gap:0.25rem;">
                @foreach($p->variacoes as $var)
                  @php
                    $q = $p->estoques->firstWhere('variacao',$var)->quantidade ?? 0;
                  @endphp
                  <span
                    class="badge bg-secondary text-truncate"
                    style="max-width:100px"
                    title="{{ $var }} ({{ $q }} em estoque)"
                  >
                    {{ $var }} ({{ $q }})
                  </span>
                @endforeach
              </div>
            @else
              <span class="text-muted">— nenhuma —</span>
            @endif
          </td>
          <td>{{ $totalStock }}</td>
          <td>
            <a href="{{ route('produtos.edit',$p) }}" class="btn btn-sm btn-warning me-1">Editar</a>
            <button
              type="button"
              class="btn btn-sm btn-danger btn-delete"
              data-bs-toggle="modal"
              data-bs-target="#deleteModal"
              data-url="{{ route('produtos.destroy',$p) }}"
            >Excluir</button>
          </td>
          <td>
            <form action="{{ route('carrinho.adicionar') }}" method="POST" class="d-flex align-items-center">
              @csrf
              <input type="hidden" name="produto_id" value="{{ $p->id }}">

              @if($hasManyVars)
                <select name="variacao" class="form-select form-select-sm me-1" style="width:120px" {{ $totalStock===0?'disabled':'' }}>
                  @foreach($p->variacoes as $var)
                    @php $q = $p->estoques->firstWhere('variacao',$var)->quantidade ?? 0; @endphp
                    <option value="{{ $var }}">
                      {{ $var }} ({{ $q }})
                    </option>
                  @endforeach
                </select>
              @else
                <input type="hidden" name="variacao" value="{{ $p->variacoes[0] ?? '' }}">
              @endif

              <input
                type="number"
                name="quantidade"
                value="1"
                min="1"
                class="form-control form-control-sm me-1"
                style="width:60px"
                {{ $totalStock===0?'disabled':'' }}
              >

              <button
                class="btn btn-sm {{ $totalStock===0?'btn-secondary':'btn-primary' }}"
                {{ $totalStock===0?'disabled title="Sem estoque"':'' }}
              >{{ $totalStock===0 ? '✖' : 'Comprar' }}</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            Nenhum produto para exibir.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Paginação só se houver mais de uma página --}}
  @if($produtos->hasPages())
    <div class="d-flex justify-content-center">
      {{ $produtos->links() }}
    </div>
  @endif
@endsection

@push('modals')
  {{-- Modal de confirmação de exclusão --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Excluir Produto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja excluir este produto?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <form method="POST" id="deleteForm">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger">Excluir</button>
        </form>
      </div>
    </div></div>
  </div>
@endpush

@push('scripts')
<script>
  // Ajusta a action do form de exclusão
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('deleteForm').action = btn.dataset.url;
    });
  });

  // Toast de sucesso ao adicionar ao carrinho
  @if(session('success'))
    const toastHtml = `
      <div class="toast align-items-center text-bg-success border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body">{{ session('success') }}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>`;
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.innerHTML = toastHtml;
    document.body.append(container);
    new bootstrap.Toast(container.querySelector('.toast')).show();
  @endif
</script>
@endpush
