@extends('layouts.app')
@section('title','Pedidos')

@section('content')
  <div class="d-flex justify-content-between mb-3">
    <h1>Pedidos</h1>
  </div>

  @if($pedidos->isEmpty())
    <p>Não há pedidos.</p>
  @else
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th>
          <th>Data</th>
          <th>Subtotal</th>
          <th>Frete</th>
          <th>Total</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pedidos as $p)
          <tr>
            <td>{{ $p->id }}</td>
            <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
            <td>R$ {{ number_format($p->subtotal,2,',','.') }}</td>
            <td>R$ {{ number_format($p->frete,2,',','.') }}</td>
            <td>R$ {{ number_format($p->total,2,',','.') }}</td>
            <td>
              @php
                // mapeia cada status para uma classe de badge “outline”
                $classes = [
                  'pendente' => 'border-warning text-warning',
                  'aprovado' => 'border-success text-success',
                  'cancelado'=> 'border-danger  text-danger',
                ];
                $class = $classes[$p->status] ?? 'border-secondary text-secondary';
              @endphp
              <span class="badge {{ $class }}">
                {{ ucfirst($p->status) }}
              </span>
            </td>
            <td>
              <a href="{{ route('pedidos.show', $p) }}" class="btn btn-sm btn-primary">
                Ver
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
@endsection
