/* ============================================================
   Merinaz admin · Vanilla JS interactions, UI router & state
   ============================================================ */
const $ = s => document.querySelector(s);
const $$ = s => Array.from(document.querySelectorAll(s));
const body = document.body;

/* ---------- toasts ---------- */
function toast(msg, accent = false) {
    const box = document.createElement('div');
    box.className = 'mz-toast' + (accent ? ' accent' : '');
    box.innerHTML = '<span class="t-dot"></span><span></span>';
    box.lastChild.textContent = msg;
    const toastBox = $('#toastBox');
    if (toastBox) {
        toastBox.appendChild(box);
        setTimeout(() => {
            box.style.opacity = '0';
            setTimeout(() => box.remove(), 300);
        }, 3200);
    }
}

document.addEventListener('click', e => {
    const t = e.target.closest('[data-toast]');
    if (t) {
        toast(t.dataset.toast);
    }
});

/* ---------- modal: destructive confirm and email preview ---------- */
const back = $('#modalBack');

function openModal(title, bodyText, okText, previewHTML) {
    if (!$('#mTitle') || !back) return;
    $('#mTitle').textContent = title;
    $('#mBody').textContent = bodyText;
    $('#mOk').textContent = okText || 'Confirm';
    const pv = $('#mPreview');
    if (previewHTML) {
        pv.style.display = 'block';
        pv.innerHTML = previewHTML;
    } else {
        pv.style.display = 'none';
        pv.innerHTML = '';
    }
    back.classList.add('show');
    $('#mCancel')?.focus();
}

function closeModal() {
    back?.classList.remove('show');
}

$('#mCancel')?.addEventListener('click', closeModal);
back?.addEventListener('click', e => {
    if (e.target === back) closeModal();
});
$('#mOk')?.addEventListener('click', () => {
    closeModal();
    toast('Done. Change saved.');
});

document.addEventListener('click', e => {
    const c = e.target.closest('[data-confirm]');
    if (c) {
        const p = c.dataset.confirm.split('|');
        openModal(p[0], p[1], p[2]);
    }
    const pv = e.target.closest('[data-preview]');
    if (pv) {
        openModal(
            'Preview · ' + pv.dataset.preview,
            'This is what the client receives. Sender is hello@merinazdc.com.',
            'Close preview',
            '<div style="border:1px solid var(--mz-border);border-radius:6px;padding:14px;background:var(--mz-bg);" class="small"><strong>Merinaz · Adams Morgan</strong><br><br>Hi Maya,<br>Your visit on Mon 5 Oct at 10:00a is confirmed.<br>Full leg wax with Priya, custom facial with Daniela.<br>Total due at desk: $170.<br><br>Free cancellation up to 12 hours before.<br>1800 Adams Mill Rd NW · 202-555-0100</div>'
        );
    }
});

$('#cancelVisitBtn')?.addEventListener('click', () => {
    openModal(
        'Cancel visit 1042',
        'This cancels 2 services for Maya Thompson on Mon 5 Oct. Policy: inside 12 hours, 50% applies. Client gets an email.',
        'Cancel visit'
    );
});

/* ---------- view router ---------- */
const titles = {
    today: 'Today',
    calendar: 'Calendar',
    bookings: 'Bookings',
    'booking-detail': 'Booking detail',
    'booking-new': 'New booking',
    clients: 'Clients',
    'client-detail': 'Client detail',
    staff: 'Staff',
    'staff-edit': 'Staff detail',
    services: 'Services',
    'service-edit': 'Edit service',
    categories: 'Categories',
    reviews: 'Reviews',
    blog: 'Blog',
    'blog-editor': 'Edit post',
    pages: 'Pages',
    'page-editor': 'Edit page',
    faq: 'FAQ',
    messages: 'Messages',
    'site-settings': 'Site settings',
    'email-notifications': 'Email notifications',
    'users-roles': 'Users and roles',
    account: 'Account',
    'role-compare': 'Role comparison',
    'error-pages': 'Error pages'
};

