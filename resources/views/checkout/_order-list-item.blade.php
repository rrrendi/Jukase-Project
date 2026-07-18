<a href="{{ route('checkout.success', $item->order_code) }}" class="li" style="text-decoration:none;color:inherit">
    <div class="meta">
        <div class="n mono">{{ $item->order_code }}</div>
        <div class="s">{{ $item->created_at?->format('d M Y, H:i') }}</div>
    </div>
    <div class="right" style="justify-content:center">
        @include('admin.orders._status', ['status' => $item->status])
    </div>
</a>