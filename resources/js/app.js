function initMobileNav() {
    const toggle = document.querySelector('[data-mobile-nav-toggle]');
    const panel = document.querySelector('[data-mobile-nav-panel]');

    if (!toggle || !panel) {
        return;
    }

    const homeToggle = panel.querySelector('[data-mobile-home-toggle]');
    const homePanel = panel.querySelector('[data-mobile-home-panel]');

    const setExpanded = (expanded) => {
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        panel.hidden = !expanded;
        if (!expanded) {
            if (homeToggle instanceof HTMLElement) {
                homeToggle.setAttribute('aria-expanded', 'false');
            }
            if (homePanel instanceof HTMLElement) {
                homePanel.classList.add('hidden');
            }
        }
    };

    const setHomeExpanded = (expanded) => {
        if (!(homeToggle instanceof HTMLElement) || !(homePanel instanceof HTMLElement)) return;
        homeToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        if (expanded) {
            homePanel.classList.remove('hidden');
        } else {
            homePanel.classList.add('hidden');
        }
    };

    setExpanded(false);
    setHomeExpanded(false);

    toggle.addEventListener('click', () => {
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        setExpanded(!expanded);
    });

    if (homeToggle instanceof HTMLElement && homePanel instanceof HTMLElement) {
        homeToggle.addEventListener('click', () => {
            const expanded = homeToggle.getAttribute('aria-expanded') === 'true';
            setHomeExpanded(!expanded);
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        setExpanded(false);
    });
}

function initHomeMenuDropdown() {
    const root = document.querySelector('[data-home-menu]');
    if (!(root instanceof HTMLElement)) return;

    const trigger = root.querySelector('[data-home-menu-trigger]');
    const panel = root.querySelector('[data-home-menu-panel]');
    if (!(trigger instanceof HTMLElement) || !(panel instanceof HTMLElement)) return;

    let open = false;
    let closeTimer = null;

    const setOpen = (nextOpen) => {
        open = !!nextOpen;
        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            panel.classList.remove('hidden');
        } else {
            panel.classList.add('hidden');
        }
    };

    setOpen(false);

    const cancelClose = () => {
        if (closeTimer) window.clearTimeout(closeTimer);
        closeTimer = null;
    };

    const scheduleClose = () => {
        cancelClose();
        closeTimer = window.setTimeout(() => setOpen(false), 120);
    };

    const onMouseEnter = () => {
        cancelClose();
        setOpen(true);
    };
    const onMouseLeave = () => scheduleClose();
    const onFocusIn = () => setOpen(true);
    const onFocusOut = () => {
        window.setTimeout(() => {
            if (!root.contains(document.activeElement)) {
                setOpen(false);
            }
        }, 0);
    };

    root.addEventListener('mouseenter', onMouseEnter);
    root.addEventListener('mouseleave', onMouseLeave);
    root.addEventListener('focusin', onFocusIn);
    root.addEventListener('focusout', onFocusOut);

    panel.addEventListener('mouseenter', () => {
        cancelClose();
        setOpen(true);
    });
    panel.addEventListener('mouseleave', () => scheduleClose());

    trigger.addEventListener('click', (event) => {
        event.preventDefault();
        setOpen(!open);
    });

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Node)) return;
        if (root.contains(target)) return;
        setOpen(false);
    });

    root.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        cancelClose();
        setOpen(false);
        if (trigger instanceof HTMLElement) {
            trigger.focus();
        }
    });
}

function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    return token || '';
}

function initFormLoading() {
    const forms = document.querySelectorAll('form[data-loading-message]');

    if (forms.length === 0) {
        return;
    }

    let overlay = null;
    let messageEl = null;

    const ensureOverlay = () => {
        if (overlay) return;

        overlay = document.createElement('div');
        overlay.className = 'form-loading-overlay';
        overlay.setAttribute('role', 'alert');
        overlay.setAttribute('aria-live', 'assertive');

        const panel = document.createElement('div');
        panel.className = 'form-loading-panel';

        const row = document.createElement('div');
        row.style.display = 'flex';
        row.style.alignItems = 'center';
        row.style.gap = '0.75rem';

        const spinner = document.createElement('div');
        spinner.className = 'form-loading-spinner';

        messageEl = document.createElement('div');
        messageEl.style.fontWeight = '700';
        messageEl.style.color = 'rgb(15 23 42)';

        row.appendChild(spinner);
        row.appendChild(messageEl);
        panel.appendChild(row);
        overlay.appendChild(panel);
    };

    const setBusy = (busy, message) => {
        const elements = document.querySelectorAll('input, select, textarea, button');
        elements.forEach((el) => {
            if (el instanceof HTMLInputElement && el.type === 'hidden') {
                return;
            }

            if (busy) {
                el.setAttribute('data-was-disabled', el.disabled ? '1' : '0');
                el.disabled = true;
            } else {
                const wasDisabled = el.getAttribute('data-was-disabled') === '1';
                el.disabled = wasDisabled;
                el.removeAttribute('data-was-disabled');
            }
        });

        if (!overlay || !messageEl) return;
        messageEl.textContent = message || 'Working...';

        if (busy) {
            document.body.appendChild(overlay);
        } else if (overlay.parentElement) {
            overlay.parentElement.removeChild(overlay);
        }
    };

    forms.forEach((form) => {
        form.addEventListener('submit', () => {
            ensureOverlay();
            const message = form.getAttribute('data-loading-message') || 'Working...';
            window.requestAnimationFrame(() => {
                setBusy(true, message);
            });
        });
    });
}

function initMultiSelectLookups() {
    const roots = document.querySelectorAll('[data-multi-select]');
    if (roots.length === 0) return;

    const csrf = getCsrfToken();

    const showError = (root, message) => {
        const errorEl = root.querySelector('[data-ms-error]');
        if (!errorEl) return;
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    };

    const clearError = (root) => {
        const errorEl = root.querySelector('[data-ms-error]');
        if (!errorEl) return;
        errorEl.textContent = '';
        errorEl.classList.add('hidden');
    };

    const hasId = (root, id) => root.querySelector(`[data-ms-hidden][data-id="${id}"]`);

    const addSelection = (root, item) => {
        clearError(root);

        if (!item || !item.id || !item.name) return;
        if (hasId(root, String(item.id))) return;

        const selected = root.querySelector('[data-ms-selected]');
        if (!selected) return;

        const pill = document.createElement('span');
        pill.className =
            'inline-flex items-center gap-2 rounded-full bg-[var(--color-muted)] px-3 py-1 text-xs font-semibold text-slate-800 ring-1 ring-[var(--color-border)]';
        pill.setAttribute('data-ms-pill', '');
        pill.setAttribute('data-id', String(item.id));

        const label = document.createElement('span');
        label.textContent = item.name;

        const remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'rounded-full px-1 text-slate-700 hover:text-slate-900';
        remove.setAttribute('data-ms-remove', '');
        remove.setAttribute('data-id', String(item.id));
        remove.setAttribute('aria-label', 'Remove');
        remove.textContent = '×';

        pill.appendChild(label);
        pill.appendChild(remove);

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `${root.dataset.name}[]`;
        hidden.value = String(item.id);
        hidden.setAttribute('data-ms-hidden', '');
        hidden.setAttribute('data-id', String(item.id));

        selected.appendChild(pill);
        selected.appendChild(hidden);
    };

    const removeSelection = (root, id) => {
        const pill = root.querySelector(`[data-ms-pill][data-id="${id}"]`);
        const hidden = root.querySelector(`[data-ms-hidden][data-id="${id}"]`);
        if (pill) pill.remove();
        if (hidden) hidden.remove();
    };

    const populateSelect = (root, items) => {
        const select = root.querySelector('[data-ms-select]');
        if (!select) return;
        select.innerHTML = '';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select an option…';
        select.appendChild(placeholder);

        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = String(item.id);
            option.textContent = item.name;
            select.appendChild(option);
        });
    };

    const fetchOptions = async (root) => {
        const url = root.dataset.indexUrl;
        if (!url) return [];

        const response = await fetch(url, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Failed to load options.');
        }

        const json = await response.json();
        return Array.isArray(json?.data) ? json.data : [];
    };

    const createOption = async (root, name) => {
        const url = root.dataset.storeUrl;
        if (!url) throw new Error('Missing create endpoint.');

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ name }),
        });

        if (!response.ok) {
            const json = await response.json().catch(() => null);
            const message =
                json?.message ||
                (json?.errors && Object.values(json.errors).flat().join(' ')) ||
                'Failed to create option.';
            throw new Error(message);
        }

        const json = await response.json();
        return json?.data;
    };

    roots.forEach(async (root) => {
        try {
            const items = await fetchOptions(root);
            populateSelect(root, items);
        } catch (e) {
            showError(root, e instanceof Error ? e.message : 'Failed to load options.');
        }

        root.addEventListener('click', async (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;

            const removeBtn = target.closest('[data-ms-remove]');
            if (removeBtn) {
                const id = removeBtn.getAttribute('data-id');
                if (id) removeSelection(root, id);
                return;
            }

            if (target.closest('[data-ms-add-existing]')) {
                const select = root.querySelector('[data-ms-select]');
                const id = select?.value;
                if (!id) return;

                const label = select?.selectedOptions?.[0]?.textContent || '';
                addSelection(root, { id: Number(id), name: label });
                select.value = '';
                return;
            }

            if (target.closest('[data-ms-add-new]')) {
                const input = root.querySelector('[data-ms-new-name]');
                const name = input?.value?.trim();
                if (!name) return;

                try {
                    clearError(root);
                    const created = await createOption(root, name);
                    if (!created) throw new Error('Failed to create option.');

                    const select = root.querySelector('[data-ms-select]');
                    if (select) {
                        const option = document.createElement('option');
                        option.value = String(created.id);
                        option.textContent = created.name;
                        select.appendChild(option);
                    }

                    addSelection(root, created);
                    input.value = '';
                } catch (e) {
                    showError(root, e instanceof Error ? e.message : 'Failed to create option.');
                }
            }
        });
    });
}

function initMemberModals() {
    const openButtons = document.querySelectorAll('[data-member-open]');
    const modals = document.querySelectorAll('[data-member-modal]');

    if (openButtons.length === 0 || modals.length === 0) {
        return;
    }

    let lastFocused = null;

    const closeModal = (modal) => {
        if (!modal) return;
        modal.dataset.state = 'closed';
        const controlsId = modal.getAttribute('id');
        if (controlsId) {
            document.querySelectorAll(`[data-member-open][aria-controls="${controlsId}"]`).forEach((button) => {
                button.setAttribute('aria-expanded', 'false');
            });
        }

        window.setTimeout(() => {
            modal.hidden = true;
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }, 200);
    };

    const closeAllModals = () => {
        modals.forEach((modal) => {
            if (!modal.hidden) closeModal(modal);
        });
    };

    const openModal = (modal, trigger) => {
        if (!modal) return;
        closeAllModals();
        lastFocused = trigger || document.activeElement;

        modal.hidden = false;
        modal.dataset.state = 'open';

        const controlsId = modal.getAttribute('id');
        if (controlsId && trigger) {
            trigger.setAttribute('aria-expanded', 'true');
        }

        const focusTarget =
            modal.querySelector('[data-member-close]') ||
            modal.querySelector('.member-modal-panel') ||
            modal;

        window.setTimeout(() => {
            if (typeof focusTarget.focus === 'function') {
                focusTarget.focus();
            }
        }, 0);
    };

    openButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('aria-controls');
            const modal = id ? document.getElementById(id) : null;
            openModal(modal, button);
        });
    });

    modals.forEach((modal) => {
        modal.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;

            if (target.hasAttribute('data-member-close')) {
                closeModal(modal);
                return;
            }

            if (target === modal) {
                closeModal(modal);
            }
        });

        modal.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal(modal);
                return;
            }

            if (event.key !== 'Tab') return;

            const focusableSelectors = [
                'a[href]',
                'button:not([disabled])',
                'input:not([disabled])',
                'select:not([disabled])',
                'textarea:not([disabled])',
                '[tabindex]:not([tabindex="-1"])',
            ];

            const focusables = Array.from(modal.querySelectorAll(focusableSelectors.join(','))).filter(
                (el) => el instanceof HTMLElement && !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'),
            );

            if (focusables.length === 0) return;

            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            const active = document.activeElement;

            if (event.shiftKey) {
                if (active === first || active === modal) {
                    event.preventDefault();
                    last.focus();
                }
            } else {
                if (active === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });
    });
}

