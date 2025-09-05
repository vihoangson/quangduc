@props(['greeting','depth'=>0])
@php($children = $greeting->children)
<div class="mt-3" id="greeting-{{ $greeting->id }}">
    <div class="d-flex gap-2">
        <div class="comment-avatar flex-shrink-0">{{ $greeting->initial }}</div>
        <div class="flex-grow-1">
            <div class="comment-bubble">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <strong class="me-auto">{{ $greeting->name }}</strong>
                    <span class="comment-meta"><time datetime="{{ $greeting->created_at }}" title="{{ $greeting->created_at }}">{{ $greeting->created_at->diffForHumans() }}</time></span>
                </div>
                @if($greeting->image_url)
                    <div class="mb-2">
                        <img src="{{ $greeting->image_url }}" alt="uploaded image" class="img-fluid rounded border" loading="lazy">
                    </div>
                @endif
                <div class="comment-message small">{!! $greeting->rendered_message !!}</div>
                <div class="comment-actions mt-2 d-flex gap-2">
                    <button class="btn btn-sm btn-outline-light text-primary fw-semibold btn-reply" data-target="#reply-form-{{ $greeting->id }}" type="button">Trả lời</button>
                    <a class="btn btn-sm btn-outline-light" href="#greeting-{{ $greeting->id }}">Liên kết</a>
                </div>
            </div>
            <form id="reply-form-{{ $greeting->id }}" class="reply-form d-none mt-2" method="post" action="{{ route('greetings.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $greeting->id }}">
                <div class="mb-2">
                    <input required maxlength="80" name="name" placeholder="Tên của bạn" class="form-control form-control-sm" />
                </div>
                <div class="mb-2 d-none">
                    <input type="file" name="image" accept="image/*" class="form-control form-control-sm" />
                </div>
                <div class="mb-2">
                    <textarea required maxlength="4000" name="message" placeholder="Lời chúc... (YouTube link / !https://...jpg!)" class="form-control form-control-sm auto-resize"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm">Gửi</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm btn-cancel-reply" data-target="#reply-form-{{ $greeting->id }}">Hủy</button>
                </div>
            </form>
            @if($children->count())
                <div class="comment-children">
                    @foreach($children as $child)
                        <x-greetings.item :greeting="$child" :depth="$depth+1" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
