import './bootstrap';
import '@fortawesome/fontawesome-free/css/all.min.css';

/**
 * Odik Shop — storefront interactions.
 * Cart/Wishlist use fetch() against the session-backed endpoints.
 * UI state (drawers, menus) is driven by Alpine (bundled with Livewire).
 */

// ---- CSRF helper ----
const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

const postJson = async (url, body = {}) => {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf(),
            'X-Requested-With': 'XMLHttpRequest',
            Accept: 'application/json',
        },
        body: JSON.stringify(body),
    });
    return res.json();
};

// ---- Toast ----
const toast = (message, type = 'success') => {
    const el = document.createElement('div');
    const color = type === 'success' ? '#0e9495' : '#ef4444';
    el.style.cssText =
        `position:fixed;bottom:88px;left:50%;transform:translateX(-50%);z-index:9999;` +
        `background:${color};color:#fff;padding:10px 18px;border-radius:10px;font-size:14px;` +
        `box-shadow:0 8px 24px rgba(0,0,0,.18);opacity:0;transition:opacity .2s,transform .2s;`;
    el.textContent = message;
    document.body.appendChild(el);
    requestAnimationFrame(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateX(-50%) translateY(-6px)';
    });
    setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 250);
    }, 1800);
};

// ---- Update cart badge + drawer ----
const refreshCart = async () => {
    try {
        const res = await fetch('/cart/partial', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        document.querySelectorAll('[data-cart-count]').forEach((n) => {
            n.textContent = data.count;
            n.classList.toggle('hidden', data.count === 0);
        });
        const body = document.getElementById('cart-drawer-body');
        if (body) body.innerHTML = data.html;
    } catch (e) {
        /* ignore */
    }
};

const refreshWishlistBadge = (count) => {
    document.querySelectorAll('[data-wishlist-count]').forEach((n) => {
        n.textContent = count;
        n.classList.toggle('hidden', count === 0);
    });
};

// ---- Event delegation (survives Livewire navigation) ----
const bind = () => {
    document.addEventListener('click', async (e) => {
        // Add to cart
        const addBtn = e.target.closest('[data-add-cart]');
        if (addBtn) {
            e.preventDefault();
            const id = addBtn.getAttribute('data-add-cart');
            const qty = parseInt(addBtn.getAttribute('data-qty') || '1', 10);
            const data = await postJson(`/cart/add/${id}`, { quantity: qty });
            if (data.ok) {
                await refreshCart();
                window.dispatchEvent(new CustomEvent('open-cart'));
                toast('Added to cart');
            }
            return;
        }

        // Remove cart item
        const rmBtn = e.target.closest('[data-remove-cart]');
        if (rmBtn) {
            e.preventDefault();
            const key = rmBtn.getAttribute('data-remove-cart');
            const data = await postJson('/cart/remove', { key });
            if (data.ok) await refreshCart();
            return;
        }

        // Wishlist toggle
        const wishBtn = e.target.closest('[data-wishlist]');
        if (wishBtn) {
            e.preventDefault();
            const id = wishBtn.getAttribute('data-wishlist');
            const data = await postJson('/wishlist/toggle', { product_id: id });
            if (data.ok) {
                wishBtn.classList.toggle('is-active', data.active);
                const icon = wishBtn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-solid', data.active);
                    icon.classList.toggle('fa-regular', !data.active);
                    icon.style.color = data.active ? '#ef4444' : '';
                }
                refreshWishlistBadge(data.count);
                toast(data.active ? 'Added to wishlist' : 'Removed from wishlist');
            }
            return;
        }
    });
};

// ---- Drag-to-scroll for horizontal sliders (mouse click-drag / swipe) ----
const enableDragScroll = (el) => {
    if (el.dataset.dragBound) return;
    el.dataset.dragBound = '1';
    let down = false, startX = 0, startScroll = 0, dragged = false;

    el.addEventListener('pointerdown', (e) => {
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        down = true;
        dragged = false;
        startX = e.clientX;
        startScroll = el.scrollLeft;
        el.style.cursor = 'grabbing';
    });
    el.addEventListener('pointermove', (e) => {
        if (!down) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 4) dragged = true;
        el.scrollLeft = startScroll - dx;
    });
    const up = () => {
        down = false;
        el.style.cursor = 'grab';
    };
    el.addEventListener('pointerup', up);
    el.addEventListener('pointerleave', up);
    el.addEventListener('pointercancel', up);
    el.querySelectorAll('img, a').forEach((x) => x.addEventListener('dragstart', (e) => e.preventDefault()));
    // Don't follow a link/click that was actually a drag.
    el.addEventListener('click', (e) => {
        if (dragged) {
            e.preventDefault();
            e.stopPropagation();
        }
    }, true);
    el.style.cursor = 'grab';
};