function initEventCards() {
    const cards = document.querySelectorAll('.event-card');
    if (cards.length === 0) return;

    const loadCard = (card) => {
        if (!(card instanceof HTMLElement)) return;

        const img = card.querySelector('.event-card-full');
        if (!(img instanceof HTMLImageElement)) {
            card.setAttribute('data-loaded', '1');
            return;
        }

        const existing = img.getAttribute('src');
        if (existing && existing !== '') return;

        const src = img.getAttribute('data-event-src');
        if (!src) {
            card.setAttribute('data-loaded', '1');
            return;
        }

        img.addEventListener(
            'load',
            () => {
                card.setAttribute('data-loaded', '1');
            },
            { once: true },
        );
        img.addEventListener(
            'error',
            () => {
                card.setAttribute('data-loaded', '1');
            },
            { once: true },
        );

        img.src = src;
    };

    if (!('IntersectionObserver' in window)) {
        cards.forEach(loadCard);
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                loadCard(entry.target);
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: '200px 0px' },
    );

    cards.forEach((card) => observer.observe(card));
}

function initEventSlideshow() {
    const viewRoot = document.querySelector('[data-events-view-root]');
    const slideshow = document.querySelector('[data-slideshow-root]');
    if (!(viewRoot instanceof HTMLElement) || !(slideshow instanceof HTMLElement)) {
        if (typeof window.__eventsSlideshowCleanup === 'function') {
            window.__eventsSlideshowCleanup();
            window.__eventsSlideshowCleanup = null;
        }
        return;
    }

    if (typeof window.__eventsSlideshowCleanup === 'function') {
        window.__eventsSlideshowCleanup();
        window.__eventsSlideshowCleanup = null;
    }

    const slides = Array.from(slideshow.querySelectorAll('[data-slide]')).filter((el) => el instanceof HTMLElement);
    if (slides.length === 0) return;

    const params = new URLSearchParams(window.location.search);
    const stateKey = `events_slideshow_index:${params.toString()}`;

    const storedIndexRaw = window.sessionStorage.getItem(stateKey);
    let index = storedIndexRaw ? Number(storedIndexRaw) : 0;
    if (!Number.isFinite(index) || index < 0) index = 0;
    if (index >= slides.length) index = 0;

    let timer = null;
    let paused = false;
    let lastTickAt = 0;
    let remainingMs = 10000;

    const clearTimer = () => {
        if (timer) window.clearTimeout(timer);
        timer = null;
    };

    const ensureFullLoaded = (slide) => {
        if (!(slide instanceof HTMLElement)) return;
        const full = slide.querySelector('.event-slide-full');
        if (!(full instanceof HTMLImageElement)) {
            slide.setAttribute('data-loaded', '1');
            return;
        }

        const existing = full.getAttribute('src');
        if (existing && existing !== '') {
            slide.setAttribute('data-loaded', '1');
            return;
        }

        const src = full.getAttribute('data-src');
        if (!src) {
            slide.setAttribute('data-loaded', '1');
            return;
        }

        full.addEventListener(
            'load',
            () => {
                slide.setAttribute('data-loaded', '1');
            },
            { once: true },
        );
        full.addEventListener(
            'error',
            () => {
                slide.setAttribute('data-loaded', '1');
            },
            { once: true },
        );
        full.src = src;
    };

    const setActive = (nextIndex, userInitiated = false) => {
        if (!Number.isFinite(nextIndex)) return;
        const normalized = ((nextIndex % slides.length) + slides.length) % slides.length;

        const prev = slides[index];
        const next = slides[normalized];

        if (prev instanceof HTMLElement) {
            prev.setAttribute('data-active', '0');
            prev.setAttribute('aria-hidden', 'true');
        }

        index = normalized;
        window.sessionStorage.setItem(stateKey, String(index));

        if (next instanceof HTMLElement) {
            next.setAttribute('data-active', '1');
            next.setAttribute('aria-hidden', 'false');

            const counter = next.querySelector('[data-slideshow-counter]');
            if (counter instanceof HTMLElement) {
                counter.textContent = `Event ${index + 1} of ${slides.length}`;
            }
        }

        ensureFullLoaded(slides[index]);
        ensureFullLoaded(slides[(index + 1) % slides.length]);
        ensureFullLoaded(slides[(index - 1 + slides.length) % slides.length]);

        if (userInitiated) {
            remainingMs = 10000;
        }
    };

    const scheduleNext = () => {
        clearTimer();
        if (paused) return;
        if (viewRoot.getAttribute('data-view') !== 'slideshow') return;

        lastTickAt = Date.now();
        timer = window.setTimeout(() => {
            setActive(index + 1);
            remainingMs = 10000;
            scheduleNext();
        }, remainingMs);
    };

    const pause = () => {
        if (paused) return;
        paused = true;
        const elapsed = lastTickAt ? Date.now() - lastTickAt : 0;
        remainingMs = Math.max(250, remainingMs - elapsed);
        clearTimer();
    };

    const resume = () => {
        if (!paused) return;
        paused = false;
        scheduleNext();
    };

    const applyMode = (mode) => {
        const nextMode = mode === 'cards' ? 'cards' : 'slideshow';
        viewRoot.setAttribute('data-view', nextMode);
        window.localStorage.setItem('events_view_mode', nextMode);

        viewRoot.querySelectorAll('[data-events-view]').forEach((btn) => {
            if (!(btn instanceof HTMLElement)) return;
            const isActive = btn.getAttribute('data-events-view') === nextMode;
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        if (nextMode === 'slideshow') {
            remainingMs = 10000;
            scheduleNext();
        } else {
            clearTimer();
        }
    };

    if (viewRoot.getAttribute('data-view-toggle-init') !== '1') {
        viewRoot.setAttribute('data-view-toggle-init', '1');

        viewRoot.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            const btn = target.closest('[data-events-view]');
            if (!(btn instanceof HTMLElement)) return;
            const mode = btn.getAttribute('data-events-view') || 'slideshow';
            applyMode(mode);
        });

        const saved = window.localStorage.getItem('events_view_mode');
        applyMode(saved === 'cards' ? 'cards' : 'slideshow');
    } else {
        const saved = window.localStorage.getItem('events_view_mode');
        viewRoot.setAttribute('data-view', saved === 'cards' ? 'cards' : 'slideshow');
        viewRoot.querySelectorAll('[data-events-view]').forEach((btn) => {
            if (!(btn instanceof HTMLElement)) return;
            const isActive = btn.getAttribute('data-events-view') === viewRoot.getAttribute('data-view');
            btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
    }

    setActive(index);

    const onMouseEnter = () => pause();
    const onMouseLeave = () => resume();
    const onFocusIn = () => pause();
    const onFocusOut = () => resume();

    slideshow.addEventListener('mouseenter', onMouseEnter);
    slideshow.addEventListener('mouseleave', onMouseLeave);
    slideshow.addEventListener('focusin', onFocusIn);
    slideshow.addEventListener('focusout', onFocusOut);

    let touchPauseTimer = null;
    const onTouchStart = () => {
        pause();
        if (touchPauseTimer) window.clearTimeout(touchPauseTimer);
    };
    const onTouchEnd = () => {
        if (touchPauseTimer) window.clearTimeout(touchPauseTimer);
        touchPauseTimer = window.setTimeout(() => resume(), 600);
    };

    slideshow.addEventListener('touchstart', onTouchStart, { passive: true });
    slideshow.addEventListener('touchend', onTouchEnd, { passive: true });

    const onControlClick = (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        const prev = target.closest('[data-slideshow-prev]');
        const next = target.closest('[data-slideshow-next]');
        if (!prev && !next) return;

        event.preventDefault();
        pause();

        if (prev) setActive(index - 1, true);
        if (next) setActive(index + 1, true);

        paused = false;
        remainingMs = 10000;
        scheduleNext();
    };

    slideshow.addEventListener('click', onControlClick);

    const onKeyDown = (event) => {
        if (viewRoot.getAttribute('data-view') !== 'slideshow') return;
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            setActive(index - 1, true);
            remainingMs = 10000;
            scheduleNext();
        }
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            setActive(index + 1, true);
            remainingMs = 10000;
            scheduleNext();
        }
    };

    slideshow.addEventListener('keydown', onKeyDown);

    if (viewRoot.getAttribute('data-view') === 'slideshow') {
        remainingMs = 10000;
        scheduleNext();
    }

    window.__eventsSlideshowCleanup = () => {
        clearTimer();
        if (touchPauseTimer) window.clearTimeout(touchPauseTimer);
        slideshow.removeEventListener('mouseenter', onMouseEnter);
        slideshow.removeEventListener('mouseleave', onMouseLeave);
        slideshow.removeEventListener('focusin', onFocusIn);
        slideshow.removeEventListener('focusout', onFocusOut);
        slideshow.removeEventListener('touchstart', onTouchStart);
        slideshow.removeEventListener('touchend', onTouchEnd);
        slideshow.removeEventListener('click', onControlClick);
        slideshow.removeEventListener('keydown', onKeyDown);
    };
}

export function homeSlideshowFindNextIndexBounded(startIndex, direction, brokenIndices, count) {
    const dir = direction >= 0 ? 1 : -1;
    const len = Number.isFinite(count) ? count : 0;
    const start = Number.isFinite(startIndex) ? startIndex : 0;
    const broken = brokenIndices instanceof Set ? brokenIndices : new Set();

    if (len <= 0) return -1;

    if (dir === 1) {
        for (let i = start; i < len; i++) {
            if (!broken.has(i)) return i;
        }
        return -1;
    }

    for (let i = start; i >= 0; i--) {
        if (!broken.has(i)) return i;
    }
    return -1;
}

export function musicSheetCarouselBoundedNextIndex(currentIndex, direction, count) {
    const len = Number.isFinite(count) ? count : 0;
    const current = Number.isFinite(currentIndex) ? currentIndex : 0;
    const dir = direction >= 0 ? 1 : -1;

    if (len <= 0) return -1;
    const next = current + dir;
    if (next < 0 || next >= len) return -1;
    return next;
}

