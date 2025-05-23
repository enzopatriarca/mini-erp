@extends('layouts.app')
@section('title', isset($cupom) ? 'Editar Cupom' : 'Novo Cupom')

@section('content')
  <div class="card">
    <div class="card-header">{{ isset($cupom)?'Editar':'Novo' }} Cupom</div>
    <div class="card-body">
      <form action="{{ isset($cupom) 
           ? route('cupons.update', $cupom) 
           : route('cupons.store') }}"
            method="POST">
        @csrf
        @if(isset($cupom)) @method('PUT') @endif

        <div class="mb-3">
          <label class="form-label">Código</label>
          <input type="text" name="codigo" class="form-control"
                 value="{{ old('codigo', $cupom->codigo ?? '') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Desconto (R$)</label>
          <input type="number" step="0.01" name="desconto" class="form-control"
                 value="{{ old('desconto', $cupom->desconto ?? '') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Mínimo Subtotal (R$)</label>
          <input type="number" step="0.01" name="minimo_subtotal" class="form-control"
                 value="{{ old('minimo_subtotal', $cupom->minimo_subtotal ?? '') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Validade</label>
          <input type="date" name="validade" class="form-control"
                 value="{{ old('validade', isset($cupom)? $cupom->validade->toDateString(): '') }}">
        </div>

        <button class="btn btn-primary">{{ isset($cupom)? 'Atualizar':'Cadastrar' }}</button>
      </form>
    </div>
  </div>
@endsection
