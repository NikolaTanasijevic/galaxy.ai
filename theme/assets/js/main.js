/* ── BADGE TYPEWRITER ── */
(function() {
  const el = document.getElementById('badgeTyped');
  if (!el) return;
  const text = '500+ Premium Domains Available Now';
  let i = 0;
  function tick() {
    el.textContent = text.slice(0, i + 1);
    i++;
    if (i < text.length) setTimeout(tick, 55);
  }
  setTimeout(tick, 500);
})();

/* ── NAV ── */
const nav = document.getElementById('nav');
if (nav) {
  window.addEventListener('scroll', () => nav.classList.toggle('sc', window.scrollY > 40), {passive: true});
}

/* ── MOBILE MENU ── */
const ham     = document.getElementById('ham');
const mobMenu = document.getElementById('mobMenu');
const mobClose = document.getElementById('mobClose');
if (ham) {
  ham.addEventListener('click', () => {
    mobMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
    ham.style.visibility = 'hidden';
  });
  mobClose.addEventListener('click', gmCloseMob);
  document.querySelectorAll('.mob-link').forEach(l => l.addEventListener('click', gmCloseMob));
}
function gmCloseMob() {
  if (mobMenu) {
    mobMenu.classList.remove('open');
    document.body.style.overflow = '';
    if (ham) ham.style.visibility = 'visible';
  }
}

/* ── FADE IN ── */
const obs = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); } });
}, {threshold: 0.08});
document.querySelectorAll('.fi').forEach(el => obs.observe(el));

/* ── DOMAIN FILTER / SORT / SEARCH ── */
let gmActiveFilter = 'all';
let gmActiveSearch = '';

function gmFilterCat(cat) {
  gmActiveFilter = cat;
  document.querySelectorAll('.cat-pill').forEach(p => p.classList.toggle('active', p.dataset.cat === cat));
  gmApplyFilter();
}

function gmTriggerSearch() {
  const input = document.getElementById('heroSearch');
  if (!input) return;
  gmActiveSearch = input.value.toLowerCase().trim();
  gmApplyFilter();
  const domains = document.getElementById('domains');
  if (domains) domains.scrollIntoView({behavior: 'smooth', block: 'start'});
}

function gmArchiveSearch() {
  const input = document.getElementById('archiveSearch');
  if (!input) return;
  gmActiveSearch = input.value.toLowerCase().trim();
  gmApplyFilter();
}

function gmApplyFilter() {
  const cards = document.querySelectorAll('.domain-card');
  let visible = 0;
  cards.forEach(card => {
    const catMatch    = gmActiveFilter === 'all' || card.dataset.cat === gmActiveFilter;
    const domainText  = card.querySelector('.dc-domain')?.textContent.toLowerCase() ?? '';
    const descText    = card.querySelector('.dc-desc')?.textContent.toLowerCase() ?? '';
    const searchMatch = !gmActiveSearch || domainText.includes(gmActiveSearch) || descText.includes(gmActiveSearch);
    const show = catMatch && searchMatch;
    card.classList.toggle('hidden', !show);
    if (show) visible++;
  });
  gmUpdateCount(visible);
}

function gmUpdateCount(count) {
  const el = document.getElementById('domainCount');
  if (!el) return;
  if (count === undefined) {
    count = document.querySelectorAll('.domain-card:not(.hidden)').length;
  }
  el.textContent = count;
}

function gmSortDomains(val) {
  const grid = document.getElementById('domainsGrid');
  if (!grid) return;
  const cards = [...grid.querySelectorAll('.domain-card')];
  if (val === 'price-high') {
    cards.sort((a, b) => (Number(b.dataset.price) || 0) - (Number(a.dataset.price) || 0));
  } else if (val === 'price-low') {
    cards.sort((a, b) => {
      const ap = Number(a.dataset.price) || Infinity;
      const bp = Number(b.dataset.price) || Infinity;
      return ap - bp;
    });
  } else if (val === 'alpha') {
    cards.sort((a, b) => {
      const at = a.querySelector('.dc-domain')?.textContent ?? '';
      const bt = b.querySelector('.dc-domain')?.textContent ?? '';
      return at.localeCompare(bt);
    });
  } else {
    cards.sort((a, b) => Number(a.dataset.origIndex) - Number(b.dataset.origIndex));
  }
  cards.forEach(c => grid.appendChild(c));
}