function initHomeSlideshow() {
    const root = document.querySelector('[data-home-slideshow]');
    if (!(root instanceof HTMLElement)) {
        if (typeof window.__homeSlideshowCleanup === 'function') {
            window.__homeSlideshowCleanup();
            window.__homeSlideshowCleanup = null;
        }
        return;
    }

    if (typeof window.__homeSlideshowCleanup === 'function') {
        window.__homeSlideshowCleanup();
        window.__homeSlideshowCleanup = null;
    }

    const stage = root.querySelector('[data-home-slideshow-stage]');
    if (!(stage instanceof HTMLElement)) return;

    const statusEl = root.querySelector('[data-home-slideshow-status]');
    const prevBtn = root.querySelector('[data-prev]');
    const nextBtn = root.querySelector('[data-next]');

    const slides = Array.from(root.querySelectorAll('[data-slide]')).filter((el) => el instanceof HTMLImageElement);
    if (slides.length === 0) return;

    let index = slides.findIndex((img) => img.getAttribute('data-active') === '1');
    if (index < 0) index = 0;

    let timer = null;
    let paused = false;
    let lastTickAt = 0;
    let remainingMs = 4000;
    let pendingSwapToken = 0;
    let leavingTimer = null;

    const brokenIndices = new Set();

    const clearTimer = () => {
        if (timer) window.clearTimeout(timer);
        timer = null;
    };

    const setStatus = (message) => {
        if (!(statusEl instanceof HTMLElement)) return;
        statusEl.textContent = message || '';
    };

    const setButtonDisabled = (btn, disabled) => {
        if (!(btn instanceof HTMLButtonElement)) return;
        btn.disabled = !!disabled;
        btn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        btn.tabIndex = disabled ? -1 : 0;
    };

    const setTransitioning = (transitioning) => {
        root.setAttribute('data-transitioning', transitioning ? '1' : '0');
        stage.setAttribute('aria-busy', transitioning ? 'true' : 'false');
        if (transitioning) {
            setButtonDisabled(prevBtn, true);
            setButtonDisabled(nextBtn, true);
        }
    };

    const syncControls = () => {
        const prevIndex = homeSlideshowFindNextIndexBounded(index - 1, -1, brokenIndices, slides.length);
        const nextIndex = homeSlideshowFindNextIndexBounded(index + 1, 1, brokenIndices, slides.length);
        setButtonDisabled(prevBtn, prevIndex < 0);
        setButtonDisabled(nextBtn, nextIndex < 0);
    };

    const ensureLoaded = (img) => {
        if (!(img instanceof HTMLImageElement)) return;
        const idx = slides.indexOf(img);
        if (idx < 0) return;
        if (brokenIndices.has(idx)) return;

        const currentSrc = img.getAttribute('src') || '';
        if (currentSrc) return;

        const src = img.getAttribute('data-src') || '';
        if (!src) {
            brokenIndices.add(idx);
            syncControls();
            return;
        }

        const srcset = img.getAttribute('data-srcset') || '';
        if (srcset) {
            img.srcset = srcset;
        }
        const sizes = img.getAttribute('data-sizes') || '';
        if (sizes) {
            img.sizes = sizes;
        }

        img.addEventListener(
            'error',
            () => {
                brokenIndices.add(idx);
                setStatus('Some slideshow images failed to load.');
                syncControls();

                if (brokenIndices.size >= slides.length) {
                    clearTimer();
                    setStatus('Slideshow could not be displayed because images failed to load.');
                    return;
                }

                if (idx === index) {
                    const nextAvailable = homeSlideshowFindNextIndexBounded(index + 1, 1, brokenIndices, slides.length);
                    if (nextAvailable < 0) {
                        clearTimer();
                        setTransitioning(false);
                        syncControls();
                        return;
                    }
                    setActive(nextAvailable, true, () => {
                        remainingMs = 4000;
                        scheduleNext();
                    });
                }
            },
            { once: true },
        );

        img.src = src;
    };

    const setActive = (nextIndex, userInitiated = false, afterSwap = null) => {
        if (!Number.isFinite(nextIndex)) return;
        clearTimer();
        pendingSwapToken++;
        const swapToken = pendingSwapToken;
        if (leavingTimer) window.clearTimeout(leavingTimer);
        leavingTimer = null;

        const resolved = homeSlideshowFindNextIndexBounded(nextIndex, nextIndex >= index ? 1 : -1, brokenIndices, slides.length);
        if (resolved < 0) return;

        const prev = slides[index];
        const next = slides[resolved];

        const effects = [
            { inName: 'homeSlideInZoom', outName: 'homeSlideOutZoom' },
            { inName: 'homeSlideInLeft', outName: 'homeSlideOutLeft' },
            { inName: 'homeSlideInRight', outName: 'homeSlideOutRight' },
            { inName: 'homeSlideInUp', outName: 'homeSlideOutDown' },
        ];
        const chosen = effects[Math.floor(Math.random() * effects.length)];
        if (chosen) {
            stage.style.setProperty('--home-slideshow-in', chosen.inName);
            stage.style.setProperty('--home-slideshow-out', chosen.outName);
        }

        const finish = () => {
            if (swapToken !== pendingSwapToken) return;

            if (next instanceof HTMLImageElement) {
                next.setAttribute('data-active', '1');
                next.setAttribute('aria-hidden', 'false');
                next.removeAttribute('data-leaving');
            }

            if (prev instanceof HTMLImageElement && prev !== next) {
                prev.setAttribute('data-leaving', '1');
                prev.setAttribute('aria-hidden', 'true');
            }

            leavingTimer = window.setTimeout(() => {
                if (swapToken !== pendingSwapToken) return;
                if (prev instanceof HTMLImageElement && prev !== next) {
                    prev.removeAttribute('data-leaving');
                    prev.setAttribute('data-active', '0');
                }
                leavingTimer = null;
            }, 560);

            index = resolved;
            setTransitioning(false);
            syncControls();

            if (typeof afterSwap === 'function') {
                afterSwap();
            }
        };

        setTransitioning(true);
        ensureLoaded(next);

        if (brokenIndices.has(resolved)) {
            setTransitioning(false);
            syncControls();
            return;
        }

        if (next.complete && next.naturalWidth > 0) {
            finish();
        } else {
            setStatus('Loading slideshow image…');
            next.addEventListener(
                'load',
                () => {
                    if (swapToken !== pendingSwapToken) return;
                    setStatus('');
                    finish();
                },
                { once: true },
            );
        }

        ensureLoaded(slides[index]);
        const peekNext = homeSlideshowFindNextIndexBounded(resolved + 1, 1, brokenIndices, slides.length);
        const peekPrev = homeSlideshowFindNextIndexBounded(resolved - 1, -1, brokenIndices, slides.length);
        if (peekNext >= 0) ensureLoaded(slides[peekNext]);
        if (peekPrev >= 0) ensureLoaded(slides[peekPrev]);

        if (userInitiated) {
            remainingMs = 4000;
        }
    };

    const scheduleNext = () => {
        clearTimer();
        if (paused) return;
        if (brokenIndices.size >= slides.length) return;
        const nextAvailable = homeSlideshowFindNextIndexBounded(index + 1, 1, brokenIndices, slides.length);
        if (nextAvailable < 0) {
            syncControls();
            return;
        }

        lastTickAt = Date.now();
        timer = window.setTimeout(() => {
            const nextIdx = homeSlideshowFindNextIndexBounded(index + 1, 1, brokenIndices, slides.length);
            if (nextIdx < 0) {
                clearTimer();
                syncControls();
                return;
            }
            setActive(nextIdx, false, () => {
                remainingMs = 4000;
                scheduleNext();
            });
        }, remainingMs);
    };

    const pause = () => {
        if (paused) return;
        paused = true;
        const elapsed = lastTickAt ? Date.now() - lastTickAt : 0;
        remainingMs = Math.max(250, remainingMs - elapsed);
        clearTimer();
    };

    const resume = () => {
        if (!paused) return;
        paused = false;
        scheduleNext();
    };

    const onMouseEnter = () => pause();
    const onMouseLeave = () => resume();
    const onFocusIn = () => pause();
    const onFocusOut = () => resume();

    root.addEventListener('mouseenter', onMouseEnter);
    root.addEventListener('mouseleave', onMouseLeave);
    root.addEventListener('focusin', onFocusIn);
    root.addEventListener('focusout', onFocusOut);

    let touchPauseTimer = null;
    const onTouchStart = () => {
        pause();
        if (touchPauseTimer) window.clearTimeout(touchPauseTimer);
    };
    const onTouchEnd = () => {
        if (touchPauseTimer) window.clearTimeout(touchPauseTimer);
        touchPauseTimer = window.setTimeout(() => resume(), 600);
    };

    root.addEventListener('touchstart', onTouchStart, { passive: true });
    root.addEventListener('touchend', onTouchEnd, { passive: true });

    const onClick = (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;

        const prev = target.closest('[data-prev]');
        const next = target.closest('[data-next]');
        if (!prev && !next) return;

        event.preventDefault();
        pause();

        if (prev) {
            const prevIdx = homeSlideshowFindNextIndexBounded(index - 1, -1, brokenIndices, slides.length);
            if (prevIdx < 0) {
                syncControls();
                return;
            }
            setActive(prevIdx, true, () => {
                paused = false;
                remainingMs = 4000;
                scheduleNext();
            });
        }
        if (next) {
            const nextIdx = homeSlideshowFindNextIndexBounded(index + 1, 1, brokenIndices, slides.length);
            if (nextIdx < 0) {
                syncControls();
                return;
            }
            setActive(nextIdx, true, () => {
                paused = false;
                remainingMs = 4000;
                scheduleNext();
            });
        }
    };

    root.addEventListener('click', onClick);

    const onKeyDown = (event) => {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            const prevIdx = homeSlideshowFindNextIndexBounded(index - 1, -1, brokenIndices, slides.length);
            if (prevIdx < 0) {
                syncControls();
                return;
            }
            setActive(prevIdx, true, () => {
                remainingMs = 4000;
                scheduleNext();
            });
        }
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            const nextIdx = homeSlideshowFindNextIndexBounded(index + 1, 1, brokenIndices, slides.length);
            if (nextIdx < 0) {
                syncControls();
                return;
            }
            setActive(nextIdx, true, () => {
                remainingMs = 4000;
                scheduleNext();
            });
        }
    };

    stage.addEventListener('keydown', onKeyDown);

    const onVisibilityChange = () => {
        if (document.hidden) {
            pause();
        } else {
            resume();
        }
    };
    document.addEventListener('visibilitychange', onVisibilityChange);

    setActive(index, true);
    syncControls();

    if (slides.length > 1) {
        remainingMs = 4000;
        scheduleNext();
    }

    window.__homeSlideshowCleanup = () => {
        clearTimer();
        if (leavingTimer) window.clearTimeout(leavingTimer);
        if (touchPauseTimer) window.clearTimeout(touchPauseTimer);
        root.removeEventListener('mouseenter', onMouseEnter);
        root.removeEventListener('mouseleave', onMouseLeave);
        root.removeEventListener('focusin', onFocusIn);
        root.removeEventListener('focusout', onFocusOut);
        root.removeEventListener('touchstart', onTouchStart);
        root.removeEventListener('touchend', onTouchEnd);
        root.removeEventListener('click', onClick);
        stage.removeEventListener('keydown', onKeyDown);
        document.removeEventListener('visibilitychange', onVisibilityChange);
    };
}

