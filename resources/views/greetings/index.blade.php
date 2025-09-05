@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        @if(isset($needMigration) && $needMigration)
            <div class="alert alert-warning">Chưa có bảng greetings. Chạy: <code>php artisan migrate</code>.</div>
        @endif
        @if(isset($loadError) && $loadError)
            <div class="alert alert-danger">{{ $errorMessage }}</div>
        @endif
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">Gửi Lời Chúc</h5>
                <form method="post" action="{{ route('greetings.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Tên</label>
                        <input type="text" name="name" class="form-control" maxlength="80" required value="{{ old('name') }}" placeholder="Tên của bạn">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lời chúc</label>
                        <textarea name="message" class="form-control auto-resize" rows="3" maxlength="4000" required placeholder="Viết lời chúc... (YouTube: dán link; Ảnh: !https://...jpg!)">{{ old('message') }}</textarea>
                        <div class="form-text">
                            YouTube: dán link (https://youtu.be/ID hoặc https://www.youtube.com/watch?v=ID).<br>
                            Ảnh: cú pháp <code>!https://domain/ten-anh.jpg!</code> (jpg, jpeg, png, gif, webp, svg).<br>
                            Có thể xuống dòng để tách đoạn.
                        </div>
                    </div>
                    <button class="btn btn-primary">Gửi</button>
                </form>
            </div>
        </div>
        <div class="mb-2 d-flex align-items-center gap-2">
            <h5 class="mb-0">Lời Chúc Mới Nhất</h5>
            @if(isset($greetings))<span class="badge text-bg-secondary">{{ $greetings->total() }}</span>@endif
        </div>
        <div id="greetings-list">
            @if(isset($greetings))
                @forelse($greetings as $greeting)
                    <x-greetings.item :greeting="$greeting" />
                @empty
                    <p class="text-muted fst-italic">Chưa có lời chúc nào.</p>
                @endforelse
            @endif
        </div>
        @if(isset($greetings))
        <div class="mt-4">
            {{ $greetings->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    function autoResize(el){ el.style.height='auto'; el.style.height=(el.scrollHeight)+'px'; }
    document.querySelectorAll('textarea.auto-resize').forEach(t=>{ t.addEventListener('input',()=>autoResize(t)); autoResize(t); });
    document.addEventListener('click', e=>{
        if(e.target.classList.contains('btn-reply')){ const sel=e.target.dataset.target; const f=document.querySelector(sel); if(f){ f.classList.toggle('d-none'); if(!f.classList.contains('d-none')){ const i=f.querySelector('input[name=name]'); i&&i.focus(); } } }
        if(e.target.classList.contains('btn-cancel-reply')){ const sel=e.target.dataset.target; const f=document.querySelector(sel); if(f){ f.classList.add('d-none'); } }
    });
    if(location.hash.startsWith('#greeting-')){ const el=document.querySelector(location.hash); if(el){ el.style.scrollMarginTop='80px'; el.scrollIntoView({behavior:'smooth'}); setTimeout(()=>{ el.classList.add('shadow-sm'); el.style.background='rgba(255,255,0,.15)'; },300); setTimeout(()=>{ el.style.background=''; el.classList.remove('shadow-sm'); },3000); } }
})();
</script>
@endpush
