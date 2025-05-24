{{-- resources/views/cupons/index.blade.php --}}
@extends('layouts.app')
@section('title','Cupons')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Cupons</h1>
    <a href="{{ route('cupons.create') }}" class="btn btn-success">+ Novo Cupom</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <table class="table table-striped mb-0">
        <thead class="table-light">
          <tr>
            <th>Código</th>
            <th>Desconto (R$)</th>
            <th>Mínimo Subtotal (R$)</th>
            <th>Validade</th>
            <th class="text-end">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cupons as $cupom)
            <tr>
              <td>{{ $cupom->codigo }}</td>
              <td>{{ number_format($cupom->desconto,2,',','.') }}</td>
              <td>{{ number_format($cupom->minimo_subtotal,2,',','.') }}</td>
              <td>{{ $cupom->validade->format('d/m/Y') }}</td>
              <td class="text-end">
                <a href="{{ route('cupons.edit', $cupom) }}"
                   class="btn btn-sm btn-warning me-1">
                  Editar
                </a>
                <button
                  type="button"
                  class="btn btn-sm btn-danger btn-delete"
                  data-bs-toggle="modal"
                  data-bs-target="#deleteModal"
                  data-url="{{ route('cupons.destroy', $cupom) }}"
                >
                  Excluir
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-3">Nenhum cupom cadastrado.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('modals')
  {{-- Modal de confirmação de exclusão de cupom --}}
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Excluir Cupom</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Tem certeza que deseja remover este cupom?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>
          <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Excluir</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endpush

@push('scripts')
<script>
  // ajusta a action do form de exclusão no modal
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('deleteForm').action = btn.dataset.url;
    });
  });
</script>
@endpush
