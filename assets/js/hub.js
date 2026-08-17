(function () {
  'use strict';

  var app;

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function revealElements() {
    if (!app || app.dataset.wcasMotion !== 'on' || prefersReducedMotion()) return;
    var elements = app.querySelectorAll('.wcas-screen > *');
    if (!('IntersectionObserver' in window)) {
      elements.forEach(function (element) { element.classList.add('wcas-reveal'); });
      return;
    }
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('wcas-reveal');
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.08 });
    elements.forEach(function (element, index) {
      element.style.transitionDelay = Math.min(index * 55, 220) + 'ms';
      observer.observe(element);
    });
  }

  function footerDockBehaviour() {
    var dock = document.querySelector('.wcas-mobile-nav');
    var footer = document.querySelector('#colophon, footer.site-footer, footer');
    if (!dock || !footer || !('IntersectionObserver' in window)) return;
    new IntersectionObserver(function (entries) {
      dock.classList.toggle('wcas-dock-hidden', entries.some(function (entry) { return entry.isIntersecting; }));
    }, { threshold: 0.05 }).observe(footer);
  }

  function setClassPrefix(element, prefix, next) {
    Array.prototype.slice.call(element.classList).forEach(function (className) {
      if (className.indexOf(prefix) === 0) element.classList.remove(className);
    });
    element.classList.add(prefix + next);
  }

  function shellForTemplate(template) {
    return { luxury: 'editorial', beauty: 'editorial', digital: 'tabs', subscription: 'club', service: 'console', wholesale: 'console', grocery: 'rail', commerce: 'rail' }[template] || 'rail';
  }

  function previewText(selector, value) {
    var element = app.querySelector(selector);
    if (!element) return;
    if (!element.dataset.wcasOriginal) element.dataset.wcasOriginal = element.textContent;
    element.textContent = value || element.dataset.wcasOriginal;
  }

  function previewVisibility(selector, visible) {
    app.querySelectorAll(selector).forEach(function (element) { element.hidden = !visible; });
  }

  function previewInitial(value) {
    return (value || 'A').trim().charAt(0).toUpperCase() || 'A';
  }

  /* Kept in sync with the server icon catalogue so unsaved choices render in the iframe. */
  var mobileIconPaths = {
    grid: '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
    home: '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z"/><path d="M9 21v-6h6v6"/>',
    sparkles: '<path d="m12 3 1.5 5.5L19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3Z"/><path d="m19 16 .6 2.4L22 19l-2.4.6L19 22l-.6-2.4L16 19l2.4-.6L19 16Z"/>',
    compass: '<circle cx="12" cy="12" r="9"/><path d="m15.8 8.2-2.1 5.5-5.5 2.1 2.1-5.5 5.5-2.1Z"/>',
    bag: '<path d="M5 8h14l-1 13H6L5 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/>',
    receipt: '<path d="M5 3h14v18l-2.5-1.5L14 21l-2-1.5L10 21l-2.5-1.5L5 21V3Z"/><path d="M8 8h8M8 12h8M8 16h5"/>',
    box: '<path d="m3 7 9-4 9 4v10l-9 4-9-4V7Z"/><path d="m3 7 9 4 9-4M12 11v10"/>',
    truck: '<path d="M3 6h11v10H3V6Z"/><path d="M14 10h4l3 3v3h-7v-6Z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>',
    download: '<path d="M12 3v12m0 0 4-4m-4 4-4-4"/><path d="M4 20h16"/>',
    book: '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16Z"/><path d="M4 19h16"/><path d="M9 7h6"/>',
    folder: '<path d="M3 6h7l2 2h9v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6Z"/><path d="M3 10h18"/>',
    play: '<circle cx="12" cy="12" r="9"/><path d="m10 8 6 4-6 4V8Z"/>',
    pin: '<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/>',
    map: '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3V6Z"/><path d="M9 3v15m6-12v15"/>',
    'home-pin': '<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z"/><path d="M12 21v-6"/><circle cx="17" cy="13" r="2.5"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c1-4 3.7-6 8-6s7 2 8 6"/>',
    shield: '<path d="M12 3 20 6v5c0 5-3.4 8.5-8 10-4.6-1.5-8-5-8-10V6l8-3Z"/><path d="m9 12 2 2 4-4"/>',
    heart: '<path d="M20.8 8.7c0 5.2-8.8 10.3-8.8 10.3S3.2 13.9 3.2 8.7A4.7 4.7 0 0 1 12 6.4a4.7 4.7 0 0 1 8.8 2.3Z"/>',
    menu: '<path d="M4 7h16M4 12h16M4 17h16"/>',
    dots: '<circle cx="5" cy="12" r="1" fill="currentColor"/><circle cx="12" cy="12" r="1" fill="currentColor"/><circle cx="19" cy="12" r="1" fill="currentColor"/>',
    star: '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/>',
    help: '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.7 2.7 0 1 1 4.3 2.2c-1.5 1.1-1.8 1.5-1.8 3"/><path d="M12 17h.01"/>',
    chevron: '<path d="m9 18 6-6-6-6"/>'
  };

  function previewMobileIcon(element, iconName) {
    if (!element || !mobileIconPaths[iconName]) return;
    var svg = element.querySelector('svg');
    if (!svg) return;
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '1.8');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    svg.innerHTML = mobileIconPaths[iconName];
    element.dataset.wcasMobileIcon = iconName;
  }

  function previewMobileIcons(icons, template) {
    if (!icons || typeof icons !== 'object') return;
    var dock = document.querySelector('.wcas-mobile-nav');
    if (!dock) return;
    var endpoints = template === 'digital' ? ['dashboard', 'library', 'orders'] :
      ((template === 'service' || template === 'beauty') ? ['dashboard', 'orders', 'profile'] : ['dashboard', 'orders', 'addresses']);
    endpoints.forEach(function (endpoint, index) {
      var target = dock.querySelector('[data-wcas-mobile-item="' + endpoint + '"]') || dock.querySelectorAll('a')[index];
      previewMobileIcon(target, icons[endpoint]);
    });
    previewMobileIcon(dock.querySelector('[data-wcas-mobile-item="more"]') || dock.querySelector('.wcas-mobile-more-trigger'), icons.more);
  }

  /* Persistent links are rendered directly in the preview so a merchant can
     assess their copy, icon and placement before publishing anything. */
  function previewLinkUrl(value) {
    if (typeof value !== 'string') return '';
    value = value.trim();
    if (/^https?:\/\//i.test(value)) return value;
    return /^\/(?!\/)/.test(value) ? value : '';
  }

  function previewIconUrl(value) {
    if (typeof value !== 'string') return '';
    value = value.trim();
    return /^https:\/\//i.test(value) ? value : '';
  }

  function previewSvgIcon(name, className) {
    var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('aria-hidden', 'true');
    svg.setAttribute('width', '20');
    svg.setAttribute('height', '20');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('stroke', 'currentColor');
    svg.setAttribute('stroke-width', '1.8');
    svg.setAttribute('stroke-linecap', 'round');
    svg.setAttribute('stroke-linejoin', 'round');
    if (className) svg.setAttribute('class', className);
    svg.innerHTML = mobileIconPaths[name] || mobileIconPaths.star;
    return svg;
  }

  function previewLinkIcon(link) {
    var source = link.iconSource || link.icon_source || 'builtin';
    var imageUrl = '';
    if (source === 'iconify' && typeof link.iconify === 'string' && /^[a-z0-9][a-z0-9-]{0,63}:[a-z0-9][a-z0-9-]{0,127}$/i.test(link.iconify.trim())) {
      imageUrl = 'https://api.iconify.design/' + link.iconify.trim().toLowerCase() + '.svg';
    } else if (source === 'url') {
      imageUrl = previewIconUrl(link.iconUrl || link.icon_url || '');
    }
    if (!imageUrl) return previewSvgIcon(link.iconName || link.icon_name || 'star');
    var image = document.createElement('img');
    image.className = 'wcas-external-icon wcas-custom-link-image';
    image.src = imageUrl; image.alt = ''; image.width = 20; image.height = 20;
    image.setAttribute('aria-hidden', 'true');
    return image;
  }

  function previewCustomLink(link, context) {
    var href = previewLinkUrl(link.url || '');
    var anchor = document.createElement('a');
    anchor.className = 'wcas-custom-link';
    anchor.href = href || '#';
    if (link.newTab || link.new_tab) { anchor.target = '_blank'; anchor.rel = 'noopener noreferrer'; }
    /* The iframe is a visual preview, not a way to navigate to an unsaved URL. */
    anchor.addEventListener('click', function (event) { event.preventDefault(); });
    anchor.appendChild(previewLinkIcon(link));
    if (context === 'sheet') {
      var labelStrong = document.createElement('b'); labelStrong.textContent = String(link.label || '').trim(); anchor.appendChild(labelStrong);
      anchor.appendChild(previewSvgIcon('chevron'));
    } else {
      var label = document.createElement('span'); label.textContent = String(link.label || '').trim(); anchor.appendChild(label);
      if (context === 'more') anchor.appendChild(previewSvgIcon('chevron'));
    }
    return anchor;
  }

  function previewCustomLinks(links) {
    if (!Array.isArray(links)) return;
    var valid = links.filter(function (link) {
      return link && truthySetting(link.enabled) && String(link.label || '').trim() && previewLinkUrl(link.url || '');
    });
    document.querySelectorAll('[data-wcas-custom-links]').forEach(function (slot) {
      var context = slot.getAttribute('data-wcas-custom-links');
      var list = context === 'sidebar' ? slot : slot.querySelector('.wcas-custom-links');
      if (!list) return;
      slot.hidden = valid.length === 0;
      list.replaceChildren();
      valid.forEach(function (link) { list.appendChild(previewCustomLink(link, context)); });
    });
  }

  function previewNavigationLabels(labels) {
    if (!labels || typeof labels !== 'object') return;
    var destinations = { dashboard: 'dashboard', orders: 'orders', library: 'downloads', addresses: 'edit-address', profile: 'edit-account', more: 'more' };
    Object.keys(destinations).forEach(function (setting) {
      var endpoint = destinations[setting];
      var selector = '[data-wcas-nav-item="' + endpoint + '"], [data-wcas-mobile-item="' + endpoint + '"]';
      document.querySelectorAll(selector).forEach(function (target) {
        var text = target.querySelector('small, span');
        if (!text) return;
        if (!text.dataset.wcasOriginal) text.dataset.wcasOriginal = text.textContent;
        var label = labels[setting] || text.dataset.wcasOriginal;
        text.textContent = label;
        target.setAttribute('aria-label', label);
      });
    });
  }

  function previewThemeTitleVisibility(hidden) {
    if (!document.body.classList.contains('wcas-preview-frame')) return;
    Array.prototype.slice.call(document.querySelectorAll('.entry-header, .page-header, .woocommerce-products-header, .page-title, .entry-title, h1')).forEach(function (element) {
      if ((app && app.contains(element)) || element.closest('#wpadminbar')) return;
      element.classList.toggle('wcas-preview-hidden-title', hidden);
    });
  }

  function truthySetting(value) {
    return value === true || value === 1 || value === '1' || value === 'yes' || value === 'on' || value === 'true';
  }

  function hubConfig() {
    return window.wcasHub && typeof window.wcasHub === 'object' ? window.wcasHub : {};
  }

  function firstSetting(payload, names) {
    var config = hubConfig();
    var i;
    for (i = 0; i < names.length; i += 1) {
      if (payload && Object.prototype.hasOwnProperty.call(payload, names[i])) return payload[names[i]];
      if (Object.prototype.hasOwnProperty.call(config, names[i])) return config[names[i]];
    }
    return undefined;
  }

  function closestElement(element, selector) {
    if (!element) return null;
    if (element.closest) return element.closest(selector);
    var current = element;
    while (current && current.nodeType === 1) {
      if (current.matches && current.matches(selector)) return current;
      current = current.parentElement;
    }
    return null;
  }

  function elementMatches(element, selector) {
    if (!element || element.nodeType !== 1) return false;
    var matcher = element.matches || element.msMatchesSelector || element.webkitMatchesSelector;
    return matcher ? matcher.call(element, selector) : false;
  }

  function isProtectedThemeChrome(element) {
    if (!element || (app && (element === app || app.contains(element)))) return true;
    return !!closestElement(element, 'header, footer, #wpadminbar, .site-header, .site-footer, .elementor-location-header, .elementor-location-footer, .wcas-app');
  }

  function safeThemeHeroSelector(payload) {
    var selector = firstSetting(payload, ['themeHeroSelector', 'theme_hero_selector']);
    if (!selector && app) selector = app.getAttribute('data-wcas-theme-hero-selector');
    if (!selector || typeof selector !== 'string') return '';
    selector = selector.trim();
    /* This is a merchant-controlled selector. Keep it short and single-target,
       then validate it with the browser before it is ever used. */
    if (selector.length > 240 || /[\u0000{};,]/.test(selector) || !/[.#\[]/.test(selector)) return '';
    try {
      document.querySelector(selector);
    } catch (error) {
      return '';
    }
    return selector;
  }

  function likelyHeroContainer(element) {
    return elementMatches(element, '[data-wcas-theme-hero], .elementor-top-section, .elementor-section, .page-header, .entry-header, .page-title-wrap, .titlebar, .hero, .banner, .page-hero, .page-banner');
  }

  function heroContainerFor(element) {
    if (!element || isProtectedThemeChrome(element)) return null;
    if (likelyHeroContainer(element)) return element;
    var structural = closestElement(element, '[data-wcas-theme-hero], .elementor-top-section, .elementor-section, .page-header, .entry-header, .page-title-wrap, .titlebar, .hero, .banner, .page-hero, .page-banner');
    if (structural && !isProtectedThemeChrome(structural)) return structural;
    /* A custom selector may point directly at a title widget rather than its
       containing section. Hiding that widget is safer than hiding all <main>. */
    if (elementMatches(element, 'h1, .elementor-widget-theme-post-title, .elementor-page-title, .page-title, .entry-title')) return element;
    return null;
  }

  function normalisedHeroText(value) {
    return (value || '').toLowerCase().replace(/[\u064b-\u065f\u0670]/g, '').replace(/[أإآ]/g, 'ا').replace(/ى/g, 'ي').replace(/[\s\u00a0\-_–—:|]+/g, ' ').trim();
  }

  function isAccountHeroTitle(value) {
    var text = normalisedHeroText(value);
    if (!text) return false;
    return [
      'my account', 'account dashboard', 'customer account',
      'حسابي', 'حساب العميل', 'لوحة حسابي', 'لوحة الحساب', 'حساب المستخدم',
      'mi cuenta', 'mon compte', 'mein konto', 'il mio account', 'min konto',
      'minha conta', 'meu cadastro', 'mijn account', 'moj racun', 'moj račun',
      'моя учетная запись', 'моя учётная запись', 'hesabim', 'hesabım',
      '我的帐户', '我的账户', 'マイアカウント', '내 계정'
    ].some(function (title) { return text === normalisedHeroText(title) || text.indexOf(normalisedHeroText(title) + ' ') === 0; });
  }

  function restoreThemeHero() {
    Array.prototype.slice.call(document.querySelectorAll('[data-wcas-theme-hero-hidden="1"]')).forEach(function (element) {
      element.classList.remove('wcas-theme-hero-hidden');
      element.removeAttribute('data-wcas-theme-hero-hidden');
    });
  }

  function hideThemeHeroElement(element) {
    if (!element || isProtectedThemeChrome(element)) return;
    element.classList.add('wcas-theme-hero-hidden');
    element.setAttribute('data-wcas-theme-hero-hidden', '1');
  }

  function findAccountThemeHero(selector) {
    var candidate;
    if (selector) {
      try {
        candidate = document.querySelector(selector);
      } catch (error) {
        candidate = null;
      }
      return heroContainerFor(candidate);
    }
    var headings = document.querySelectorAll('h1, [role="heading"][aria-level="1"]');
    for (var i = 0; i < headings.length; i += 1) {
      if (isProtectedThemeChrome(headings[i]) || !isAccountHeroTitle(headings[i].textContent)) continue;
      candidate = heroContainerFor(headings[i]);
      if (candidate) return candidate;
    }
    return null;
  }

  /* Hides only the account title hero outside Account Studio. A theme-specific
     selector wins when supplied; otherwise we recognise account titles in the
     supported interface languages and stop at the nearest hero-like section. */
  function syncThemeHero(payload) {
    var enabledValue = firstSetting(payload, ['hideThemeHero', 'hide_theme_hero']);
    var enabled = typeof enabledValue === 'undefined' ? document.body.classList.contains('wcas-hide-theme-hero') : truthySetting(enabledValue);
    var selector = safeThemeHeroSelector(payload);
    document.body.classList.toggle('wcas-hide-theme-hero', enabled);
    restoreThemeHero();
    if (!enabled) return;
    hideThemeHeroElement(findAccountThemeHero(selector));
  }

  function mobileMoreSheet() {
    var trigger = document.querySelector('.wcas-mobile-more-trigger');
    var sheet = document.getElementById('wcas-mobile-more-sheet');
    if (!trigger || !sheet) return;
    var dock = trigger.closest ? trigger.closest('.wcas-mobile-nav') : document.querySelector('.wcas-mobile-nav');
    function closeSheet() {
      if (typeof sheet.close === 'function' && sheet.open) sheet.close();
      else sheet.removeAttribute('open');
      trigger.setAttribute('aria-expanded', 'false');
      trigger.classList.remove('is-active');
      if (dock) dock.classList.remove('wcas-more-open');
    }
    trigger.addEventListener('click', function () {
      if (sheet.open) { closeSheet(); return; }
      if (typeof sheet.showModal === 'function') sheet.showModal();
      else sheet.setAttribute('open', '');
      trigger.setAttribute('aria-expanded', 'true');
      trigger.classList.add('is-active');
      if (dock) dock.classList.add('wcas-more-open');
    });
    sheet.addEventListener('click', function (event) { if (event.target === sheet) closeSheet(); });
    sheet.addEventListener('close', function () { trigger.setAttribute('aria-expanded', 'false'); trigger.classList.remove('is-active'); if (dock) dock.classList.remove('wcas-more-open'); });
    var closeButton = sheet.querySelector('.wcas-sheet-close');
    if (closeButton) closeButton.addEventListener('click', closeSheet);
    window.addEventListener('resize', function () { if (window.innerWidth > 820) closeSheet(); });
  }

  /*
   * The server knows which endpoint is active, but the dock is also an app-like
   * control.  Exposing an index lets each dock skin animate one shared active
   * indicator without relying on a fragile :has() selector.  It also makes the
   * labels meaningful to screen readers while keeping the visual design free to
   * use an icon-only treatment.
   */
  function mobileDockExperience() {
    var dock = document.querySelector('.wcas-mobile-nav');
    if (!dock) return;
    var items = Array.prototype.slice.call(dock.querySelectorAll('a, button'));
    if (!items.length) return;

    dock.style.setProperty('--wcas-nav-count', items.length);
    dock.classList.add('wcas-mobile-nav--ready');

    function currentItem() {
      return dock.querySelector('a.is-active') || items[0];
    }

    function setIndicator(item) {
      var index = Math.max(0, items.indexOf(item));
      dock.style.setProperty('--wcas-active-index', index);
      dock.style.setProperty('--wcas-active-shift', (index * 100) + '%');
      dock.style.setProperty('--wcas-active-shift-rtl', '-' + (index * 100) + '%');
    }

    items.forEach(function (item, index) {
      item.dataset.wcasDockItem = String(index + 1);
      if (item.tagName === 'A' && item.classList.contains('is-active')) item.setAttribute('aria-current', 'page');
      item.addEventListener('click', function () {
        /* Anchor navigation remains native; this simply provides instant feedback. */
        if (item.tagName === 'A') setIndicator(item);
      });
    });

    setIndicator(currentItem());

    var more = dock.querySelector('.wcas-mobile-more-trigger');
    if (more && 'MutationObserver' in window) {
      new MutationObserver(function () {
        setIndicator(more.classList.contains('is-active') ? more : currentItem());
      }).observe(more, { attributes: true, attributeFilter: ['class'] });
    }
  }

  function renderPreview(payload) {
    if (!app || !payload) return;
    var colors = payload.colors || {};
    Object.keys(colors).forEach(function (key) {
      app.style.setProperty('--hub-' + key, colors[key]);
    });
    if (payload.radius) app.style.setProperty('--hub-radius', payload.radius + 'px');
    if (payload.width) app.style.setProperty('--hub-content-width', payload.width + 'px');
    if (payload.scale) app.style.setProperty('--hub-scale', Math.max(.85, Math.min(1.2, payload.scale / 100)));
    if (payload.template) setClassPrefix(app, 'wcas-experience-', payload.template);
    if (payload.template) setClassPrefix(app, 'wcas-shell-', shellForTemplate(payload.template));
    if (payload.appearance) setClassPrefix(app, 'wcas-appearance-', payload.appearance);
    if (payload.navigation) setClassPrefix(app, 'wcas-nav-', payload.navigation);
    if (payload.mobileNav) setClassPrefix(document.body, 'wcas-mobile-nav-', payload.mobileNav);
    var mobileStyle = payload.mobileNavStyle || payload.mobileNav;
    if (mobileStyle) {
      /* Older saved installs called these dock/rail/notch; keep preview graceful. */
      mobileStyle = { dock: 'solid', rail: 'outline', notch: 'split' }[mobileStyle] || mobileStyle;
      setClassPrefix(document.body, 'wcas-mobile-style-', mobileStyle);
    }
    if (typeof payload.mobileNavLabels !== 'undefined') {
      var labelsOn = payload.mobileNavLabels === true || payload.mobileNavLabels === 'yes' || payload.mobileNavLabels === '1';
      setClassPrefix(document.body, 'wcas-mobile-labels-', labelsOn ? 'yes' : 'no');
    }
    if (payload.mobileIconStyle) setClassPrefix(document.body, 'wcas-mobile-icons-', payload.mobileIconStyle);
    if (payload.mobileActiveStyle) setClassPrefix(document.body, 'wcas-mobile-active-', payload.mobileActiveStyle);
    if (payload.mobileIcons) previewMobileIcons(payload.mobileIcons, payload.template);
    if (payload.labels) previewNavigationLabels(payload.labels);
    if (payload.customLinks) previewCustomLinks(payload.customLinks);
    if (typeof payload.hidePageTitle !== 'undefined') {
      var hidePageTitle = payload.hidePageTitle === true || payload.hidePageTitle === 'yes' || payload.hidePageTitle === '1';
      document.body.classList.toggle('wcas-hide-page-title', hidePageTitle);
      app.classList.toggle('wcas-hide-page-title', hidePageTitle);
      previewThemeTitleVisibility(hidePageTitle);
    }
    if (typeof payload.hideThemeHero !== 'undefined' || typeof payload.hide_theme_hero !== 'undefined' || typeof payload.themeHeroSelector !== 'undefined' || typeof payload.theme_hero_selector !== 'undefined') {
      syncThemeHero(payload);
    }
    if (payload.language) app.setAttribute('dir', payload.language === 'ar' ? 'rtl' : 'ltr');
    if (typeof payload.motion !== 'undefined') {
      app.dataset.wcasMotion = payload.motion ? 'on' : 'off';
      if (payload.motion) revealElements();
    }
    previewText('.wcas-home > .wcas-screen-heading h1', payload.heroTitle || '');
    previewText('.wcas-home > .wcas-screen-heading p', payload.heroDescription || '');
    if (payload.modules) {
      previewVisibility('.wcas-order-focus', payload.modules.order !== false);
      previewVisibility('.wcas-template-stories', payload.modules.story !== false);
      previewVisibility('.wcas-dashboard-actions', payload.modules.actions !== false);
      previewVisibility('.wcas-reward', payload.modules.rewards !== false);
    }
    var name = app.querySelector('.wcas-brand-name');
    var tagline = app.querySelector('.wcas-brand-tagline');
    if (name) {
      if (!name.dataset.wcasOriginal) name.dataset.wcasOriginal = name.textContent;
      name.textContent = payload.brandName || name.dataset.wcasOriginal;
    }
    if (tagline) tagline.textContent = payload.brandTagline || '';
    var mark = app.querySelector('.wcas-brand-mark');
    if (mark) {
      if (!mark.dataset.wcasOriginal) mark.dataset.wcasOriginal = mark.innerHTML;
      if (payload.logoSource === 'upload' && payload.logoUrl) {
        var image = document.createElement('img');
        image.className = 'wcas-site-logo'; image.src = payload.logoUrl; image.alt = '';
        mark.replaceChildren(image);
      } else if (payload.logoSource === 'none') {
        var fallback = document.createElement('span');
        fallback.className = 'wcas-brand-fallback'; fallback.textContent = previewInitial(payload.brandName);
        mark.replaceChildren(fallback);
      } else {
        mark.innerHTML = mark.dataset.wcasOriginal;
      }
    }
  }

  function previewMessaging() {
    window.addEventListener('message', function (event) {
      if (event.origin !== window.location.origin || !event.data || event.data.type !== 'wcas-preview:update') return;
      renderPreview(event.data.payload);
    });
    if (window.parent !== window) {
      window.parent.postMessage({ type: 'wcas-preview:ready' }, window.location.origin);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    app = document.querySelector('.wcas-app');
    revealElements();
    footerDockBehaviour();
    mobileMoreSheet();
    mobileDockExperience();
    previewMessaging();
    syncThemeHero();
    /* Some builders finish their page-title markup immediately after DOM ready. */
    window.setTimeout(function () { syncThemeHero(); }, 180);
  });
}());