function initEventModals() {
    const modal = document.querySelector('[data-event-modal]');
    if (!(modal instanceof HTMLElement)) return;

    const panel = modal.querySelector('.event-modal-panel');
    const titleEl = modal.querySelector('#event-modal-title');
    const scheduleEl = modal.querySelector('[data-event-modal-schedule]');
    const locationEl = modal.querySelector('[data-event-modal-location]');
    const aboutEl = modal.querySelector('[data-event-modal-about]');
    const thumbEl = modal.querySelector('[data-event-modal-thumb]');
    const fullEl = modal.querySelector('[data-event-modal-full]');

    if (!(panel instanceof HTMLElement)) return;

    let lastFocused = null;
    let currentTrigger = null;

    const openModal = (data, trigger) => {
        lastFocused = trigger || document.activeElement;
        currentTrigger = trigger || null;

        if (currentTrigger instanceof HTMLElement) {
            currentTrigger.setAttribute('aria-expanded', 'true');
        }

        const title = String(data?.title || '');
        const schedule = String(data?.schedule || '');
        const location = String(data?.location || '');
        const about = String(data?.about || '');
        const photo = data?.photo_url ? String(data.photo_url) : '';
        const thumb = data?.photo_thumb_url ? String(data.photo_thumb_url) : '';

        if (titleEl) titleEl.textContent = title || 'Event';
        if (scheduleEl instanceof HTMLElement) scheduleEl.textContent = schedule || '—';
        if (locationEl instanceof HTMLElement) locationEl.textContent = location || '—';
        if (aboutEl instanceof HTMLElement) aboutEl.textContent = about || 'No description provided.';

        const thumbUrl = thumb ? thumb : photo;
        if (thumbEl instanceof HTMLImageElement) {
            thumbEl.src = thumbUrl ? thumbUrl : '';
        }

        if (fullEl instanceof HTMLImageElement) {
            fullEl.classList.remove('opacity-100');
            fullEl.classList.add('opacity-0');
            fullEl.src = '';
        }

        if (photo && fullEl instanceof HTMLImageElement) {
            fullEl.addEventListener(
                'load',
                () => {
                    fullEl.classList.remove('opacity-0');
                    fullEl.classList.add('opacity-100');
                },
                { once: true },
            );
            fullEl.src = photo;
        }

        modal.hidden = false;
        modal.dataset.state = 'open';

        window.setTimeout(() => {
            const closeBtn = modal.querySelector('[data-event-close][aria-label="Close dialog"]');
            if (closeBtn instanceof HTMLElement) {
                closeBtn.focus();
            } else {
                panel.focus();
            }
        }, 0);
    };

    const closeModal = () => {
        modal.dataset.state = 'closed';

        if (currentTrigger instanceof HTMLElement) {
            currentTrigger.setAttribute('aria-expanded', 'false');
        }

        window.setTimeout(() => {
            modal.hidden = true;
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }, 200);
    };

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;

        const openBtn = target.closest('[data-event-open]');
        if (openBtn) {
            const json = openBtn.getAttribute('data-event') || '{}';
            let data = {};
            try {
                data = JSON.parse(json);
            } catch {
                data = {};
            }
            openModal(data, openBtn);
            return;
        }

        if (target.closest('[data-event-close]') && !modal.hidden) {
            closeModal();
        }
    });

    modal.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.key !== 'Tab') return;

        const focusableSelectors = [
            'a[href]',
            'button:not([disabled])',
            'input:not([disabled])',
            'select:not([disabled])',
            'textarea:not([disabled])',
            '[tabindex]:not([tabindex="-1"])',
        ];

        const focusables = Array.from(modal.querySelectorAll(focusableSelectors.join(','))).filter(
            (el) => el instanceof HTMLElement && !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'),
        );

        if (focusables.length === 0) return;

        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        const active = document.activeElement;

        if (event.shiftKey) {
            if (active === first || active === modal) {
                event.preventDefault();
                last.focus();
            }
        } else {
            if (active === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });
}

function initUploadPreviews() {
    const dropzones = document.querySelectorAll('[data-upload-dropzone]');

    if (dropzones.length === 0) return;

    const renderPreviews = (input, preview) => {
        if (!input || !preview) return;
        preview.innerHTML = '';

        const files = Array.from(input.files || []);
        files.forEach((file) => {
            if (!file.type || !file.type.startsWith('image/')) return;

            const url = URL.createObjectURL(file);
            const card = document.createElement('div');
            card.className =
                'overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white shadow-sm';

            const img = document.createElement('img');
            img.src = url;
            img.alt = file.name;
            img.loading = 'lazy';
            img.className = 'h-28 w-full object-cover';
            img.addEventListener('load', () => URL.revokeObjectURL(url), { once: true });

            const label = document.createElement('div');
            label.className = 'px-3 py-2 text-xs font-semibold text-slate-800';
            label.textContent = file.name;

            card.appendChild(img);
            card.appendChild(label);
            preview.appendChild(card);
        });
    };

    const mergeFiles = (currentFiles, newFiles) => {
        const dt = new DataTransfer();
        Array.from(currentFiles || []).forEach((f) => dt.items.add(f));
        Array.from(newFiles || []).forEach((f) => dt.items.add(f));
        return dt.files;
    };

    dropzones.forEach((zone) => {
        const input = zone.querySelector('[data-upload-input]');
        const preview = zone.parentElement?.querySelector('[data-upload-preview]');

        if (!(input instanceof HTMLInputElement)) return;

        input.addEventListener('change', () => renderPreviews(input, preview));

        zone.addEventListener('dragover', (event) => {
            event.preventDefault();
            zone.classList.add('ring-2', 'ring-[var(--color-primary)]');
        });

        zone.addEventListener('dragleave', () => {
            zone.classList.remove('ring-2', 'ring-[var(--color-primary)]');
        });

        zone.addEventListener('drop', (event) => {
            event.preventDefault();
            zone.classList.remove('ring-2', 'ring-[var(--color-primary)]');

            const files = event.dataTransfer?.files;
            if (!files || files.length === 0) return;

            input.files = mergeFiles(input.files, files);
            renderPreviews(input, preview);
        });

        renderPreviews(input, preview);
    });
}

