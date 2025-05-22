<div class="modal fade" id="produtoModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="produtoForm" method="POST" action="{{ route('produtos.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Produto</h5></div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
          </div>
          <div class="mb-3">
            <label for="preco" class="form-label">Preço</label>
            <input type="number" step="0.01" class="form-control" id="preco" name="preco" required>
          </div>
          {{-- Variações dinâmicas e estoques aqui --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </div>
    </form>
  </div>
</div>
