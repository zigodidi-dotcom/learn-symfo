import './styles/app.css';

/* ═══════════════════════════════════════════════════════════════════
   SYMFONY 8 CHEATSHEET — App JS
   Modules : Theme · Sidebar · Navigation · Search
   ═══════════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initNavigation();
    initSearch();
});

/* ─────────────────────────────────────────────── THEME (dark/light) */
function initTheme() {
    const btn  = document.getElementById('themeToggle');
    const root = document.documentElement;
    const key  = 'cs-theme';

    const saved = localStorage.getItem(key);
    if (saved) root.dataset.theme = saved;
    else if (window.matchMedia('(prefers-color-scheme: dark)').matches)
        root.dataset.theme = 'dark';

    btn?.addEventListener('click', () => {
        const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
        root.dataset.theme = next;
        localStorage.setItem(key, next);
    });
}

/* ─────────────────────────────────────────────── SIDEBAR */
function initSidebar() {
    const sidebar       = document.getElementById('sidebar');
    const toggleBtn     = document.getElementById('sidebarToggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

    if (!sidebar) return;

    /* Desktop collapse */
    const savedState = localStorage.getItem('cs-sidebar');
    if (savedState === 'collapsed') sidebar.classList.add('collapsed');

    toggleBtn?.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('cs-sidebar', sidebar.classList.contains('collapsed') ? 'collapsed' : 'open');
    });

    /* Mobile open/close */
    mobileMenuBtn?.addEventListener('click', () => sidebar.classList.toggle('mobile-open'));

    /* Fermer sidebar mobile en cliquant à l'extérieur */
    document.addEventListener('click', (e) => {
        if (window.innerWidth <= 900
            && sidebar.classList.contains('mobile-open')
            && !sidebar.contains(e.target)
            && e.target !== mobileMenuBtn) {
            sidebar.classList.remove('mobile-open');
        }
    });
}

/* ─────────────────────────────────────────────── NAVIGATION */
function initNavigation() {
    const navItems   = document.querySelectorAll('.nav-item');
    const viewAll    = document.getElementById('view-all');
    const viewGloss  = document.getElementById('view-glossary');
    const sections   = document.querySelectorAll('.category-section');

    navItems.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;

            /* État actif sidebar */
            navItems.forEach(n => n.classList.remove('active'));
            btn.classList.add('active');

            /* Mobile : fermer sidebar */
            if (window.innerWidth <= 900) {
                document.getElementById('sidebar')?.classList.remove('mobile-open');
            }

            if (filter === 'glossary') {
                viewAll.classList.add('hidden');
                viewGloss.classList.remove('hidden');
                return;
            }

            viewGloss.classList.add('hidden');
            viewAll.classList.remove('hidden');

            if (filter === 'all') {
                sections.forEach(s => s.style.display = '');
                scrollToTop();
                return;
            }

            /* Filtrer par catégorie + scroll */
            sections.forEach(s => {
                s.style.display = s.dataset.category === filter ? '' : 'none';
            });

            const target = document.getElementById('cat-' + filter);
            target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
}

