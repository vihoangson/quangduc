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
        // Reset aria-expanded on all reply buttons when hiding others
        document.querySelectorAll('.btn-reply[aria-expanded="true"]').forEach(btn=>btn.setAttribute('aria-expanded','false'));
    }

    function showReplyForm(form) {
        if (!form) return;
        hideAllReplyForms(form.id);
        form.classList.remove('d-none');
        // Set aria-expanded for the triggering button
        const btn = document.querySelector('.btn-reply[data-target="#'+form.id+'"], .btn-reply[data-reply-form="#'+form.id+'"]');
        if (btn) btn.setAttribute('aria-expanded','true');
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
            const targetSel = replyBtn.getAttribute('data-target') || replyBtn.getAttribute('data-reply-form');
            if (targetSel) {
                const form = document.querySelector(targetSel);
                if (form) {
                    const isOpen = !form.classList.contains('d-none');
                    if (isOpen) {
                        form.classList.add('d-none');
                        replyBtn.setAttribute('aria-expanded','false');
                    } else {
                        showReplyForm(form);
                        replyBtn.setAttribute('aria-expanded','true');
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
                if (form) {
                    form.classList.add('d-none');
                    // Reset aria-expanded on its button
                    const btn = document.querySelector('.btn-reply[data-target="'+targetSel+'"], .btn-reply[data-reply-form="'+targetSel+'"]');
                    if (btn) btn.setAttribute('aria-expanded','false');
                }
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
        initGuestName();
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

    // Guest name storage logic
    function initGuestName(){
        const KEY='guestName';
        let cached = localStorage.getItem(KEY) || '';
        const modalEl = document.getElementById('guestNameModal');
        const inputEl = modalEl ? modalEl.querySelector('#guestNameInput') : null;
        const changeBtn = document.getElementById('changeNameBtn');
        const clearBtn = document.getElementById('clearSavedNameBtn');

        function applyName(name){
            document.querySelectorAll('input[name="name"]').forEach(inp => {
                if (!inp.value || inp.dataset.autofilled === '1' || inp.value === cached) {
                    inp.value = name;
                    inp.dataset.autofilled = '1';
                }
            });
            if (changeBtn){
                changeBtn.classList.remove('d-none');
                changeBtn.textContent = name ? 'Đổi tên' : 'Đặt tên';
            }
        }

        function openModal(){
            if (!modalEl) return;
            const inst = window.bootstrap?.Modal.getOrCreateInstance(modalEl);
            if (inputEl) {
                inputEl.value = cached || '';
                setTimeout(()=>inputEl.focus(),90);
            }
            inst?.show();
        }

        function saveName(name){
            cached = name.trim();
            if (!cached) return false;
            localStorage.setItem(KEY, cached);
            applyName(cached);
            return true;
        }

        if (cached) applyName(cached); else applyName('');

        // Focus interception
        document.addEventListener('focusin', (e)=>{
            if (e.target.matches('input[name="name"]')) {
                if (!cached) {
                    e.target.blur();
                    openModal();
                }
            }
        });

        // Modal form submit
        modalEl?.querySelector('form')?.addEventListener('submit',(e)=>{
            e.preventDefault();
            const val = inputEl?.value.trim() || '';
            if (val.length === 0) {
                inputEl?.classList.add('is-invalid');
                return;
            }
            inputEl?.classList.remove('is-invalid');
            if (saveName(val)) {
                window.bootstrap?.Modal.getInstance(modalEl)?.hide();
            }
        });

        changeBtn?.addEventListener('click',(e)=>{
            e.preventDefault();
            openModal();
        });

        clearBtn?.addEventListener('click',(e)=>{
            e.preventDefault();
            localStorage.removeItem(KEY);
            cached='';
            document.querySelectorAll('input[name="name"]').forEach(inp=>{
                if (inp.dataset.autofilled==='1') { inp.value=''; }
            });
            applyName('');
            openModal();
        });
        // Expose for debugging
        window.__guestName = { set: saveName, get: ()=>cached, open: openModal };
    }
})();

// Optional minimal style injection for highlight (only if not already present)
(function injectHighlightStyle(){
    if (document.getElementById('greeting-highlight-style')) return;
    const style = document.createElement('style');
    style.id = 'greeting-highlight-style';
    style.textContent = '.greeting-highlight { animation: greetingFlash 3s ease-in-out; outline: 2px solid #0d6efd; border-radius: 6px; }@keyframes greetingFlash {0%,100%{background:rgba(13,110,253,0.08);}50%{background:rgba(13,110,253,0.25);}}';
    document.head.appendChild(style);
})();
