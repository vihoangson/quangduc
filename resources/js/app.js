import './bootstrap';

// Reply form toggle & helpers
(function() {
    function hideAllReplyForms(exceptId) {
        document.querySelectorAll('.reply-form').forEach(f => {
            if (!exceptId || f.id !== exceptId) {
                if (!f.classList.contains('d-none')) {
                    f.classList.add('d-none');
                }
            }
        });
    }

    function showReplyForm(form) {
        if (!form) return;
        hideAllReplyForms(form.id);
        form.classList.remove('d-none');
        // Focus first non-hidden input (prefer name, else message)
        const nameInput = form.querySelector('input[name="name"]');
        const messageArea = form.querySelector('textarea[name="message"]');
        (nameInput || messageArea)?.focus();
    }

    function autoResize(el) {
        el.style.height = 'auto';
        el.style.overflow = 'hidden';
        el.style.height = el.scrollHeight + 'px';
    }

    // Event delegation for clicks
    document.addEventListener('click', (e) => {
        const replyBtn = e.target.closest('.btn-reply');
        if (replyBtn) {
            const targetSel = replyBtn.getAttribute('data-target');
            if (targetSel) {
                const form = document.querySelector(targetSel);
                if (form) {
                    const isOpen = !form.classList.contains('d-none');
                    if (isOpen) {
                        form.classList.add('d-none');
                    } else {
                        showReplyForm(form);
                    }
                }
            }
            return; // stop further processing
        }
        const cancelBtn = e.target.closest('.btn-cancel-reply');
        if (cancelBtn) {
            const targetSel = cancelBtn.getAttribute('data-target');
            if (targetSel) {
                const form = document.querySelector(targetSel);
                if (form) form.classList.add('d-none');
            }
            return;
        }
    });

    // Auto-resize existing textareas on load
    function initAutoResize() {
        document.querySelectorAll('textarea.auto-resize').forEach(t => {
            autoResize(t);
        });
    }

    document.addEventListener('input', (e) => {
        const ta = e.target.closest('textarea.auto-resize');
        if (ta) autoResize(ta);
    });

    document.addEventListener('DOMContentLoaded', () => {
        initAutoResize();
        highlightHash();
    });

    // Highlight greeting if linked via hash
    function highlightHash() {
        if (location.hash && location.hash.startsWith('#greeting-')) {
            const el = document.querySelector(location.hash);
            if (el) {
                el.classList.add('greeting-highlight');
                setTimeout(() => el.classList.remove('greeting-highlight'), 3500);
            }
        }
    }

    window.addEventListener('hashchange', highlightHash);
})();

// Optional minimal style injection for highlight (only if not already present)
(function injectHighlightStyle(){
    if (document.getElementById('greeting-highlight-style')) return;
    const style = document.createElement('style');
    style.id = 'greeting-highlight-style';
    style.textContent = '.greeting-highlight { animation: greetingFlash 3s ease-in-out; outline: 2px solid #0d6efd; border-radius: 6px; }@keyframes greetingFlash {0%,100%{background:rgba(13,110,253,0.08);}50%{background:rgba(13,110,253,0.25);}}';
    document.head.appendChild(style);
})();