function showView(id) {
    $$('.mz-view').forEach(v => v.classList.remove('on'));
    const el = $('#view-' + id);
    if (!el) return;

    // staff guard: only today calendar bookings clients account
    if (
        body.dataset.role === 'staff' &&
        !['today', 'calendar', 'bookings', 'booking-detail', 'booking-new', 'clients', 'client-detail', 'account'].includes(id)
    ) {
        showView('today');
        return;
    }

    el.classList.add('on');
    if ($('#pageTitle')) $('#pageTitle').textContent = titles[id] || id;

    $$('.side-link').forEach(a => {
        a.classList.toggle(
            'active',
            a.dataset.view === id ||
            ((id === 'booking-detail' || id === 'booking-new') && a.dataset.view === 'bookings') ||
            (id === 'client-detail' && a.dataset.view === 'clients') ||
            (id === 'staff-edit' && a.dataset.view === 'staff') ||
            (id === 'service-edit' && a.dataset.view === 'services') ||
            (id === 'blog-editor' && a.dataset.view === 'blog') ||
            (id === 'page-editor' && a.dataset.view === 'pages')
        );
    });

    body.classList.remove('side-open');
    const mainContent = $('#mainContent');
    if (mainContent) mainContent.scrollTop = 0;
    window.scrollTo(0, 0);

    if (id === 'bookings' && !el.dataset.loaded) {
        skeleton('#bookingsBody', 6);
        el.dataset.loaded = '1';
    }
    if (id === 'clients' && !el.dataset.loaded) {
        skeleton('#clientsBody', 6);
        el.dataset.loaded = '1';
    }
}

document.addEventListener('click', e => {
    const v = e.target.closest('[data-view]');
    if (v) {
        e.preventDefault();
        showView(v.dataset.view);
    }
});

const menuBtn = $('#menuBtn');
if (menuBtn) menuBtn.onclick = () => body.classList.toggle('side-open');

const footAccount = $('#footAccount');
if (footAccount) {
    footAccount.onclick = e => {
        e.preventDefault();
        showView('account');
    };
}

/* ---------- skeleton: flat pulse rows ---------- */
function skeleton(tbodySel, n) {
    const tb = $(tbodySel);
    if (!tb) return;
    const orig = tb.innerHTML;
    tb.innerHTML = Array.from({ length: n }, () =>
        '<tr><td colspan="8"><div class="sk" style="width:96%;"></div><div class="sk mt-2" style="width:70%;"></div></td></tr>'
    ).join('');
    setTimeout(() => {
        tb.innerHTML = orig;
        bindRowChecks();
        applyFilters();
        applyRoleScope();
    }, 650);
}

/* ---------- role switching ---------- */
const staffUser = 'Lila Karki';

$$('.role-switch button').forEach(b => {
    b.onclick = () => {
        $$('.role-switch button').forEach(x => x.classList.remove('on'));
        b.classList.add('on');
        body.dataset.role = b.dataset.role;

        const avatarBtn = $('#avatarBtn');
        if (avatarBtn) {
            avatarBtn.textContent = b.dataset.role === 'owner' ? 'MH' : b.dataset.role === 'manager' ? 'SM' : 'LK';
        }

        if (b.dataset.role === 'staff') {
            showView('today');
        } else {
            applyRoleScope();
        }
        toast('Previewing as ' + b.dataset.role + '. View updated.');
    };
});

