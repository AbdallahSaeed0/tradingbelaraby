/*
 * Admin tables: row action buttons look the same everywhere.
 * Up to 4 actions are shown as icon buttons. With more, the first 3 stay visible and the
 * rest move into a "more" (⋮) menu. The original elements are moved, not copied, so their
 * links, onclick handlers and forms (delete, etc.) keep working.
 */
(function () {
    'use strict';

    var MAX_VISIBLE = 4; // show all when there are this many or fewer
    var KEEP = 3;        // otherwise keep this many and fold the rest

    function isActionItem(el) {
        if (!el || el.nodeType !== 1) return false;
        if (el.matches('a.btn, button.btn')) return true;
        if (el.matches('form') && el.querySelector('.btn, button[type="submit"]')) return true;
        if (el.matches('.dropdown, .btn-group') && el.querySelector('[data-bs-toggle="dropdown"]')) return true;
        return false;
    }

    // The element holding the row's buttons: the cell itself or its single wrapper div
    function findContainer(cell) {
        var el = cell;
        for (var depth = 0; depth < 3; depth++) {
            var kids = Array.prototype.filter.call(el.children, function (c) {
                return c.tagName !== 'SCRIPT' && c.tagName !== 'INPUT' && c.tagName !== 'TEMPLATE';
            });
            if (kids.length === 1 && !isActionItem(kids[0]) && kids[0].tagName === 'DIV') {
                el = kids[0];
                continue;
            }
            break;
        }
        return el;
    }

    function labelOf(el) {
        var btn = el.matches('form') ? el.querySelector('.btn, button[type="submit"]') : el;
        var label = (btn && (btn.getAttribute('title') || btn.getAttribute('aria-label') || btn.getAttribute('data-bs-original-title'))) || '';
        if (!label && btn) label = btn.textContent.trim();
        return label.trim();
    }

    function isDanger(el) {
        var btn = el.matches('form') ? el.querySelector('.btn, button') : el;
        return !!(btn && /\bbtn-(outline-)?danger\b/.test(btn.className));
    }

    // Solid buttons (btn-primary, btn-danger...) become the outline style used across the admin tables
    function outlineStyle(btn) {
        if (!btn.classList.contains('btn')) btn.classList.add('btn');
        btn.classList.add('btn-sm');
        btn.classList.remove('btn-lg', 'btn-link');
        ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'].forEach(function (v) {
            if (btn.classList.contains('btn-' + v)) {
                btn.classList.remove('btn-' + v);
                btn.classList.add('btn-outline-' + v);
            }
        });
        var hasVariant = Array.prototype.some.call(btn.classList, function (c) { return /^btn-outline-/.test(c); });
        if (!hasVariant) btn.classList.add('btn-outline-secondary');
    }

    function toMenuItem(btn, label, danger) {
        btn.className = btn.className
            .split(/\s+/)
            .filter(function (c) { return c && !/^btn(-|$)/.test(c) && c !== 'me-1' && c !== 'ms-1' && c !== 'ms-2'; })
            .concat(['dropdown-item'], danger ? ['text-danger'] : [])
            .join(' ');
        btn.removeAttribute('style');
        btn.removeAttribute('data-bs-toggle');
        var icon = btn.querySelector('i');
        if (icon) icon.classList.add('me-2', 'fa-fw');
        var hasText = Array.prototype.some.call(btn.childNodes, function (n) {
            return n.nodeType === 3 && n.textContent.trim();
        });
        if (!hasText && label) btn.appendChild(document.createTextNode(label));
    }

    function enhance(cell) {
        if (cell.dataset.rowActionsDone) return;
        var container = findContainer(cell);
        var items = Array.prototype.filter.call(container.children, isActionItem);
        if (items.length < 2) return;
        cell.dataset.rowActionsDone = '1';

        // Same square icon buttons everywhere, but only when the buttons are icons (no text labels)
        var iconOnly = items.every(function (it) {
            var b = it.matches('form') ? it.querySelector('.btn, button') : (it.matches('.dropdown, .btn-group') ? it.querySelector('[data-bs-toggle="dropdown"]') : it);
            return b && b.textContent.trim() === '';
        });
        if (iconOnly) {
            container.classList.add('table-icon-actions');
            container.classList.remove('btn-group', 'btn-group-sm'); // separate buttons, not a joined group
            container.removeAttribute('role');
            cell.classList.add('table-icon-actions-cell');
            items.forEach(function (it) {
                var b = it.matches('form') ? it.querySelector('.btn, button') : (it.matches('.dropdown, .btn-group') ? it.querySelector('[data-bs-toggle="dropdown"]') : it);
                if (b) outlineStyle(b);
            });
        }

        if (items.length <= MAX_VISIBLE) return;

        var existingMenu = null;
        items.forEach(function (it) {
            if (!existingMenu && it.matches('.dropdown, .btn-group')) existingMenu = it;
        });

        var visible = items.filter(function (it) { return it !== existingMenu; }).slice(0, KEEP);
        var folded = items.filter(function (it) { return visible.indexOf(it) === -1 && it !== existingMenu; });

        var wrapper = existingMenu;
        var menu;
        if (wrapper) {
            menu = wrapper.querySelector('.dropdown-menu');
        } else {
            wrapper = document.createElement('div');
            wrapper.className = 'dropdown';
            wrapper.innerHTML =
                '<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="More actions" title="More actions">' +
                '<i class="fa fa-ellipsis-v"></i></button>' +
                '<ul class="dropdown-menu dropdown-menu-end"></ul>';
            menu = wrapper.querySelector('.dropdown-menu');
        }
        if (!menu) return;

        var dangerItems = [];
        folded.forEach(function (it) {
            var label = labelOf(it);
            var danger = isDanger(it);
            var li = document.createElement('li');
            if (it.matches('form')) {
                it.classList.remove('d-inline', 'd-inline-block');
                var b = it.querySelector('.btn, button[type="submit"]');
                if (b) toMenuItem(b, label, danger);
            } else {
                toMenuItem(it, label, danger);
            }
            li.appendChild(it);
            if (danger) {
                dangerItems.push(li); // delete-type actions go last, after a divider
            } else {
                var divider = menu.querySelector('.dropdown-divider');
                menu.insertBefore(li, divider ? divider.closest('li') : null);
            }
        });
        if (dangerItems.length) {
            if (!menu.querySelector('.dropdown-divider')) {
                var hr = document.createElement('li');
                hr.innerHTML = '<hr class="dropdown-divider">';
                menu.appendChild(hr);
            }
            dangerItems.forEach(function (li) { menu.appendChild(li); });
        }

        // Order: visible buttons, then the menu at the end
        container.appendChild(wrapper);
        visible.forEach(function (it) { container.insertBefore(it, wrapper); });
        // The ⋮ button opens through Bootstrap's data-bs-toggle, like the courses table menu
    }

    function run(root) {
        (root || document).querySelectorAll('table tbody tr > td:last-child').forEach(function (cell) {
            try { enhance(cell); } catch (e) { /* leave the row as it was */ }
        });
    }

    function start() {
        run(document);
        // Tables refreshed by AJAX filters/search
        var pending = null;
        new MutationObserver(function () {
            if (pending) return;
            pending = setTimeout(function () { pending = null; run(document); }, 50);
        }).observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
