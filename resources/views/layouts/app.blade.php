<!doctype html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Lời Chúc' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f0f2f5; font-family: system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,'Noto Sans','Liberation Sans',sans-serif; }
        .navbar-brand { font-weight:600; }
        .comment-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#0d6efd,#6610f2); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:15px; }
        .comment-bubble { background:#fff; border-radius:18px; padding:10px 14px; box-shadow:0 1px 2px rgba(0,0,0,.05); position:relative; }
        .comment-bubble:before { content:''; position:absolute; left:-6px; top:14px; width:12px; height:12px; background:#fff; transform:rotate(45deg); border-left:1px solid #e5e7eb; border-top:1px solid #e5e7eb; }
        .comment-bubble { border:1px solid #e5e7eb; }
        .comment-actions button, .comment-actions a { font-size:12px; line-height:1; }
        .reply-form { margin-left:52px; }
        .comment-children { margin-left:52px; }
        .comment-meta time { font-size:12px; color:#6c757d; }
        textarea.auto-resize { overflow:hidden; resize:none; min-height:70px; }
        .ratio iframe { border:0; }
        .card-title { font-weight:600; }
        .form-text { font-size:12px; }
        a { text-decoration:none; }
        .badge { font-weight:500; }
        .pagination { --bs-pagination-border-radius: 8px; }
        .comment-bubble img, .comment-message img, .comment-upload-img {max-width:700px; width:100%; height:auto;}
        .nav-link.active { font-weight:600; }
    </style>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('greetings.index') }}">Lời Chúc</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNavbar" aria-controls="primaryNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="primaryNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('greetings.index') ? 'active' : '' }}" href="{{ route('greetings.index') }}">Trang Chủ</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button id="changeNameBtn" type="button" class="btn btn-outline-secondary btn-sm d-none">Đổi tên</button>
                @if (Route::has('login'))
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name ?? 'Tài khoản' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ url('/home') }}">Bảng điều khiển</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 m-0 text-danger text-decoration-none">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Đăng nhập</a>
                        @if (Route::has('register'))
                            <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Đăng ký</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>
<!-- Guest Name Modal (restored) -->
<div class="modal fade" id="guestNameModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tên hiển thị</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label small text-muted">Nhập tên (lưu trong trình duyệt).</label>
          <input id="guestNameInput" type="text" maxlength="80" class="form-control" required>
          <div class="invalid-feedback">Vui lòng nhập tên.</div>
        </div>
        <div class="d-flex justify-content-between">
          <button id="clearSavedNameBtn" type="button" class="btn btn-outline-danger btn-sm">Xóa tên đã lưu</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Lưu</button>
      </div>
    </form>
  </div>
</div>
<main class="container mb-5">
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
<script>
// Fallback guest name (only if main app.js logic not active)
(function(){
  if (window.__guestName || window.__guestNameFallbackInit) return; window.__guestNameFallbackInit = true;
  const KEY='guestName';
  const btn=document.getElementById('changeNameBtn');
  const modalEl=document.getElementById('guestNameModal');
  const inputEl=document.getElementById('guestNameInput');
  const clearBtn=document.getElementById('clearSavedNameBtn');
  let cached=localStorage.getItem(KEY)||'';
  function setBtnLabel(){ if(btn) btn.textContent = cached? 'Đổi tên':'Đặt tên'; }
  function apply(){document.querySelectorAll('input[name="name"]').forEach(i=>{if(!i.value||i.dataset.autofilled==='1'||i.value===cached){i.value=cached;i.dataset.autofilled='1';}});setBtnLabel();}
  function open(){ if(!modalEl) return; const inst=bootstrap.Modal.getOrCreateInstance(modalEl); if(inputEl){inputEl.value=cached; setTimeout(()=>inputEl.focus(),60);} inst.show(); }
  function save(v){cached=v.trim(); if(!cached) return false; localStorage.setItem(KEY,cached); apply(); return true; }
  function clear(){localStorage.removeItem(KEY); cached=''; document.querySelectorAll('input[name="name"]').forEach(i=>{ if(i.dataset.autofilled==='1') i.value='';}); apply(); }
  apply();
  btn?.addEventListener('click',()=>open());
  clearBtn?.addEventListener('click',()=>{clear(); open();});
  modalEl?.querySelector('form')?.addEventListener('submit',e=>{e.preventDefault(); const v=(inputEl?.value||'').trim(); if(!v){inputEl.classList.add('is-invalid'); return;} inputEl.classList.remove('is-invalid'); if(save(v)) bootstrap.Modal.getInstance(modalEl).hide();});
  document.addEventListener('focusin',e=>{ if(e.target.matches('input[name="name"]') && !cached){ e.target.blur(); open(); }});
})();
</script>
</body>
</html>