function initAdminSlideshow() {
    const root = document.querySelector('[data-admin-slideshow]');
    if (!(root instanceof HTMLElement)) return;

    const form = root.querySelector('[data-admin-slideshow-form]');
    const drop = root.querySelector('[data-admin-slideshow-drop]');
    const input = root.querySelector('[data-admin-slideshow-input]');
    const browse = root.querySelector('[data-admin-slideshow-browse]');
    const upload = root.querySelector('[data-admin-slideshow-upload]');
    const queue = root.querySelector('[data-admin-slideshow-queue]');
    const queueList = root.querySelector('[data-admin-slideshow-queue-list]');
    const targetWrap = root.querySelector('[data-admin-slideshow-target-wrap]');
    const targetEl = root.querySelector('[data-admin-slideshow-target]');
    const targetPreview = root.querySelector('[data-admin-slideshow-target-preview]');
    const targetLabel = root.querySelector('[data-admin-slideshow-target-label]');
    const targetWidthInput = root.querySelector('[data-admin-slideshow-target-width]');
    const targetHeightInput = root.querySelector('[data-admin-slideshow-target-height]');
    const progress = root.querySelector('[data-admin-slideshow-progress]');
    const progressText = root.querySelector('[data-admin-slideshow-progress-text]');
    const progressBar = root.querySelector('[data-admin-slideshow-progress-bar]');
    const errorBox = root.querySelector('[data-admin-slideshow-error]');

    const bulkForm = root.querySelector('[data-admin-slideshow-bulk-form]');
    const bulkDelete = root.querySelector('[data-admin-slideshow-bulk-delete]');
    const selects = Array.from(root.querySelectorAll('[data-admin-slideshow-select]'));

    const modal = document.querySelector('[data-admin-slideshow-preview-modal]');
    const modalImg = document.querySelector('[data-admin-slideshow-preview-img]');
    const modalCloses = Array.from(document.querySelectorAll('[data-admin-slideshow-preview-close]'));
    const previewButtons = Array.from(root.querySelectorAll('[data-admin-slideshow-preview]'));

    if (!(form instanceof HTMLFormElement)) return;
    if (!(drop instanceof HTMLElement)) return;
    if (!(input instanceof HTMLInputElement)) return;
    if (!(browse instanceof HTMLElement)) return;
    if (!(upload instanceof HTMLButtonElement)) return;

    const csrf = getCsrfToken();

    const getTargetSize = () => {
        const fallback = { w: 1600, h: 700 };
        if (!(targetEl instanceof HTMLElement)) return fallback;
        const rect = targetEl.getBoundingClientRect();
        const w = Math.round(rect.width);
        const h = Math.round(rect.height);
        if (!Number.isFinite(w) || !Number.isFinite(h) || w <= 0 || h <= 0) return fallback;
        return { w, h };
    };

    const syncTargetInputs = () => {
        const { w, h } = getTargetSize();
        if (targetWidthInput instanceof HTMLInputElement) targetWidthInput.value = String(w);
        if (targetHeightInput instanceof HTMLInputElement) targetHeightInput.value = String(h);
        if (targetLabel instanceof HTMLElement) targetLabel.textContent = `${w}×${h}`;
        return { w, h };
    };

    const coverCropToDataUrl = async (file, dstW, dstH, mime, quality) => {
        const url = URL.createObjectURL(file);
        try {
            const img = new Image();
            img.decoding = 'async';
            const loaded = new Promise((resolve, reject) => {
                img.onload = () => resolve(true);
                img.onerror = () => reject(new Error('Failed to load image.'));
            });
            img.src = url;
            await loaded;

            const srcW = img.naturalWidth || img.width || 0;
            const srcH = img.naturalHeight || img.height || 0;
            if (srcW <= 0 || srcH <= 0) throw new Error('Invalid image dimensions.');

            const canvas = document.createElement('canvas');
            canvas.width = Math.max(1, Math.floor(dstW));
            canvas.height = Math.max(1, Math.floor(dstH));
            const ctx = canvas.getContext('2d');
            if (!ctx) throw new Error('Failed to allocate canvas context.');

            const scale = Math.max(canvas.width / srcW, canvas.height / srcH);
            const cropW = canvas.width / scale;
            const cropH = canvas.height / scale;
            const sx = Math.max(0, (srcW - cropW) / 2);
            const sy = Math.max(0, (srcH - cropH) / 2);

            ctx.drawImage(img, sx, sy, cropW, cropH, 0, 0, canvas.width, canvas.height);

            if (mime === 'image/jpeg') {
                return canvas.toDataURL('image/jpeg', typeof quality === 'number' ? quality : 0.8);
            }
            if (mime === 'image/webp') {
                return canvas.toDataURL('image/webp', typeof quality === 'number' ? quality : 0.8);
            }
            return canvas.toDataURL('image/png');
        } finally {
            URL.revokeObjectURL(url);
        }
    };

    const setError = (message) => {
        if (!(errorBox instanceof HTMLElement)) return;
        if (!message) {
            errorBox.classList.add('hidden');
            errorBox.textContent = '';
            return;
        }
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
    };

    const setUploading = (isUploading) => {
        upload.disabled = !!isUploading || !input.files || input.files.length === 0;
        upload.setAttribute('aria-disabled', upload.disabled ? 'true' : 'false');
        browse.toggleAttribute('disabled', !!isUploading);
        input.toggleAttribute('disabled', !!isUploading);
    };

    const renderQueue = () => {
        if (!(queue instanceof HTMLElement) || !(queueList instanceof HTMLElement)) return;
        const files = Array.from(input.files || []);
        if (files.length === 0) {
            queue.classList.add('hidden');
            queueList.innerHTML = '';
            if (targetWrap instanceof HTMLElement) targetWrap.classList.add('hidden');
            upload.disabled = true;
            upload.setAttribute('aria-disabled', 'true');
            return;
        }

        const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/pjpeg', 'image/jfif', 'image/tiff', 'image/heif', 'image/heic'];
        const hasTargetWrap = targetWrap instanceof HTMLElement;
        if (hasTargetWrap) targetWrap.classList.remove('hidden');
        const { w: targetW, h: targetH } = syncTargetInputs();

        const ratio = targetW > 0 ? targetH / targetW : 0.4375;
        const mediumW = Math.max(480, Math.min(1400, Math.round(targetW * 0.6)));
        const mediumH = Math.max(1, Math.round(mediumW * ratio));

        queueList.innerHTML = '';
        const slice = files.slice(0, 10);
        const validities = slice.map((file) => {
            const name = (file.name || '').toLowerCase();
            const extOk =
                name.endsWith('.jpg') ||
                name.endsWith('.jpeg') ||
                name.endsWith('.png') ||
                name.endsWith('.webp') ||
                name.endsWith('.jfif') ||
                name.endsWith('.tif') ||
                name.endsWith('.heif');
            const typeOk = !file.type ? extOk : allowed.includes(file.type);
            return { file, ok: typeOk, typeOk };
        });

        validities.forEach(({ file, ok, typeOk }) => {
            const li = document.createElement('li');
            li.className = ok ? 'text-slate-700' : 'text-red-700';

            const row = document.createElement('div');
            row.className = 'flex items-center gap-3';

            const thumb = document.createElement('img');
            thumb.alt = '';
            thumb.decoding = 'async';
            thumb.loading = 'lazy';
            thumb.className = 'h-10 w-10 rounded-lg bg-white object-cover ring-1 ring-[var(--color-border)]';
            row.appendChild(thumb);

            const meta = document.createElement('div');
            meta.className = 'min-w-0';

            const title = document.createElement('div');
            title.className = 'truncate text-xs font-semibold';
            title.textContent = String(file.name || 'Untitled');
            meta.appendChild(title);

            const detail = document.createElement('div');
            detail.className = 'text-[11px] font-semibold text-slate-600';
            detail.textContent = `${Math.round((file.size || 0) / 1024)} KB · preview ${mediumW}×${mediumH}${ok ? '' : ` · ${!typeOk ? 'unsupported format' : ''}`}`;
            meta.appendChild(detail);

            row.appendChild(meta);
            li.appendChild(row);
            queueList.appendChild(li);

            if (!ok) return;

            const wantsPng = (file.type || '').toLowerCase() === 'image/png' || (file.name || '').toLowerCase().endsWith('.png');
            const wantsWebp = (file.type || '').toLowerCase() === 'image/webp' || (file.name || '').toLowerCase().endsWith('.webp');
            const previewMime = wantsPng ? 'image/png' : wantsWebp ? 'image/webp' : 'image/jpeg';
            coverCropToDataUrl(file, mediumW, mediumH, previewMime, 0.8)
                .then((dataUrl) => {
                    if (thumb instanceof HTMLImageElement) thumb.src = dataUrl;
                    if (targetPreview instanceof HTMLImageElement && file === validities[0]?.file) {
                        targetPreview.hidden = false;
                        targetPreview.src = dataUrl;
                    }
                })
                .catch(() => {
                    if (thumb instanceof HTMLImageElement) thumb.remove();
                });
        });
        if (files.length > 10) {
            const li = document.createElement('li');
            li.className = 'text-slate-600';
            li.textContent = `+${files.length - 10} more file(s)…`;
            queueList.appendChild(li);
        }

        queue.classList.remove('hidden');
        const allOk = validities.every((v) => v.ok);
        upload.disabled = !allOk;
        upload.setAttribute('aria-disabled', upload.disabled ? 'true' : 'false');
    };

    const setProgress = (pct) => {
        if (!(progress instanceof HTMLElement) || !(progressText instanceof HTMLElement) || !(progressBar instanceof HTMLElement)) return;
        progress.classList.remove('hidden');
        const safe = Math.max(0, Math.min(100, pct));
        progressText.textContent = `${safe}%`;
        progressBar.style.width = `${safe}%`;
    };

    const hideProgress = () => {
        if (!(progress instanceof HTMLElement)) return;
        progress.classList.add('hidden');
        if (progressText instanceof HTMLElement) progressText.textContent = '0%';
        if (progressBar instanceof HTMLElement) progressBar.style.width = '0%';
    };

    const mergeFiles = (currentFiles, newFiles) => {
        const dt = new DataTransfer();
        Array.from(currentFiles || []).forEach((f) => dt.items.add(f));
        Array.from(newFiles || []).forEach((f) => dt.items.add(f));
        return dt.files;
    };

    browse.addEventListener('click', () => input.click());
    input.addEventListener('change', () => {
        setError('');
        renderQueue();
    });

    drop.addEventListener('dragover', (event) => {
        event.preventDefault();
        drop.classList.add('ring-2', 'ring-[var(--color-primary)]');
    });
    drop.addEventListener('dragleave', () => drop.classList.remove('ring-2', 'ring-[var(--color-primary)]'));
    drop.addEventListener('drop', (event) => {
        event.preventDefault();
        drop.classList.remove('ring-2', 'ring-[var(--color-primary)]');
        const files = event.dataTransfer?.files;
        if (!files || files.length === 0) return;
        input.files = mergeFiles(input.files, files);
        setError('');
        renderQueue();
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        setError('');

        const files = Array.from(input.files || []);
        if (files.length === 0) return;

        const data = new FormData(form);

        setUploading(true);
        setProgress(0);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', csrf);

        xhr.upload.onprogress = (e) => {
            if (!e.lengthComputable) return;
            setProgress(Math.round((e.loaded / e.total) * 100));
        };

        xhr.onload = () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.reload();
                return;
            }

            const json = (() => {
                try {
                    return JSON.parse(xhr.responseText || '{}');
                } catch {
                    return null;
                }
            })();

            const message = json?.message || 'Upload failed. Please try again.';
            setError(message);
            setUploading(false);
            hideProgress();
        };

        xhr.onerror = () => {
            setError('Upload failed. Please check your connection and try again.');
            setUploading(false);
            hideProgress();
        };

        xhr.send(data);
    });

    const refreshBulk = () => {
        if (!(bulkDelete instanceof HTMLButtonElement)) return;
        const checked = selects.filter((el) => el instanceof HTMLInputElement && el.checked).length;
        bulkDelete.disabled = checked === 0;
        bulkDelete.setAttribute('aria-disabled', bulkDelete.disabled ? 'true' : 'false');
    };

    selects.forEach((el) => {
        if (!(el instanceof HTMLInputElement)) return;
        el.addEventListener('change', refreshBulk);
    });
    refreshBulk();

    if (bulkForm instanceof HTMLFormElement) {
        bulkForm.addEventListener('submit', (event) => {
            const ids = selects
                .filter((el) => el instanceof HTMLInputElement && el.checked)
                .map((el) => Number(el.value))
                .filter((id) => Number.isFinite(id) && id > 0);

            if (ids.length === 0) {
                event.preventDefault();
                return;
            }

            if (!window.confirm(`Delete ${ids.length} selected slideshow image(s)? This will remove all variants.`)) {
                event.preventDefault();
                return;
            }

            Array.from(bulkForm.querySelectorAll('input[name="ids[]"]')).forEach((n) => n.remove());
            ids.forEach((id) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'ids[]';
                hidden.value = String(id);
                bulkForm.appendChild(hidden);
            });
        });
    }

    const closePreview = () => {
        if (!(modal instanceof HTMLElement)) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (modalImg instanceof HTMLImageElement) {
            modalImg.src = '';
        }
    };

    const openPreview = (src) => {
        if (!(modal instanceof HTMLElement) || !(modalImg instanceof HTMLImageElement)) return;
        modalImg.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    previewButtons.forEach((btn) => {
        if (!(btn instanceof HTMLElement)) return;
        btn.addEventListener('click', () => {
            const src = btn.getAttribute('data-preview-src') || '';
            if (src) openPreview(src);
        });
    });

    modalCloses.forEach((btn) => {
        if (!(btn instanceof HTMLElement)) return;
        btn.addEventListener('click', closePreview);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        closePreview();
    });
}

function initAdminPhotoReorder() {
    const roots = document.querySelectorAll('[data-photo-sort]');
    if (roots.length === 0) return;

    const csrf = getCsrfToken();

    const setStatus = (root, message, kind) => {
        const status = root.parentElement?.querySelector('[data-photo-reorder-status]');
        if (!status) return;
        status.textContent = message || '';
        status.className =
            kind === 'error'
                ? 'text-sm text-red-700'
                : kind === 'success'
                  ? 'text-sm text-emerald-700'
                  : 'text-sm text-slate-700';
    };

    const saveOrder = async (root) => {
        const url = root.getAttribute('data-reorder-url');
        if (!url) return;

        const ids = Array.from(root.querySelectorAll('[data-photo-id]'))
            .map((el) => el.getAttribute('data-photo-id'))
            .filter(Boolean)
            .map((id) => Number(id));

        setStatus(root, 'Saving order…', 'info');

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            credentials: 'same-origin',
            body: JSON.stringify({ photo_ids: ids }),
        });

        if (!response.ok) {
            const json = await response.json().catch(() => null);
            const message = json?.message || 'Failed to save order.';
            setStatus(root, message, 'error');
            return;
        }

        setStatus(root, 'Order saved.', 'success');
        window.setTimeout(() => setStatus(root, '', 'info'), 2000);
    };

    roots.forEach((root) => {
        let dragging = null;
        let saveTimer = null;

        const scheduleSave = () => {
            if (saveTimer) window.clearTimeout(saveTimer);
            saveTimer = window.setTimeout(() => saveOrder(root), 250);
        };

        root.addEventListener('dragstart', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            const item = target.closest('[data-photo-id]');
            if (!(item instanceof HTMLElement)) return;
            dragging = item;
            event.dataTransfer?.setData('text/plain', item.getAttribute('data-photo-id') || '');
            event.dataTransfer && (event.dataTransfer.effectAllowed = 'move');
            item.classList.add('ring-2', 'ring-[var(--color-primary)]');
        });

        root.addEventListener('dragend', () => {
            if (dragging) dragging.classList.remove('ring-2', 'ring-[var(--color-primary)]');
            dragging = null;
        });

        root.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (!dragging) return;

            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            const over = target.closest('[data-photo-id]');
            if (!(over instanceof HTMLElement)) return;
            if (over === dragging) return;

            const rect = over.getBoundingClientRect();
            const before = event.clientY < rect.top + rect.height / 2;
            root.insertBefore(dragging, before ? over : over.nextSibling);
        });

        root.addEventListener('drop', (event) => {
            event.preventDefault();
            if (!dragging) return;
            scheduleSave();
        });
    });
}

function initLightbox() {
    const triggers = document.querySelectorAll('[data-lightbox-item]');
    if (triggers.length === 0) return;

    let overlay = null;
    let img = null;
    let closeBtn = null;

    const ensure = () => {
        if (overlay) return;

        overlay = document.createElement('div');
        overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4';
        overlay.hidden = true;
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');

        const panel = document.createElement('div');
        panel.className = 'relative w-full max-w-5xl';

        closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className =
            'absolute right-0 top-0 inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl bg-white/90 text-slate-900 ring-1 ring-[var(--color-border)] hover:bg-white';
        closeBtn.setAttribute('aria-label', 'Close');
        closeBtn.textContent = '×';

        img = document.createElement('img');
        img.className = 'w-full max-h-[80vh] rounded-2xl object-contain bg-black';
        img.alt = '';

        panel.appendChild(closeBtn);
        panel.appendChild(img);
        overlay.appendChild(panel);
        document.body.appendChild(overlay);

        const close = () => {
            if (!overlay) return;
            overlay.hidden = true;
            if (img) img.src = '';
        };

        closeBtn.addEventListener('click', close);
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) close();
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && overlay && !overlay.hidden) close();
        });
    };

    const open = (src, alt) => {
        ensure();
        if (!overlay || !img) return;
        img.src = src;
        img.alt = alt || '';
        overlay.hidden = false;
        closeBtn && closeBtn.focus();
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            if (!(trigger instanceof HTMLElement)) return;
            const src = trigger.getAttribute('data-full') || trigger.getAttribute('href');
            if (!src) return;
            event.preventDefault();
            const alt = trigger.getAttribute('data-alt') || '';
            open(src, alt);
        });
    });
}

