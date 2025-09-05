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
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('greetings.index') }}">Lời Chúc</a>
    </div>
</nav>
<main class="container mb-5">
    @yield('content')
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>