function applyRoleScope() {
    const staff = body.dataset.role === 'staff';
    $$('#view-today .tl-person, #view-calendar .tl-person').forEach(c => {
        c.style.display = (staff && c.dataset.staff !== staffUser) ? 'none' : '';
    });
    $$('#bookingsBody tr').forEach(r => {
        r.style.display = (staff && !(r.dataset.staff || '').includes(staffUser)) ? 'none' : '';
    });
    $$('#clientHist tr').forEach(r => {
        r.style.display = (staff && !(r.dataset.staff || '').includes(staffUser)) ? 'none' : '';
    });
    const note = document.querySelector('#view-client-detail .staff-note');
    if (note) note.style.display = staff ? 'block' : 'none';

    const bookingsCount = $('#bookingsCount');
    if (bookingsCount) {
        bookingsCount.textContent = staff
            ? '4 visits in range · your day only'
            : '12 visits in range · $684 expected · 4 pending';
    }
}
applyRoleScope();

/* ---------- bookings filters + bulk ---------- */
function applyFilters() {
    const s = ($('#fStaff')?.value || '').toLowerCase();
    const st = ($('#fStatus')?.value || '').toLowerCase();
    const q = ($('#fSearch')?.value || '').toLowerCase();

    $$('#bookingsBody tr').forEach(r => {
        let vis = true;
        if (s && !(r.dataset.staff || '').toLowerCase().includes(s)) vis = false;
        if (st && (r.dataset.status || '').toLowerCase() !== st) vis = false;
        if (q && !r.textContent.toLowerCase().includes(q)) vis = false;
        if (body.dataset.role === 'staff' && !(r.dataset.staff || '').includes(staffUser)) vis = false;
        r.style.display = vis ? '' : 'none';
    });
    updateBulk();
}

['fStaff', 'fStatus'].forEach(id => $('#' + id)?.addEventListener('change', applyFilters));
$('#fSearch')?.addEventListener('input', applyFilters);
$('#fDate')?.addEventListener('change', () => toast('Date range updated. Showing matching visits.'));

function bindRowChecks() {
    $$('.rowcheck').forEach(c => c.onchange = updateBulk);
    const all = $('#checkAll');
    if (all) {
        all.onchange = () => {
            $$('#bookingsBody tr').forEach(r => {
                if (r.style.display !== 'none') {
                    const c = r.querySelector('.rowcheck');
                    if (c) c.checked = all.checked;
                }
            });
            updateBulk();
        };
    }
}

function updateBulk() {
    const n = $$('#bookingsBody .rowcheck').filter(c => c.checked && c.closest('tr').style.display !== 'none').length;
    $('#bulkBar')?.classList.toggle('show', n > 0);
    if ($('#bulkCount')) $('#bulkCount').textContent = n + ' selected';
    $$('#bookingsBody tr').forEach(r => r.classList.toggle('is-selected', !!r.querySelector('.rowcheck')?.checked));
}
bindRowChecks();
updateBulk();

const bulkClear = $('#bulkClear');
if (bulkClear) {
    bulkClear.onclick = () => {
        $$('.rowcheck').forEach(c => c.checked = false);
        const checkAll = $('#checkAll');
        if (checkAll) checkAll.checked = false;
        updateBulk();
    };
}

/* ---------- calendar toggle ---------- */
$('#calDayBtn')?.addEventListener('click', () => {
    $('#calDayBtn')?.classList.add('on');
    $('#calWeekBtn')?.classList.remove('on');
    if ($('#calDay')) $('#calDay').style.display = '';
    if ($('#calWeek')) $('#calWeek').style.display = 'none';
});

$('#calWeekBtn')?.addEventListener('click', () => {
    $('#calWeekBtn')?.classList.add('on');
    $('#calDayBtn')?.classList.remove('on');
    if ($('#calWeek')) $('#calWeek').style.display = '';
    if ($('#calDay')) $('#calDay').style.display = 'none';
});

/* ---------- clients search ---------- */
$('#clientSearch')?.addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    $$('#clientsBody tr').forEach(r => r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none');
});

$('#topSearch')?.addEventListener('input', e => {
    const q = e.target.value;
    const fs = $('#fSearch');
    if (fs) {
        fs.value = q;
        applyFilters();
    }
    const cs = $('#clientSearch');
    if (cs) {
        cs.value = q;
        cs.dispatchEvent(new Event('input'));
    }
});