function initGalleryCarousel() {
    const root = document.querySelector('[data-gallery-carousel]');
    if (!(root instanceof HTMLElement)) return;

    const raw = root.getAttribute('data-gallery-photos') || '[]';
    let photos = [];
    try {
        photos = JSON.parse(raw);
    } catch {
        photos = [];
    }

    if (!Array.isArray(photos) || photos.length === 0) return;

    const title = root.getAttribute('data-gallery-title') || 'Gallery';

    let overlay = null;
    let panel = null;
    let img = null;
    let spinner = null;
    let counter = null;
    let errorEl = null;
    let closeBtn = null;
    let prevBtn = null;
    let nextBtn = null;

    let index = 0;
    let lastFocused = null;
    let touchStartX = 0;
    let touchStartY = 0;
    let isSwiping = false;

    const ensure = () => {
        if (overlay) return;

        overlay = document.createElement('div');
        overlay.className = 'gallery-carousel';
        overlay.hidden = true;
        overlay.dataset.state = 'closed';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-label', title);

        const backdrop = document.createElement('div');
        backdrop.className = 'absolute inset-0';
        backdrop.setAttribute('data-gallery-close', '');

        panel = document.createElement('div');
        panel.className = 'gallery-carousel-panel';
        panel.tabIndex = -1;

        const topBar = document.createElement('div');
        topBar.className =
            'absolute left-0 right-0 top-0 z-10 flex items-center justify-between gap-3 bg-black/25 px-4 py-3 backdrop-blur';

        counter = document.createElement('div');
        counter.className = 'text-sm font-semibold text-white';

        closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className =
            'inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl bg-white/90 text-lg font-semibold text-slate-900 ring-1 ring-[var(--color-border)] hover:bg-white';
        closeBtn.setAttribute('aria-label', 'Close');
        closeBtn.setAttribute('data-gallery-close', '');
        closeBtn.textContent = '×';

        topBar.appendChild(counter);
        topBar.appendChild(closeBtn);

        img = document.createElement('img');
        img.className = 'gallery-carousel-img';
        img.alt = '';
        img.decoding = 'async';
        img.loading = 'eager';

        const center = document.createElement('div');
        center.className = 'absolute inset-0 flex items-center justify-center';

        spinner = document.createElement('div');
        spinner.className = 'gallery-carousel-spinner';

        errorEl = document.createElement('div');
        errorEl.className = 'hidden rounded-2xl bg-white/95 px-4 py-3 text-sm font-semibold text-slate-900';

        center.appendChild(spinner);
        center.appendChild(errorEl);

        const nav = document.createElement('div');
        nav.className = 'absolute inset-y-0 left-0 right-0 z-10 flex items-center justify-between px-3';

        prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className =
            'inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl bg-white/90 text-slate-900 ring-1 ring-[var(--color-border)] hover:bg-white';
        prevBtn.setAttribute('aria-label', 'Previous photo');
        prevBtn.innerHTML =
            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M12.5 4 7.5 10l5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className =
            'inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl bg-white/90 text-slate-900 ring-1 ring-[var(--color-border)] hover:bg-white';
        nextBtn.setAttribute('aria-label', 'Next photo');
        nextBtn.innerHTML =
            '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M7.5 4 12.5 10l-5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        nav.appendChild(prevBtn);
        nav.appendChild(nextBtn);

        panel.appendChild(topBar);
        panel.appendChild(img);
        panel.appendChild(center);
        panel.appendChild(nav);

        overlay.appendChild(backdrop);
        overlay.appendChild(panel);
        document.body.appendChild(overlay);

        overlay.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;
            if (target.closest('[data-gallery-close]')) {
                close();
            }
        });

        prevBtn.addEventListener('click', () => go(-1));
        nextBtn.addEventListener('click', () => go(1));

        overlay.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                event.preventDefault();
                close();
                return;
            }
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                go(-1);
                return;
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                go(1);
                return;
            }

            if (event.key !== 'Tab') return;

            const focusableSelectors = [
                'button:not([disabled])',
                'a[href]',
                '[tabindex]:not([tabindex="-1"])',
            ];
            const focusables = Array.from(overlay.querySelectorAll(focusableSelectors.join(','))).filter(
                (el) => el instanceof HTMLElement && !el.hasAttribute('disabled'),
            );
            if (focusables.length === 0) return;
            const first = focusables[0];
            const last = focusables[focusables.length - 1];
            const active = document.activeElement;

            if (event.shiftKey) {
                if (active === first || active === overlay) {
                    event.preventDefault();
                    last.focus();
                }
            } else {
                if (active === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });

        overlay.addEventListener(
            'touchstart',
            (event) => {
                const t = event.touches?.[0];
                if (!t) return;
                touchStartX = t.clientX;
                touchStartY = t.clientY;
                isSwiping = true;
            },
            { passive: true },
        );

        overlay.addEventListener(
            'touchmove',
            (event) => {
                if (!isSwiping) return;
                const t = event.touches?.[0];
                if (!t) return;
                const dx = t.clientX - touchStartX;
                const dy = t.clientY - touchStartY;
                if (Math.abs(dy) > Math.abs(dx)) {
                    isSwiping = false;
                }
            },
            { passive: true },
        );

        overlay.addEventListener(
            'touchend',
            (event) => {
                if (!isSwiping) return;
                const t = event.changedTouches?.[0];
                if (!t) return;
                const dx = t.clientX - touchStartX;
                if (Math.abs(dx) > 55) {
                    go(dx > 0 ? -1 : 1);
                }
                isSwiping = false;
            },
            { passive: true },
        );
    };

    const setLoading = (loading) => {
        if (!(spinner instanceof HTMLElement)) return;
        spinner.style.display = loading ? 'block' : 'none';
    };

    const setError = (message) => {
        if (!(errorEl instanceof HTMLElement)) return;
        if (!message) {
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
            return;
        }
        errorEl.textContent = message;
        errorEl.classList.remove('hidden');
    };

    const updateCounter = () => {
        if (!(counter instanceof HTMLElement)) return;
        counter.textContent = `${index + 1} of ${photos.length}`;
    };

    const preload = (idx) => {
        const p = photos[idx];
        if (!p || !p.full) return;
        const pre = new Image();
        pre.decoding = 'async';
        pre.src = p.full;
    };

    const render = (nextIndex) => {
        if (!(img instanceof HTMLImageElement)) return;

        index = (nextIndex + photos.length) % photos.length;
        updateCounter();

        const p = photos[index] || {};

        img.dataset.visible = '0';
        setLoading(true);
        setError('');

        const showThumb = (src) => {
            if (!src) return;
            img.src = src;
            requestAnimationFrame(() => {
                img.dataset.visible = '1';
            });
        };

        const thumb = typeof p.thumb === 'string' ? p.thumb : '';
        const full = typeof p.full === 'string' ? p.full : '';

        showThumb(thumb || full);

        if (!full) {
            setLoading(false);
            setError('Image unavailable.');
            return;
        }

        const loader = new Image();
        loader.decoding = 'async';
        loader.onload = () => {
            img.src = full;
            requestAnimationFrame(() => {
                img.dataset.visible = '1';
            });
            setLoading(false);
            setError('');
            preload((index + 1) % photos.length);
            preload((index - 1 + photos.length) % photos.length);
        };
        loader.onerror = () => {
            setLoading(false);
            setError('Failed to load image.');
        };
        loader.src = full;
    };

    const open = (startIndex, trigger) => {
        ensure();
        if (!overlay || !panel) return;

        lastFocused = trigger || document.activeElement;
        overlay.hidden = false;
        overlay.dataset.state = 'open';
        render(startIndex);

        window.setTimeout(() => {
            if (closeBtn instanceof HTMLElement) {
                closeBtn.focus();
            } else {
                panel.focus();
            }
        }, 0);
    };

    const close = () => {
        if (!overlay) return;
        overlay.dataset.state = 'closed';
        window.setTimeout(() => {
            overlay.hidden = true;
            if (img instanceof HTMLImageElement) {
                img.src = '';
                img.dataset.visible = '0';
            }
            setError('');
            setLoading(false);
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }, 160);
    };

    const go = (delta) => {
        render(index + delta);
    };

    root.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        const btn = target.closest('[data-gallery-open]');
        if (!btn) return;
        const i = Number(btn.getAttribute('data-index') || 0);
        open(Number.isFinite(i) ? i : 0, btn);
    });
}

function initPerformanceModal() {
    const modal = document.querySelector('[data-performance-modal]');
    if (!(modal instanceof HTMLElement)) return;

    const panel = modal.querySelector('.performance-modal-panel');
    if (!(panel instanceof HTMLElement)) return;

    const titleEl = modal.querySelector('#performance-modal-title');
    const metaEl = modal.querySelector('[data-performance-modal-meta]');
    const descriptionEl = modal.querySelector('[data-performance-modal-description]');
    const iframe = modal.querySelector('[data-performance-modal-iframe]');
    const loadingEl = modal.querySelector('[data-performance-modal-loading]');
    const errorEl = modal.querySelector('[data-performance-modal-error]');

    let lastFocused = null;
    let currentTrigger = null;
    let loadTimer = null;

    const setLoading = (loading) => {
        if (loadingEl instanceof HTMLElement) {
            loadingEl.classList.toggle('hidden', !loading);
            loadingEl.classList.toggle('flex', loading);
        }
    };

    const setError = (message) => {
        if (!(errorEl instanceof HTMLElement)) return;
        if (!message) {
            errorEl.classList.add('hidden');
            errorEl.classList.remove('flex');
            return;
        }
        errorEl.classList.remove('hidden');
        errorEl.classList.add('flex');
        const box = errorEl.querySelector('div');
        if (box instanceof HTMLElement) {
            box.textContent = message;
        }
    };

    const closeModal = () => {
        modal.dataset.state = 'closed';

        if (currentTrigger instanceof HTMLElement) {
            currentTrigger.setAttribute('aria-expanded', 'false');
        }

        if (loadTimer) {
            window.clearTimeout(loadTimer);
            loadTimer = null;
        }

        if (iframe instanceof HTMLIFrameElement) {
            iframe.src = '';
        }

        window.setTimeout(() => {
            modal.hidden = true;
            setLoading(false);
            setError('');
            if (lastFocused && typeof lastFocused.focus === 'function') {
                lastFocused.focus();
            }
        }, 200);
    };

    const openModal = (data, trigger) => {
        lastFocused = trigger || document.activeElement;
        currentTrigger = trigger || null;

        if (currentTrigger instanceof HTMLElement) {
            currentTrigger.setAttribute('aria-expanded', 'true');
        }

        const title = String(data?.title || '');
        const description = String(data?.description || '');
        const createdAt = String(data?.created_at || '');
        const youtubeUrl = String(data?.youtube_url || '');
        const embedUrl = String(data?.youtube_embed_url || '');

        if (titleEl instanceof HTMLElement) titleEl.textContent = title || 'Performance';
        if (descriptionEl instanceof HTMLElement) descriptionEl.textContent = description || '—';
        if (metaEl instanceof HTMLElement) {
            metaEl.textContent = createdAt ? `Posted: ${createdAt}` : '';
        }

        modal.hidden = false;
        modal.dataset.state = 'open';

        setError('');
        setLoading(true);

        if (!(iframe instanceof HTMLIFrameElement)) {
            setLoading(false);
            setError('Video player unavailable.');
        } else if (!embedUrl) {
            setLoading(false);
            setError(youtubeUrl ? 'Invalid YouTube URL.' : 'Missing YouTube URL.');
        } else {
            const autoplayUrl = embedUrl.includes('?')
                ? `${embedUrl}&autoplay=1&mute=1&playsinline=1&rel=0`
                : `${embedUrl}?autoplay=1&mute=1&playsinline=1&rel=0`;

            const onLoad = () => {
                iframe.removeEventListener('load', onLoad);
                if (loadTimer) window.clearTimeout(loadTimer);
                setLoading(false);
                setError('');
            };

            iframe.addEventListener('load', onLoad);
            iframe.src = autoplayUrl;

            loadTimer = window.setTimeout(() => {
                setLoading(false);
                setError('Video failed to load.');
            }, 6000);
        }

        window.setTimeout(() => {
            const closeBtn = modal.querySelector('[data-performance-close][aria-label="Close dialog"]');
            if (closeBtn instanceof HTMLElement) {
                closeBtn.focus();
            } else {
                panel.focus();
            }
        }, 0);
    };

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;

        const openBtn = target.closest('[data-performance-open]');
        if (openBtn) {
            const json = openBtn.getAttribute('data-performance') || '{}';
            let data = {};
            try {
                data = JSON.parse(json);
            } catch {
                data = {};
            }
            openModal(data, openBtn);
            return;
        }

        if (!modal.hidden && target.closest('[data-performance-close]')) {
            closeModal();
        }
    });

    modal.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        if (event.key !== 'Tab') return;

        const focusableSelectors = [
            'a[href]',
            'button:not([disabled])',
            '[tabindex]:not([tabindex="-1"])',
        ];

        const focusables = Array.from(modal.querySelectorAll(focusableSelectors.join(','))).filter(
            (el) => el instanceof HTMLElement && !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden'),
        );

        if (focusables.length === 0) return;

        const first = focusables[0];
        const last = focusables[focusables.length - 1];
        const active = document.activeElement;

        if (event.shiftKey) {
            if (active === first || active === modal) {
                event.preventDefault();
                last.focus();
            }
        } else {
            if (active === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });
}

