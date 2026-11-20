/**
 * Admin helpers: SweetAlert2 confirm dialogs, flash toasts, copy-to-clipboard.
 * All destructive forms use `data-confirm*` attributes — no per-page JS needed.
 * Falls back to native confirm() if the Swal CDN fails to load.
 */
(function () {
    'use strict';

    function flashToast() {
        var flash = window.__flash || {};
        if (typeof Swal === 'undefined') return;
        var toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
        if (flash.success) toast.fire({ icon: 'success', title: flash.success });
        if (flash.error) toast.fire({ icon: 'error', title: flash.error });
    }

    function bindConfirms() {
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form || !form.matches || !form.matches('form[data-confirm]')) return;
            if (form.dataset.confirmed === '1') {
                delete form.dataset.confirmed;
                return; // already approved, let it submit
            }
            e.preventDefault();

            var title = form.getAttribute('data-confirm') || 'Yakin?';
            var text = form.getAttribute('data-confirm-text') || '';
            var confirmText = form.getAttribute('data-confirm-button') || 'Ya, lanjutkan';

            if (typeof Swal === 'undefined') {
                if (window.confirm(title + (text ? '\n' + text : ''))) form.submit();
                return;
            }

            Swal.fire({
                title: title,
                text: text,
                icon: form.getAttribute('data-confirm-icon') || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#193CB8',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.requestSubmit ? form.requestSubmit() : form.submit();
                }
            });
        });
    }

    function bindCopy() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest ? e.target.closest('[data-copy]') : null;
            if (!btn) return;
            var text = btn.getAttribute('data-copy') || '';
            if (!text) return;

            function done() {
                if (typeof Swal !== 'undefined') {
                    Swal.mixin({
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 2000,
                    }).fire({ icon: 'success', title: 'Disalin ke clipboard' });
                }
            }

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(done).catch(function () {
                    fallbackCopy(text); done();
                });
            } else {
                fallbackCopy(text); done();
            }
        });

        function fallbackCopy(text) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy'); } catch (err) { /* ignore */ }
            document.body.removeChild(ta);
        }
    }

    function bindBulk() {
        var bar = document.querySelector('[data-bulk-bar]');
        if (!bar) return;
        var form = document.querySelector(bar.getAttribute('data-bulk-form') || '#bulkForm');
        var countEl = bar.querySelector('[data-bulk-count]');
        var checkAll = document.getElementById('bulkCheckAll');

        function boxes() {
            return Array.prototype.slice.call(document.querySelectorAll('.js-bulk-check'));
        }
        function checked() {
            return boxes().filter(function (b) { return b.checked; });
        }
        function refresh() {
            var n = checked().length;
            var total = boxes().length;
            if (countEl) countEl.textContent = n;
            bar.classList.toggle('hidden', n === 0);
            boxes().forEach(function (b) {
                var tr = b.closest('tr');
                if (tr) tr.classList.toggle('bg-[#193CB8]/[0.04]', b.checked);
            });
            if (checkAll) {
                checkAll.checked = total > 0 && n === total;
                checkAll.indeterminate = n > 0 && n < total;
            }
        }

        document.addEventListener('change', function (e) {
            if (e.target === checkAll) {
                boxes().forEach(function (b) { b.checked = checkAll.checked; });
                refresh();
            } else if (e.target.classList && e.target.classList.contains('js-bulk-check')) {
                refresh();
            }
        });

        bar.addEventListener('click', function (e) {
            var clear = e.target.closest('[data-bulk-clear]');
            if (clear) {
                boxes().forEach(function (b) { b.checked = false; });
                refresh();
                return;
            }
            var exp = e.target.closest('[data-bulk-export]');
            if (exp) {
                var ids = checked().map(function (b) { return b.value; });
                if (!ids.length) return;
                var url = exp.getAttribute('data-export-url');
                window.location.href = url + '?' + ids.map(function (id) {
                    return 'ids[]=' + encodeURIComponent(id);
                }).join('&');
                return;
            }
            var go = e.target.closest('[data-bulk-go]');
            if (!go || !form) return;
            var ids = checked().map(function (b) { return b.value; });
            if (!ids.length) return;
            form.querySelector('input[name="action"]').value = go.getAttribute('data-bulk-go');
            var holder = form.querySelector('[data-bulk-ids]');
            holder.innerHTML = '';
            ids.forEach(function (id) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                holder.appendChild(input);
            });

            var title = (go.getAttribute('data-confirm-title') || '').replace('{n}', ids.length);
            var text = (go.getAttribute('data-confirm-text') || '').replace('{n}', ids.length);
            if (!title) {
                form.submit();
                return;
            }
            if (typeof Swal === 'undefined') {
                if (window.confirm(title + (text ? '\n' + text : ''))) form.submit();
                return;
            }
            Swal.fire({
                title: title,
                text: text,
                icon: go.getAttribute('data-confirm-icon') || 'warning',
                showCancelButton: true,
                confirmButtonColor: '#193CB8',
                cancelButtonColor: '#9ca3af',
                confirmButtonText: go.getAttribute('data-confirm-button') || 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });

        refresh();
    }

    function bindFilterPanels() {
        document.querySelectorAll('[data-filter-panel]').forEach(function (panel) {
            var toggle = panel.querySelector('[data-filter-toggle]');
            var body = panel.querySelector('[data-filter-body]');
            var chevron = panel.querySelector('[data-filter-chevron]');
            if (!toggle || !body) return;

            var key = 'fp:' + (panel.getAttribute('data-filter-key') || 'default');
            var hasActive = panel.getAttribute('data-filter-active') === '1';

            function setOpen(open) {
                body.classList.toggle('grid-rows-[0fr]', !open);
                body.classList.toggle('opacity-0', !open);
                body.classList.toggle('grid-rows-[1fr]', open);
                body.classList.toggle('opacity-100', open);
                if (chevron) chevron.classList.toggle('rotate-180', open);
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                try { localStorage.setItem(key, open ? '1' : '0'); } catch (err) { /* ignore */ }
            }

            var stored = null;
            try { stored = localStorage.getItem(key); } catch (err) { /* ignore */ }
            setOpen(hasActive ? true : (stored === null ? true : stored === '1'));

            toggle.addEventListener('click', function () {
                setOpen(toggle.getAttribute('aria-expanded') !== 'true');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        flashToast();
        bindConfirms();
        bindCopy();
        bindBulk();
        bindFilterPanels();
    });
})();