// ---- Hero slider (auto-advance + drag) ----
const initHeroSlider = () => {
    document.querySelectorAll('[data-hero]').forEach((slider) => {
        if (slider.dataset.bound) return;
        slider.dataset.bound = '1';
        const track = slider.querySelector('[data-hero-track]');
        if (!track) return;
        const slides = track.children.length;
        if (slides <= 1) return;
        let i = 0;

        const go = (n, animate = true) => {
            i = (n + slides) % slides;
            track.style.transition = animate ? 'transform .5s ease' : 'none';
            track.style.transform = `translateX(-${i * 100}%)`;
            slider.querySelectorAll('[data-hero-dot]').forEach((d, idx) =>
                d.classList.toggle('bg-white', idx === i)
            );
        };

        slider.querySelector('[data-hero-next]')?.addEventListener('click', () => go(i + 1));
        slider.querySelector('[data-hero-prev]')?.addEventListener('click', () => go(i - 1));
        slider.querySelectorAll('[data-hero-dot]').forEach((d, idx) =>
            d.addEventListener('click', () => go(idx))
        );

        // Auto-advance (slower), paused on hover + while dragging.
        let timer = null;
        const start = () => { stop(); timer = setInterval(() => go(i + 1), 8000); };
        const stop = () => { if (timer) clearInterval(timer); timer = null; };
        slider.addEventListener('mouseenter', stop);
        slider.addEventListener('mouseleave', start);

        // Pointer drag to slide.
        let down = false, startX = 0, dragged = false, w = 1;
        slider.addEventListener('pointerdown', (e) => {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            down = true;
            dragged = false;
            startX = e.clientX;
            w = slider.clientWidth || 1;
            track.style.transition = 'none';
            stop();
        });
        slider.addEventListener('pointermove', (e) => {
            if (!down) return;
            const dx = e.clientX - startX;
            if (Math.abs(dx) > 4) dragged = true;
            const pct = (dx / w) * 100;
            track.style.transform = `translateX(calc(-${i * 100}% + ${pct}%))`;
        });
        const release = (e) => {
            if (!down) return;
            down = false;
            const dx = (e.clientX || startX) - startX;
            if (dx < -40) go(i + 1);
            else if (dx > 40) go(i - 1);
            else go(i);
            start();
        };
        slider.addEventListener('pointerup', release);
        slider.addEventListener('pointercancel', release);
        slider.querySelectorAll('img, a').forEach((el) => el.addEventListener('dragstart', (e) => e.preventDefault()));
        slider.addEventListener('click', (e) => {
            if (dragged) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        slider.style.cursor = 'grab';
        go(0, false);
        start();
    });
};

// ---- Horizontal slider arrows + drag ----
const initRowSliders = () => {
    document.querySelectorAll('[data-row-scroll]').forEach((scroller) => {
        enableDragScroll(scroller);
        if (scroller.dataset.rowBound) return;
        scroller.dataset.rowBound = '1';
        const row = scroller.closest('[data-row]');
        if (!row) return;
        const step = () => scroller.clientWidth * 0.85;
        row.querySelector('[data-row-next]')?.addEventListener('click', () =>
            scroller.scrollBy({ left: step(), behavior: 'smooth' })
        );
        row.querySelector('[data-row-prev]')?.addEventListener('click', () =>
            scroller.scrollBy({ left: -step(), behavior: 'smooth' })
        );
    });
};

// ---- Live search suggestions (ghorerbazar-style dropdown) ----
const initLiveSearch = () => {
    document.querySelectorAll('[data-search]').forEach((form) => {
        const input = form.querySelector('[data-search-input]');
        const box = form.querySelector('[data-search-results]');
        if (!input || !box || input.dataset.bound) return;
        input.dataset.bound = '1';

        let timer;
        const hide = () => {
            box.classList.add('hidden');
            box.innerHTML = '';
        };

        const render = (results) => {
            if (!results.length) {
                box.innerHTML =
                    '<div style="padding:16px;text-align:center;color:#6b7280;font-size:13px">No products found</div>';
                box.classList.remove('hidden');
                return;
            }
            box.innerHTML = results
                .map(
                    (r) =>
                        `<a href="${r.url}" style="display:flex;gap:12px;align-items:center;padding:10px 14px;border-bottom:1px solid #f0f0f0;text-decoration:none;color:#1f2937">
                            <img src="${r.image}" style="width:44px;height:44px;object-fit:cover;border-radius:8px;flex:none;border:1px solid #eee" loading="lazy">
                            <span style="flex:1;min-width:0;font-size:13px;font-weight:600;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${r.name}</span>
                            <span style="font-weight:800;color:#0e9495;font-size:13px;white-space:nowrap">${r.price}${
                                r.old_price ? ` <s style="color:#9ca3af;font-weight:400;font-size:11px">${r.old_price}</s>` : ''
                            }</span>
                        </a>`
                )
                .join('');
            box.classList.remove('hidden');
        };

        input.addEventListener('input', () => {
            const q = input.value.trim();
            clearTimeout(timer);
            if (q.length < 2) {
                hide();
                return;
            }
            timer = setTimeout(async () => {
                try {
                    const res = await fetch(`/search/suggest?q=${encodeURIComponent(q)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                    });
                    const data = await res.json();
                    render(data.results || []);
                } catch (e) {
                    hide();
                }
            }, 250);
        });

        input.addEventListener('focus', () => {
            if (input.value.trim().length >= 2 && box.innerHTML) box.classList.remove('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!form.contains(e.target)) hide();
        });
    });
};

const init = () => {
    initHeroSlider();
    initRowSliders();
    initLiveSearch();
    refreshCart();
};

bind();
document.addEventListener('DOMContentLoaded', init);
document.addEventListener('livewire:navigated', init);
