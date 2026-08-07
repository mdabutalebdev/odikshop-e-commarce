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
    const color = type === 'success' ? '#008060' : '#ef4444';
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
                toast('কার্টে যোগ হয়েছে');
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
                toast(data.active ? 'উইশলিস্টে যোগ হয়েছে' : 'উইশলিস্ট থেকে বাদ');
            }
            return;
        }
    });
};

// ---- Hero slider (auto-advance) ----
const initHeroSlider = () => {
    document.querySelectorAll('[data-hero]').forEach((slider) => {
        const track = slider.querySelector('[data-hero-track]');
        if (!track) return;
        const slides = track.children.length;
        if (slides <= 1) return;
        let i = 0;
        const go = (n) => {
            i = (n + slides) % slides;
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
        setInterval(() => go(i + 1), 4500);
    });
};

// ---- Horizontal slider arrows ----
const initRowSliders = () => {
    document.querySelectorAll('[data-row]').forEach((row) => {
        const scroller = row.querySelector('[data-row-scroll]');
        if (!scroller) return;
        const step = () => scroller.clientWidth * 0.8;
        row.querySelector('[data-row-next]')?.addEventListener('click', () =>
            scroller.scrollBy({ left: step(), behavior: 'smooth' })
        );
        row.querySelector('[data-row-prev]')?.addEventListener('click', () =>
            scroller.scrollBy({ left: -step(), behavior: 'smooth' })
        );
    });
};

const init = () => {
    initHeroSlider();
    initRowSliders();
    refreshCart();
};

bind();
document.addEventListener('DOMContentLoaded', init);
document.addEventListener('livewire:navigated', init);