function initPublicListings() {
    const roots = document.querySelectorAll('[data-live-listing]');
    if (roots.length === 0) return;

    const debounce = (fn, waitMs) => {
        let t = null;
        return (...args) => {
            if (t) window.clearTimeout(t);
            t = window.setTimeout(() => fn(...args), waitMs);
        };
    };

    roots.forEach((root) => {
        const form = root.querySelector('[data-live-form]');
        const results = root.querySelector('[data-live-results]');
        const loadingEl = root.querySelector('[data-live-loading]');
        const errorEl = root.querySelector('[data-live-error]');

        if (!(form instanceof HTMLFormElement) || !(results instanceof HTMLElement)) return;

        const qInput = form.querySelector('input[name="q"]');
        const perPageSelect = form.querySelector('select[name="per_page"]');

        const setLoading = (isLoading) => {
            results.setAttribute('aria-busy', isLoading ? 'true' : 'false');
            if (loadingEl instanceof HTMLElement) loadingEl.hidden = !isLoading;
        };

        const setError = (message) => {
            if (!(errorEl instanceof HTMLElement)) return;
            const msg = String(message || '');
            errorEl.textContent = msg;
            errorEl.hidden = msg === '';
        };

        const fetchAndRender = async (url, pushState = true) => {
            setError('');
            setLoading(true);

            try {
                const response = await fetch(url, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error(`Request failed (${response.status})`);
                }

                const data = await response.json();
                if (!data || typeof data.html !== 'string') {
                    throw new Error('Unexpected server response.');
                }

                results.innerHTML = data.html;

                if (pushState) {
                    window.history.pushState({}, '', url);
                }

                if (typeof initEventCards === 'function') {
                    initEventCards();
                }
                if (typeof initEventSlideshow === 'function') {
                    initEventSlideshow();
                }
                initMusicSheetCardPdfPreviews(results);
            } catch (e) {
                setError('Failed to load results. Please try again.');
            } finally {
                setLoading(false);
            }
        };

        const buildUrl = (overrides = {}) => {
            const base = new URL(form.action || window.location.href, window.location.origin);
            const params = new URLSearchParams(window.location.search);

            const q = qInput instanceof HTMLInputElement ? qInput.value : params.get('q') || '';
            const perPage =
                perPageSelect instanceof HTMLSelectElement ? perPageSelect.value : params.get('per_page') || '12';

            if (String(q).trim() === '') {
                params.delete('q');
            } else {
                params.set('q', q);
            }
            params.set('per_page', perPage);

            Object.entries(overrides).forEach(([k, v]) => {
                if (v === null || v === undefined || v === '') {
                    params.delete(k);
                } else {
                    params.set(k, String(v));
                }
            });

            if (!overrides.hasOwnProperty('page')) {
                if (!params.get('page')) params.set('page', '1');
            }

            base.search = params.toString();
            return base.toString();
        };

        const doSearch = debounce(() => {
            const url = buildUrl({ page: 1 });
            fetchAndRender(url);
        }, 300);

        if (qInput instanceof HTMLInputElement) {
            qInput.addEventListener('input', () => doSearch());
        }

        if (perPageSelect instanceof HTMLSelectElement) {
            perPageSelect.addEventListener('change', () => {
                const url = buildUrl({ page: 1 });
                fetchAndRender(url);
            });
        }

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const url = buildUrl({ page: 1 });
            fetchAndRender(url);
        });

        results.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLElement)) return;

            const link = target.closest('a[href]');
            if (!(link instanceof HTMLAnchorElement)) return;

            const href = link.getAttribute('href') || '';
            if (!href) return;

            const url = new URL(href, window.location.origin);
            if (url.pathname !== window.location.pathname) return;

            event.preventDefault();
            fetchAndRender(url.toString());
        });

        window.addEventListener('popstate', () => {
            const url = window.location.href;
            fetchAndRender(url, false);
        });

        if (loadingEl instanceof HTMLElement) {
            loadingEl.hidden = true;
        }
        if (errorEl instanceof HTMLElement) {
            errorEl.hidden = true;
        }
        results.setAttribute('aria-busy', 'false');
    });
}

let musicSheetPdfJsPromise = null;
let musicSheetPdfPreviewObserver = null;

async function loadMusicSheetPdfJs() {
    if (musicSheetPdfJsPromise) return musicSheetPdfJsPromise;

    musicSheetPdfJsPromise = import('pdfjs-dist/build/pdf.mjs').then((pdfjs) => {
        try {
            pdfjs.GlobalWorkerOptions.workerSrc = new URL(
                'pdfjs-dist/build/pdf.worker.mjs',
                import.meta.url
            ).toString();
        } catch {
        }

        return pdfjs;
    });

    return musicSheetPdfJsPromise;
}

async function renderMusicSheetPdfCardCanvas(canvas) {
    if (!(canvas instanceof HTMLCanvasElement)) return;
    if (canvas.dataset.rendered === '1') return;

    const url = canvas.dataset.pdfUrl || '';
    if (!url) return;

    const container = canvas.parentElement;
    if (!(container instanceof HTMLElement)) return;

    const fallback = container.querySelector('[data-music-sheet-card-pdf-fallback]');

    canvas.dataset.rendered = '1';
    try {
        const pdfjs = await loadMusicSheetPdfJs();
        const task = pdfjs.getDocument({ url, withCredentials: true });
        const pdf = await task.promise;
        const requestedPages = Number.parseInt(String(canvas.dataset.pdfPages || '1'), 10);
        const pagesToRender = Math.max(1, Math.min(3, Number.isFinite(requestedPages) ? requestedPages : 1));
        const totalPages = Math.max(1, pdf.numPages || 1);
        const actualPages = Math.min(pagesToRender, totalPages);

        const pages = [];
        for (let i = 1; i <= actualPages; i += 1) {
            pages.push(await pdf.getPage(i));
        }

        const baseViewports = pages.map((p) => p.getViewport({ scale: 1 }));
        const baseWidth = Math.max(...baseViewports.map((v) => v.width));
        const baseHeight = baseViewports.reduce((sum, v) => sum + v.height, 0);

        const box = container.getBoundingClientRect();
        const targetW = Math.max(1, Math.floor(box.width));
        const targetH = Math.max(1, Math.floor(box.height));
        const scale = Math.min(targetW / baseWidth, targetH / baseHeight);
        const viewports = pages.map((p) => p.getViewport({ scale }));
        const renderWidth = baseWidth * scale;
        const renderHeight = baseHeight * scale;

        const dpr = Math.min(2, window.devicePixelRatio || 1);
        canvas.width = Math.floor(renderWidth * dpr);
        canvas.height = Math.floor(renderHeight * dpr);
        canvas.style.width = `${Math.floor(renderWidth)}px`;
        canvas.style.height = `${Math.floor(renderHeight)}px`;

        const ctx = canvas.getContext('2d', { alpha: false });
        if (!ctx) throw new Error('Missing canvas context.');

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, renderWidth, renderHeight);

        let y = 0;
        for (let i = 0; i < pages.length; i += 1) {
            const page = pages[i];
            const viewport = viewports[i];
            ctx.save();
            ctx.translate(0, y);
            await page.render({ canvasContext: ctx, viewport }).promise;
            ctx.restore();
            y += viewport.height;
        }

        if (fallback instanceof HTMLElement) {
            fallback.hidden = true;
        }
    } catch {
        if (fallback instanceof HTMLElement) {
            fallback.hidden = false;
            fallback.textContent = 'PDF';
        }
    }
}

function initMusicSheetCardPdfPreviews(root = document) {
    const scope = root instanceof HTMLElement || root instanceof Document ? root : document;
    const canvases = Array.from(scope.querySelectorAll('[data-music-sheet-card-pdf]'));
    if (canvases.length === 0) return;

    const observe = (canvas) => {
        if (!(canvas instanceof HTMLCanvasElement)) return;
        if (canvas.dataset.observed === '1') return;
        canvas.dataset.observed = '1';

        if (!('IntersectionObserver' in window)) {
            renderMusicSheetPdfCardCanvas(canvas);
            return;
        }

        if (!musicSheetPdfPreviewObserver) {
            musicSheetPdfPreviewObserver = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        const el = entry.target;
                        if (!(el instanceof HTMLCanvasElement)) return;
                        if (!entry.isIntersecting) return;
                        musicSheetPdfPreviewObserver?.unobserve(el);
                        renderMusicSheetPdfCardCanvas(el);
                    });
                },
                { rootMargin: '200px 0px' }
            );
        }

        musicSheetPdfPreviewObserver.observe(canvas);
    };

    canvases.forEach(observe);
}

