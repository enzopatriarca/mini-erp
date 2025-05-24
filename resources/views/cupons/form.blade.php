@extends('layouts.app')
@section('title', isset($cupom) ? 'Editar Cupom' : 'Novo Cupom')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ isset($cupom) ? 'Editar Cupom' : 'Novo Cupom' }}</h1>
    <a href="{{ route('cupons.index') }}" class="btn btn-secondary">← Voltar</a>
  </div>

  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $msg)
          <li>{{ $msg }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form
    action="{{ isset($cupom)
        // passe só o ID, não o model inteiro
        ? route('cupons.update',   ['cupom' => $cupom->id])
        : route('cupons.store') }}"
    method="POST"
  >
    @csrf
  @isset($cupom)
    {{-- especifique o método correto (PATCH ou PUT) --}}
    @method('PATCH')
  @endisset

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="codigo" class="form-label">Código</label>
        <input
          id="codigo"
          name="codigo"
          type="text"
          class="form-control @error('codigo') is-invalid @enderror"
          value="{{ old('codigo', $cupom->codigo ?? '') }}"
          required
        >
        @error('codigo')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6 mb-3">
        <label for="validade" class="form-label">Validade</label>
        <input
          id="validade"
          name="validade"
          type="date"
          class="form-control @error('validade') is-invalid @enderror"
          value="{{ old('validade',
                isset($cupom) ? $cupom->validade->toDateString() : ''
          ) }}"
          required
        >
        @error('validade')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="row">
      <div class="col-md-6 mb-3">
        <label for="desconto" class="form-label">Desconto (R$)</label>
        <input
          id="desconto"
          name="desconto"
          type="number"
          step="0.01"
          class="form-control @error('desconto') is-invalid @enderror"
          value="{{ old('desconto', $cupom->desconto ?? '') }}"
          required
        >
        @error('desconto')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="col-md-6 mb-3">
        <label for="minimo_subtotal" class="form-label">Mínimo Subtotal (R$)</label>
        <input
          id="minimo_subtotal"
          name="minimo_subtotal"
          type="number"
          step="0.01"
          class="form-control @error('minimo_subtotal') is-invalid @enderror"
          value="{{ old('minimo_subtotal', $cupom->minimo_subtotal ?? '') }}"
          required
        >
        @error('minimo_subtotal')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">
      {{ isset($cupom) ? 'Atualizar Cupom' : 'Cadastrar Cupom' }}
    </button>
  </form>
@endsection