function scrollToTop() {
    document.getElementById('mainContent')?.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ─────────────────────────────────────────────── SEARCH */
function initSearch() {
    const overlay       = document.getElementById('searchOverlay');
    const backdrop      = document.getElementById('searchBackdrop');
    const overlayInput  = document.getElementById('searchOverlayInput');
    const closeBtn      = document.getElementById('searchOverlayClose');
    const resultsEl     = document.getElementById('searchResults');
    const topbarBar     = document.getElementById('searchBar');
    const topbarInput   = document.getElementById('searchInput');

    if (!overlay) return;

    /* Lire les données JSON injectées par Twig */
    let appData = { features: [], terms: [] };
    try {
        appData = JSON.parse(document.getElementById('appData')?.textContent || '{}');
    } catch (e) { console.warn('appData parse error', e); }

    let focusIndex = -1;
    let currentResults = [];
    let debounceTimer;

    /* Ouvrir l'overlay */
    function openSearch() {
        overlay.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => overlayInput?.focus(), 50);
        if (overlayInput?.value) performSearch(overlayInput.value);
    }

    /* Fermer l'overlay */
    function closeSearch() {
        overlay.classList.add('hidden');
        document.body.style.overflow = '';
        focusIndex = -1;
    }

    /* Déclencheurs d'ouverture */
    topbarBar?.addEventListener('click', openSearch);
    topbarInput?.addEventListener('focus', openSearch);
    backdrop?.addEventListener('click', closeSearch);
    closeBtn?.addEventListener('click', closeSearch);

    /* Raccourci clavier ⌘K / Ctrl+K */
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') { e.preventDefault(); openSearch(); }
        if (e.key === 'Escape') closeSearch();
    });

    /* Recherche en temps réel */
    overlayInput?.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const q = e.target.value.trim();
        if (!q) { renderHint(); return; }
        debounceTimer = setTimeout(() => performSearch(q), 180);
    });

    /* Navigation clavier dans les résultats */
    overlayInput?.addEventListener('keydown', (e) => {
        const items = resultsEl?.querySelectorAll('.search-result-item');
        if (!items?.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            focusIndex = Math.min(focusIndex + 1, items.length - 1);
            updateFocus(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            focusIndex = Math.max(focusIndex - 1, 0);
            updateFocus(items);
        } else if (e.key === 'Enter' && focusIndex >= 0) {
            e.preventDefault();
            items[focusIndex]?.click();
        }
    });

    function updateFocus(items) {
        items.forEach((el, i) => el.classList.toggle('focused', i === focusIndex));
        items[focusIndex]?.scrollIntoView({ block: 'nearest' });
    }

    /* ─── Moteur de recherche ─── */
    function performSearch(q) {
        const query = q.toLowerCase().trim();
        focusIndex = -1;

        const featResults = appData.features.filter(f =>
            f.name.toLowerCase().includes(query) ||
            f.description.toLowerCase().includes(query) ||
            f.categoryName.toLowerCase().includes(query) ||
            f.difficulty.includes(query) ||
            f.type.includes(query)
        );

        const termResults = appData.terms.filter(t =>
            t.name.toLowerCase().includes(query) ||
            t.definition.toLowerCase().includes(query)
        );

        currentResults = { features: featResults.slice(0, 12), terms: termResults.slice(0, 6) };
        renderResults(query, currentResults);
    }

    /* ─── Rendu des résultats ─── */
    function renderResults(query, { features, terms }) {
        if (!features.length && !terms.length) {
            resultsEl.innerHTML = `
                <div class="search-no-result">
                    Aucun résultat pour <strong>"${escHtml(query)}"</strong><br>
                    <small style="margin-top:8px;display:block;opacity:.6">Essayez un terme plus court ou en anglais</small>
                </div>`;
            return;
        }

        let html = '';

        if (features.length) {
            html += `<div class="search-section-title">Features (${features.length})</div>`;
            features.forEach(f => {
                const diffColor = { beginner: '#10b981', intermediate: '#f59e0b', advanced: '#ef4444' }[f.difficulty] || '#94a3b8';
                html += `
                <div class="search-result-item" data-type="feature" data-category="${escHtml(f.category)}" data-slug="${escHtml(f.slug)}">
                    <div class="search-result-cat-icon">${escHtml(f.categoryIcon)}</div>
                    <div class="search-result-body">
                        <div class="search-result-name">${highlight(f.name, query)}</div>
                        <div class="search-result-meta">
                            <span style="color:${diffColor};font-weight:600;font-size:11px">${f.difficulty}</span>
                            <span>·</span>
                            <span>${escHtml(f.categoryName)}</span>
                            <span>·</span>
                            <span>${escHtml(f.type)}</span>
                        </div>
                        <div class="search-result-desc">${highlight(f.description, query)}</div>
                    </div>
                    <span class="search-result-arrow">›</span>
                </div>`;
            });
        }

        if (terms.length) {
            html += `<div class="search-section-title" style="margin-top:8px">Glossaire (${terms.length})</div>`;
            terms.forEach(t => {
                html += `
                <div class="search-result-item" data-type="term" data-slug="${escHtml(t.slug)}">
                    <div class="search-result-cat-icon">📖</div>
                    <div class="search-result-body">
                        <div class="search-result-name">${highlight(t.name, query)}</div>
                        <div class="search-result-desc">${highlight(t.definition, query)}</div>
                    </div>
                    <span class="search-result-arrow">›</span>
                </div>`;
            });
        }

        resultsEl.innerHTML = html;

        /* Clics sur les résultats */
        resultsEl.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', () => {
                const type = item.dataset.type;
                if (type === 'feature') {
                    navigateToFeature(item.dataset.category, item.dataset.slug);
                } else {
                    navigateToTerm(item.dataset.slug);
                }
                closeSearch();
            });
        });
    }

    function renderHint() {
        const total = appData.features.length;
        const terms = appData.terms.length;
        resultsEl.innerHTML = `
            <div class="search-hint">
                <span>💡</span> Tapez pour rechercher parmi <strong>${total}</strong> features et <strong>${terms}</strong> termes
                <br><small style="opacity:.6;margin-top:6px;display:block">↑↓ naviguer · Entrée sélectionner · Échap fermer</small>
            </div>`;
    }

    /* ─── Navigation vers une feature ─── */
    function navigateToFeature(categorySlug, featureSlug) {
        /* Activer la catégorie dans la sidebar */
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        const navBtn = document.querySelector(`.nav-item[data-filter="${categorySlug}"]`);
        navBtn?.classList.add('active');

        /* Afficher la section */
        const viewAll = document.getElementById('view-all');
        const viewGloss = document.getElementById('view-glossary');
        viewGloss?.classList.add('hidden');
        viewAll?.classList.remove('hidden');

        document.querySelectorAll('.category-section').forEach(s => {
            s.style.display = s.dataset.category === categorySlug ? '' : 'none';
        });

        /* Scroll + highlight */
        setTimeout(() => {
            const card = document.getElementById('feature-' + featureSlug);
            if (card) {
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                card.classList.add('highlight');
                setTimeout(() => card.classList.remove('highlight'), 2500);
            }
        }, 100);
    }

    /* ─── Navigation vers un terme du glossaire ─── */
    function navigateToTerm(slug) {
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        const glossBtn = document.querySelector('.nav-item[data-filter="glossary"]');
        glossBtn?.classList.add('active');

        document.getElementById('view-all')?.classList.add('hidden');
        document.getElementById('view-glossary')?.classList.remove('hidden');

        setTimeout(() => {
            const card = document.getElementById('term-' + slug);
            card?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }, 100);
    }

    /* ─── Helpers ─── */
    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function highlight(text, query) {
        const escaped = escHtml(text);
        if (!query) return escaped;
        const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g,'\\$&')})`, 'gi');
        return escaped.replace(regex, '<mark style="background:color-mix(in srgb,var(--accent) 20%,transparent);color:var(--accent);border-radius:2px;padding:0 2px">$1</mark>');
    }
}