/* ---------- new booking ---------- */
const nbNewToggle = $('#nbNewToggle');
if (nbNewToggle) {
    nbNewToggle.onchange = e => {
        const nbNewForm = $('#nbNewForm');
        if (nbNewForm) nbNewForm.style.display = e.target.checked ? 'flex' : 'none';
    };
}

$$('#nbClientPicks button').forEach(b => {
    b.onclick = () => {
        if ($('#nbClientName')) $('#nbClientName').textContent = b.dataset.client;
        if ($('#nbSumClient')) $('#nbSumClient').textContent = b.dataset.client;
    };
});

$('#nbAdd')?.addEventListener('click', () => {
    const v = $('#nbSvc')?.value;
    const d = document.createElement('div');
    d.className = 'line-item py-2';
    d.innerHTML = '<div class="d-flex justify-content-between align-items-center"><span class="small"><strong></strong></span><button class="btn btn-quiet btn-sm">Remove</button></div>';
    d.querySelector('strong').textContent = v;
    d.querySelector('button').onclick = () => d.remove();
    $('#nbList')?.appendChild(d);
    toast('Service added. Pick staff and slot below.');
});

$$('#nbSlots .slot').forEach(s => {
    if (s.disabled) return;
    s.onclick = () => {
        $$('#nbSlots .slot').forEach(x => x.classList.remove('sel'));
        s.classList.add('sel');
    };
});

/* ---------- staff blackout ---------- */
$('#boAdd')?.addEventListener('click', () => {
    const d = $('#boDate')?.value || 'Dec 24';
    const w = $('#boWhy')?.value || 'Holiday';
    const li = document.createElement('li');
    li.className = 'd-flex justify-content-between border-bottom py-2';
    li.innerHTML = '<span></span><button class="btn btn-quiet btn-sm">Remove</button>';
    li.querySelector('span').textContent = d + ' · ' + w;
    li.querySelector('button').onclick = () => li.remove();
    $('#blackoutList')?.appendChild(li);
    if ($('#boDate')) $('#boDate').value = '';
    if ($('#boWhy')) $('#boWhy').value = '';
    toast('Blackout date added.');
});

$$('#blackoutList button').forEach(b => {
    b.onclick = e => e.target.closest('li')?.remove();
});

/* ---------- reviews: compute from rows ---------- */
function computeReviews() {
    const rows = $$('#reviewsList .line-item');
    if (!rows.length) return;
    const rs = rows.map(r => +r.dataset.rating);
    const avg = (rs.reduce((a, b) => a + b, 0) / rs.length).toFixed(1);
    if ($('#revAvg')) $('#revAvg').textContent = avg;
    if ($('#revCount')) $('#revCount').textContent = rs.length + ' reviews shown';
}
computeReviews();

/* ---------- messages tabs ---------- */
const tabNeed = $('#tabNeed');
const tabDone = $('#tabDone');
if (tabNeed && tabDone) {
    tabNeed.onclick = () => {
        tabNeed.classList.add('on');
        tabDone.classList.remove('on');
        if ($('#msgNeed')) $('#msgNeed').style.display = '';
        if ($('#msgDone')) $('#msgDone').style.display = 'none';
    };
    tabDone.onclick = () => {
        tabDone.classList.add('on');
        tabNeed.classList.remove('on');
        if ($('#msgNeed')) $('#msgNeed').style.display = 'none';
        if ($('#msgDone')) $('#msgDone').style.display = '';
    };
}

$('#backNeed')?.addEventListener('click', () => tabNeed?.click());

$$('.inbox-item').forEach(r => {
    r.onclick = () => {
        $$('.inbox-item').forEach(x => x.classList.remove('active'));
        r.classList.add('active');
        r.classList.remove('unread');
        const d = r.querySelector('.dot-unread');
        if (d) d.remove();
    };
});

