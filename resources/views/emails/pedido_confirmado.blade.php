{{-- resources/views/emails/pedido_confirmado.blade.php --}}
<x-mail::message>

# Pedido #{{ $pedido->id }} Confirmado

Olá,

Seu pedido foi registrado com sucesso e está agora com **status:** {{ ucfirst($pedido->status) }}.

<x-mail::table>
| Produto       | Variação    | Qtd. | Unitário       | Total           |
| ------------- | ----------- | ---: | --------------:| ---------------:|
@foreach($pedido->itens as $item)
@php $prod = \App\Models\Produto::find($item['produto_id']); @endphp
| {{ $prod->nome }} | {{ $item['variacao'] ?? '—' }} | {{ $item['quantidade'] }} | R$ {{ number_format($item['preco_unitario'], 2, ',', '.') }} | R$ {{ number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') }} |
@endforeach
</x-mail::table>

<x-mail::panel>
**Subtotal:** R$ {{ number_format($pedido->subtotal, 2, ',', '.') }}  
**Frete:**    R$ {{ number_format($pedido->frete,    2, ',', '.') }}  
**Total:**    R$ {{ number_format($pedido->total,    2, ',', '.') }}
</x-mail::panel>

<x-mail::button :url="route('pedidos.show', $pedido)">
Ver detalhes do pedido
</x-mail::button>

Obrigado por comprar conosco!  
{{ config('app.name') }}

</x-mail::message>
