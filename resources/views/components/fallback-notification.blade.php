@if(!empty($fallbackNotifications))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <h6 class="alert-heading">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Notifikasi Warisan Harga
    </h6>
    <p class="mb-2">Beberapa harga obat menggunakan harga dari bulan sebelumnya:</p>
    <ul class="mb-0">
        @foreach($fallbackNotifications as $notification)
        <li>
            <strong>{{ $notification['nama_obat'] }}</strong> -
            Harga dari periode <strong>{{ $notification['source_periode'] }}</strong>
            digunakan untuk periode <strong>{{ $notification['target_periode'] }}</strong>
            @if($notification['fallback_depth'] > 1)
            <span class="badge bg-info ms-1">{{ $notification['fallback_depth'] }} bulan mundur</span>
            @endif
        </li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