function initMusicSheetsPreview() {
    const gallery = document.querySelector('[data-music-sheets-gallery]');
    if (!(gallery instanceof HTMLElement)) return;

    const modal = document.querySelector('[data-music-sheet-modal]');
    if (!(modal instanceof HTMLElement)) return;

    const panel = modal.querySelector('.performance-modal-panel');
    const titleEl = modal.querySelector('#music-sheet-modal-title');
    const metaEl = modal.querySelector('[data-music-sheet-modal-meta]');
    const viewCountEl = modal.querySelector('[data-music-sheet-modal-view-count]');
    const downloadCountEl = modal.querySelector('[data-music-sheet-modal-download-count]');

    const loadingEl = modal.querySelector('[data-music-sheet-loading]');
    const loadingHintEl = modal.querySelector('[data-music-sheet-loading-hint]');
    const errorEl = modal.querySelector('[data-music-sheet-error]');

    const pdfEl = modal.querySelector('[data-music-sheet-pdf]');
    const imgEl = modal.querySelector('[data-music-sheet-image]');

    const prevBtn = modal.querySelector('[data-music-sheet-prev]');
    const nextBtn = modal.querySelector('[data-music-sheet-next]');
    const downloadLink = modal.querySelector('[data-music-sheet-download]');
    const fallbackLink = modal.querySelector('[data-music-sheet-fallback]');

    if (!(panel instanceof HTMLElement)) return;

    let items = [];
    let index = -1;
    let lastFocused = null;
    let hintTimer = null;
    let previewFallbackTimer = null;

    const csrf = getCsrfToken();

    const setModalOpen = (open) => {
        modal.dataset.state = open ? 'open' : 'closed';
        modal.hidden = !open;
        if (open) {
            window.setTimeout(() => {
                panel.focus();
            }, 0);
        }
    };

    const setButtonDisabled = (btn, disabled) => {
        if (!(btn instanceof HTMLButtonElement)) return;
        btn.disabled = !!disabled;
        btn.setAttribute('aria-disabled', disabled ? 'true' : 'false');
        btn.tabIndex = disabled ? -1 : 0;
    };

    const setLoading = (loading) => {
        if (hintTimer) window.clearTimeout(hintTimer);
        hintTimer = null;
        if (previewFallbackTimer) window.clearTimeout(previewFallbackTimer);
        previewFallbackTimer = null;

        if (loadingEl instanceof HTMLElement) loadingEl.hidden = !loading;
        if (errorEl instanceof HTMLElement) errorEl.hidden = true;
        if (loadingHintEl instanceof HTMLElement) loadingHintEl.hidden = true;

        if (loading) {
            hintTimer = window.setTimeout(() => {
                if (loadingHintEl instanceof HTMLElement) loadingHintEl.hidden = false;
            }, 2000);
        }
    };

    const setError = () => {
        if (hintTimer) window.clearTimeout(hintTimer);
        hintTimer = null;
        if (previewFallbackTimer) window.clearTimeout(previewFallbackTimer);
        previewFallbackTimer = null;
        if (loadingEl instanceof HTMLElement) loadingEl.hidden = true;
        if (errorEl instanceof HTMLElement) errorEl.hidden = false;
        if (loadingHintEl instanceof HTMLElement) loadingHintEl.hidden = true;
    };

    const hidePreview = () => {
        if (previewFallbackTimer) window.clearTimeout(previewFallbackTimer);
        previewFallbackTimer = null;
        if (pdfEl instanceof HTMLIFrameElement) {
            pdfEl.hidden = true;
            pdfEl.onload = null;
            pdfEl.src = '';
        }
        if (imgEl instanceof HTMLImageElement) {
            imgEl.hidden = true;
            imgEl.onload = null;
            imgEl.onerror = null;
            imgEl.src = '';
        }
    };

    const syncCounters = (sheetId, viewCount, downloadCount) => {
        const viewTargets = document.querySelectorAll(`[data-music-sheet-view-count="${sheetId}"]`);
        viewTargets.forEach((el) => {
            if (el instanceof HTMLElement) el.textContent = String(viewCount);
        });
        const downloadTargets = document.querySelectorAll(`[data-music-sheet-download-count="${sheetId}"]`);
        downloadTargets.forEach((el) => {
            if (el instanceof HTMLElement) el.textContent = String(downloadCount);
        });

        if (viewCountEl instanceof HTMLElement) viewCountEl.textContent = String(viewCount);
        if (downloadCountEl instanceof HTMLElement) downloadCountEl.textContent = String(downloadCount);
    };

    const postJson = async (url) => {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Request failed.');
        }

        return response.json();
    };

    const postCountNonBlocking = (url) => {
        const endpoint = String(url || '');
        if (!endpoint) return;

        if (csrf && typeof navigator !== 'undefined' && typeof navigator.sendBeacon === 'function') {
            try {
                const data = new FormData();
                data.append('_token', csrf);
                navigator.sendBeacon(endpoint, data);
                return;
            } catch {
            }
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            keepalive: true,
        }).catch(() => {});
    };

    const loadItems = () => {
        items = Array.from(document.querySelectorAll('[data-music-sheet-open]'))
            .map((btn) => {
                if (!(btn instanceof HTMLElement)) return null;
                const raw = btn.getAttribute('data-music-sheet') || '{}';
                try {
                    const parsed = JSON.parse(raw);
                    if (!parsed || typeof parsed !== 'object') return null;
                    return parsed;
                } catch {
                    return null;
                }
            })
            .filter(Boolean);
    };

    const syncNavButtons = () => {
        const prevIdx = musicSheetCarouselBoundedNextIndex(index, -1, items.length);
        const nextIdx = musicSheetCarouselBoundedNextIndex(index, 1, items.length);
        setButtonDisabled(prevBtn, prevIdx < 0);
        setButtonDisabled(nextBtn, nextIdx < 0);
    };

    const openAt = async (nextIndex) => {
        if (!Number.isFinite(nextIndex)) return;
        if (nextIndex < 0 || nextIndex >= items.length) return;
        index = nextIndex;
        const item = items[index];
        if (!item) return;

        const sheetId = item.id;

        if (titleEl instanceof HTMLElement) titleEl.textContent = String(item.title || 'Music Sheet');
        if (metaEl instanceof HTMLElement) metaEl.textContent = String(item.composer || '');

        const vc = Number(item.view_count || 0);
        const dc = Number(item.download_count || 0);
        syncCounters(sheetId, Number.isFinite(vc) ? vc : 0, Number.isFinite(dc) ? dc : 0);

        if (downloadLink instanceof HTMLAnchorElement) {
            downloadLink.href = String(item.download_url || '#');
        }
        if (fallbackLink instanceof HTMLAnchorElement) {
            fallbackLink.href = String(item.file_url || '#');
        }

        syncNavButtons();

        setLoading(true);
        hidePreview();

        const fileUrl = String(item.file_url || '');
        if (!fileUrl) {
            setError();
            return;
        }

        if (item.is_pdf) {
            if (pdfEl instanceof HTMLIFrameElement) {
                pdfEl.hidden = false;
                pdfEl.onload = () => {
                    setLoading(false);
                };
                pdfEl.src = fileUrl;
                previewFallbackTimer = window.setTimeout(() => {
                    if (modal.hidden) return;
                    if (pdfEl.src === fileUrl) {
                        setLoading(false);
                    }
                }, 1200);
            } else {
                setError();
            }
        } else if (item.is_image) {
            if (imgEl instanceof HTMLImageElement) {
                imgEl.hidden = false;
                imgEl.alt = String(item.title || '');
                imgEl.onload = () => setLoading(false);
                imgEl.onerror = () => setError();
                imgEl.src = fileUrl;
            } else {
                setError();
            }
        } else {
            setError();
        }

        postJson(String(item.track_view_url || ''))
            .then((json) => {
                if (json && typeof json === 'object') {
                    syncCounters(sheetId, Number(json.view_count || 0), Number(json.download_count || 0));
                    item.view_count = Number(json.view_count || 0);
                    item.download_count = Number(json.download_count || 0);
                }
            })
            .catch(() => {});
    };

    const openFromButton = (btn) => {
        if (!(btn instanceof HTMLElement)) return;
        loadItems();

        const raw = btn.getAttribute('data-music-sheet') || '{}';
        let parsed = null;
        try {
            parsed = JSON.parse(raw);
        } catch {
            parsed = null;
        }
        if (!parsed) return;

        const targetId = parsed.id;
        const startIndex = items.findIndex((it) => it && it.id === targetId);
        if (startIndex < 0) return;

        lastFocused = btn;
        setModalOpen(true);
        openAt(startIndex);
    };

    const close = () => {
        setModalOpen(false);
        hidePreview();
        if (hintTimer) window.clearTimeout(hintTimer);
        hintTimer = null;
        if (lastFocused && typeof lastFocused.focus === 'function') {
            lastFocused.focus();
        }
    };

    document.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;

        const downloadBtn = target.closest('[data-music-sheet-download-trigger]');
        if (downloadBtn instanceof HTMLElement) {
            event.preventDefault();

            const raw = downloadBtn.getAttribute('data-music-sheet') || '{}';
            let parsed = null;
            try {
                parsed = JSON.parse(raw);
            } catch {
                parsed = null;
            }

            if (parsed && typeof parsed === 'object') {
                const sheetId = parsed.id;
                const nextDownloads = Number(parsed.download_count || 0) + 1;
                const nextViews = Number(parsed.view_count || 0);
                syncCounters(sheetId, Number.isFinite(nextViews) ? nextViews : 0, Number.isFinite(nextDownloads) ? nextDownloads : 0);
                parsed.download_count = nextDownloads;
                postCountNonBlocking(String(parsed.download_intent_url || ''));

                const url = String(parsed.download_url || '');
                if (url) window.location.href = url;
                return;
            }

            const href = downloadBtn.getAttribute('href') || '';
            if (href) window.location.href = href;
            return;
        }

        const openBtn = target.closest('[data-music-sheet-open]');
        if (openBtn) {
            event.preventDefault();
            openFromButton(openBtn);
            return;
        }

        if (target.closest('[data-music-sheet-close]') && !modal.hidden) {
            close();
        }
    });

    modal.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            close();
            return;
        }
        if (event.key === 'ArrowLeft') {
            const prevIdx = musicSheetCarouselBoundedNextIndex(index, -1, items.length);
            if (prevIdx >= 0) {
                event.preventDefault();
                openAt(prevIdx);
            }
        }
        if (event.key === 'ArrowRight') {
            const nextIdx = musicSheetCarouselBoundedNextIndex(index, 1, items.length);
            if (nextIdx >= 0) {
                event.preventDefault();
                openAt(nextIdx);
            }
        }
    });

    if (prevBtn instanceof HTMLElement) {
        prevBtn.addEventListener('click', () => {
            const prevIdx = musicSheetCarouselBoundedNextIndex(index, -1, items.length);
            if (prevIdx >= 0) openAt(prevIdx);
        });
    }
    if (nextBtn instanceof HTMLElement) {
        nextBtn.addEventListener('click', () => {
            const nextIdx = musicSheetCarouselBoundedNextIndex(index, 1, items.length);
            if (nextIdx >= 0) openAt(nextIdx);
        });
    }

    if (downloadLink instanceof HTMLAnchorElement) {
        downloadLink.addEventListener('click', async (event) => {
            event.preventDefault();
            const item = items[index];
            if (!item) return;

            try {
                const json = await postJson(String(item.download_intent_url || ''));
                if (json && typeof json === 'object') {
                    syncCounters(item.id, Number(json.view_count || 0), Number(json.download_count || 0));
                    item.view_count = Number(json.view_count || 0);
                    item.download_count = Number(json.download_count || 0);

                    const url = String(json.download_url || item.download_url || '');
                    if (url) {
                        window.location.href = url;
                    } else if (item.download_url) {
                        window.location.href = String(item.download_url);
                    }
                }
            } catch {
                const url = String(item.download_url || '');
                if (url) {
                    window.location.href = url;
                }
            }
        });
    }

    let touchStartX = 0;
    let touchStartY = 0;
    let touchAt = 0;

    const onTouchStart = (event) => {
        const t = event.touches?.[0];
        if (!t) return;
        touchStartX = t.clientX;
        touchStartY = t.clientY;
        touchAt = Date.now();
    };

    const onTouchEnd = (event) => {
        const t = event.changedTouches?.[0];
        if (!t) return;

        const dx = t.clientX - touchStartX;
        const dy = t.clientY - touchStartY;
        const dt = Date.now() - touchAt;

        if (dt > 800) return;
        if (Math.abs(dx) < 40) return;
        if (Math.abs(dy) > 80) return;

        if (dx < 0) {
            const nextIdx = musicSheetCarouselBoundedNextIndex(index, 1, items.length);
            if (nextIdx >= 0) openAt(nextIdx);
        } else {
            const prevIdx = musicSheetCarouselBoundedNextIndex(index, -1, items.length);
            if (prevIdx >= 0) openAt(prevIdx);
        }
    };

    modal.addEventListener('touchstart', onTouchStart, { passive: true });
    modal.addEventListener('touchend', onTouchEnd, { passive: true });
}

if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        initMobileNav();
        initHomeMenuDropdown();
        initFormLoading();
        initMultiSelectLookups();
        initMemberModals();
        initHomeSlideshow();
        initEventCards();
        initEventSlideshow();
        initEventModals();
        initMusicSheetsPreview();
        initMusicSheetCardPdfPreviews();
        initUploadPreviews();
        initAdminSlideshow();
        initAdminPhotoReorder();
        initLightbox();
        initGalleryCarousel();
        initPerformanceModal();
        initPublicListings();
    });
}