/* ── SEARCH AUTOCOMPLETE ── */
function gmInitAutocomplete(inputId, dropdownId) {
  const input    = document.getElementById(inputId);
  const dropdown = document.getElementById(dropdownId);
  if (!input || !dropdown) return;

  let debounceTimer;
  let activeIndex = -1;

  function highlight(text, term) {
    if (!term) return text;
    const re = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
    return text.replace(re, '<mark>$1</mark>');
  }

  function renderItems(items, term) {
    if (!items.length) { closeDropdown(); return; }
    dropdown.innerHTML = items.map((item, i) =>
      `<div class="sd-item" data-url="${item.url}" data-index="${i}">
        <div>
          <div class="sd-title">${highlight(item.title, term)}</div>
          <div class="sd-meta"><span>${item.cat}</span></div>
        </div>
        <div class="sd-price">${item.price}</div>
      </div>`
    ).join('');
    dropdown.querySelectorAll('.sd-item').forEach(el => {
      el.addEventListener('mousedown', e => { e.preventDefault(); window.location = el.dataset.url; });
    });
    activeIndex = -1;
    dropdown.classList.add('open');
  }

  function closeDropdown() {
    dropdown.classList.remove('open');
    dropdown.innerHTML = '';
    activeIndex = -1;
  }

  function suggest(term) {
    fetch(`${gmAjax?.url ?? '/wp-admin/admin-ajax.php'}?action=gm_suggest&q=${encodeURIComponent(term)}`)
      .then(r => r.json())
      .then(res => { if (res.success) renderItems(res.data, term); });
  }

  input.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const val = input.value.trim();
    if (val.length < 3) { closeDropdown(); return; }
    debounceTimer = setTimeout(() => suggest(val), 220);
  });

  input.addEventListener('keydown', e => {
    const items = dropdown.querySelectorAll('.sd-item');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      activeIndex = Math.min(activeIndex + 1, items.length - 1);
      items.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      activeIndex = Math.max(activeIndex - 1, -1);
      items.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
    } else if (e.key === 'Enter' && activeIndex >= 0) {
      e.preventDefault();
      window.location = items[activeIndex].dataset.url;
    } else if (e.key === 'Escape') {
      closeDropdown();
    }
  });

  document.addEventListener('click', e => {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) closeDropdown();
  });
}

gmInitAutocomplete('heroSearch', 'searchDropdown');
gmInitAutocomplete('archiveSearch', 'archiveDropdown');

/* Init: search on Enter */
document.addEventListener('DOMContentLoaded', () => {
  const heroInput = document.getElementById('heroSearch');
  if (heroInput) heroInput.addEventListener('keydown', e => { if (e.key === 'Enter') gmTriggerSearch(); });

  const archInput = document.getElementById('archiveSearch');
  if (archInput) archInput.addEventListener('keydown', e => { if (e.key === 'Enter') gmArchiveSearch(); });

  gmUpdateCount();
});

/* ── CONTACT PAGE FORM ── */
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = contactForm.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.textContent = 'Sending…';
    const data = new FormData(contactForm);
    data.append('action', 'gm_contact');
    fetch(gmAjax?.url ?? '/wp-admin/admin-ajax.php', { method: 'POST', body: data })
      .then(r => r.json())
      .then(res => {
        const success = document.getElementById('contactFormSuccess');
        if (res.success) {
          contactForm.style.display = 'none';
          success.textContent = res.data;
          success.style.display = 'block';
        } else {
          btn.disabled = false;
          btn.textContent = 'Send Message';
          alert(res.data || 'Something went wrong.');
        }
      })
      .catch(() => { btn.disabled = false; btn.textContent = 'Send Message'; });
  });
}

/* ── INQUIRY FORM ── */
const form = document.getElementById('inquiryForm');
if (form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = form.querySelector('.btn-inquire');
    btn.disabled = true;
    btn.textContent = 'Sending…';

    const data = new FormData(form);
    data.append('action', 'gm_inquiry');

    fetch(gmAjax?.url ?? '/wp-admin/admin-ajax.php', {
      method: 'POST',
      body: data,
    })
    .then(r => r.json())
    .then(res => {
      const success = document.getElementById('formSuccess');
      if (res.success) {
        form.style.display = 'none';
        success.textContent = res.data;
        success.style.display = 'block';
      } else {
        btn.disabled = false;
        btn.textContent = 'Send Inquiry';
        alert(res.data || 'Something went wrong. Please try again.');
      }
    })
    .catch(() => {
      btn.disabled = false;
      btn.textContent = 'Send Inquiry';
    });
  });
}