/* ---------- blog slug ---------- */
$('#bpTitle')?.addEventListener('input', e => {
    const bpSlug = $('#bpSlug');
    if (bpSlug) {
        bpSlug.value = e.target.value
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '')
            .slice(0, 60);
    }
});

/* ---------- site settings unsaved ---------- */
$$('.site-field').forEach(f => {
    f.addEventListener('input', () => $('#unsavedBar')?.classList.add('show'));
});

function saveSite() {
    $('#unsavedBar')?.classList.remove('show');
    toast('Settings saved. Changes are live on the site.');
}

$('#unsavedSave')?.addEventListener('click', saveSite);
$('#siteSaveTop')?.addEventListener('click', saveSite);
$('#unsavedDiscard')?.addEventListener('click', () => {
    $('#unsavedBar')?.classList.remove('show');
    toast('Changes discarded.');
});

/* ---------- error pages ---------- */
const errs = {
    '403': ['Error 403', 'You do not have access to this page.', 'Your role is Staff. Ask an owner if you need this section.', 'Go to today'],
    '404': ['Error 404', 'This page was not found.', 'It may have been moved or the link is old. Check the menu and try again.', 'Go to today'],
    '419': ['Error 419', 'Your session expired.', 'Log in again to continue. Unsaved work was kept on this device.', 'Log in again'],
    '429': ['Error 429', 'Too many requests.', 'Wait a moment, then try again. The desk line stays open at 202-555-0100.', 'Try again'],
    '500': ['Error 500', 'Something went wrong on our side.', 'We logged the error with time and page. Try again in a minute.', 'Go to today'],
    '503': ['Error 503', 'The site is down for maintenance.', 'Bookings are paused for about 20 minutes. Existing visits are safe.', 'Try again']
};

$$('#errBtns button').forEach(b => {
    b.onclick = () => {
        $$('#errBtns button').forEach(x => {
            x.classList.remove('btn-ink');
            x.classList.add('btn-outline');
        });
        b.classList.add('btn-ink');
        b.classList.remove('btn-outline');

        const e = errs[b.dataset.err];
        const errPanel = $('#errPanel');
        if (!errPanel) return;

        errPanel.innerHTML = '<div class="tiny muted"></div><h2 class="mt-1"></h2><p class="small muted"></p><button class="btn btn-accent btn-sm" data-view="today"></button>';
        errPanel.children[0].textContent = e[0];
        errPanel.children[1].textContent = e[1];
        errPanel.children[2].textContent = e[2];
        errPanel.children[3].textContent = e[3];
        errPanel.children[3].onclick = () => {
            if (b.dataset.err === '419') {
                logout();
            } else {
                showView('today');
            }
        };
    };
});

/* ---------- login ---------- */
function logout() {
    const loginWrap = $('#loginWrap');
    if (loginWrap) loginWrap.style.display = 'flex';
}

function loginDone() {
    const loginWrap = $('#loginWrap');
    if (loginWrap) loginWrap.style.display = 'none';
    toast('Welcome back. Today has 3 bookings to confirm.');
}

$('#loginForm')?.addEventListener('submit', e => {
    e.preventDefault();
    if ($('#loginRate')?.style.display === 'block') return;
    loginDone();
});

$('#demoErr')?.addEventListener('click', () => {
    if ($('#loginError')) $('#loginError').style.display = 'block';
    if ($('#loginRate')) $('#loginRate').style.display = 'none';
});

$('#demoRate')?.addEventListener('click', () => {
    if ($('#loginRate')) $('#loginRate').style.display = 'block';
    if ($('#loginError')) $('#loginError').style.display = 'none';
    const loginBtn = $('#loginBtn');
    if (loginBtn) {
        loginBtn.disabled = true;
        setTimeout(() => loginBtn.disabled = false, 2000);
    }
});

$('#footLogout')?.addEventListener('click', e => {
    e.preventDefault();
    logout();
});
$('#ddLogout')?.addEventListener('click', e => {
    e.preventDefault();
    logout();
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});
