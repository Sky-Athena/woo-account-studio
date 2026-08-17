jQuery(function ($) {
  'use strict';
  var mediaFrame;
  var iframe = document.getElementById('wcas-live-iframe');
  var stage = document.querySelector('.wcas-preview-stage');
  var form = document.getElementById('wcas-studio-form');
  var origin = window.location.origin;

  function field(name) { return form && form.querySelector('[name="wcas_settings[' + name + ']"]'); }
  function value(name, fallback) {
    var input = field(name);
    if (!input) return fallback || '';
    return input.type === 'checkbox' ? input.checked : input.value;
  }
  function selectedTemplate() {
    var input = form && form.querySelector('[name="wcas_settings[experience]"]:checked');
    return input ? input.value : 'commerce';
  }
  function previewLanguage() {
    var mode = value('language_mode', 'auto');
    return mode === 'auto' ? ((window.wcasStudio && window.wcasStudio.autoLanguage) || 'en') : mode;
  }
  function customLinkField(index, key) {
    return form && form.querySelector('[name="wcas_settings[custom_links][' + index + '][' + key + ']"]');
  }
  function customLinkValue(index, key, fallback) {
    var input = customLinkField(index, key);
    if (!input) return fallback || '';
    return input.type === 'checkbox' ? input.checked : input.value;
  }
  function customLinksPayload() {
    var links = [];
    for (var index = 0; index < 6; index += 1) {
      links.push({
        enabled: customLinkValue(index, 'enabled', false),
        label: customLinkValue(index, 'label', ''),
        url: customLinkValue(index, 'url', ''),
        iconSource: customLinkValue(index, 'icon_source', 'builtin'),
        iconName: customLinkValue(index, 'icon_name', 'star'),
        iconify: customLinkValue(index, 'iconify', ''),
        iconUrl: customLinkValue(index, 'icon_url', ''),
        newTab: customLinkValue(index, 'new_tab', false)
      });
    }
    return links;
  }
  function previewPayload() {
    var logoSource = value('logo_source', 'site');
    return {
      template: selectedTemplate(),
      language: previewLanguage(),
      appearance: value('appearance', 'light'), navigation: value('navigation', 'sidebar'), mobileNav: value('mobile_nav', 'dock'), mobileNavStyle: value('mobile_nav_style', 'glass'), mobileNavLabels: value('mobile_nav_labels', true), mobileIconStyle: value('mobile_icon_style', 'line'), mobileActiveStyle: value('mobile_active_style', 'none'), hidePageTitle: value('hide_theme_hero', false), hideThemeHero: value('hide_theme_hero', false), themeHeroSelector: value('theme_hero_selector', ''),
      radius: value('radius', 22), width: value('content_width', 1060), scale: value('font_scale', 100), motion: value('enable_motion', true),
      brandName: value('brand_name', ''), brandTagline: value('brand_tagline', ''), heroTitle: value('hero_title', ''), heroDescription: value('hero_description', ''), logoSource: logoSource,
      logoUrl: logoSource === 'upload' && document.querySelector('#wcas-logo-preview img') ? document.querySelector('#wcas-logo-preview img').src : '',
      mobileIcons: { dashboard: value('mobile_icon_dashboard', 'grid'), orders: value('mobile_icon_orders', 'bag'), library: value('mobile_icon_library', 'download'), addresses: value('mobile_icon_addresses', 'pin'), profile: value('mobile_icon_profile', 'user'), more: value('mobile_icon_more', 'user') },
      labels: { dashboard: value('label_dashboard', ''), orders: value('label_orders', ''), library: value('label_library', ''), addresses: value('label_addresses', ''), profile: value('label_profile', ''), more: value('label_more', '') },
      modules: { order: value('show_order_focus', true), story: value('show_template_story', true), actions: value('show_action_cards', true), rewards: value('show_rewards', true) },
      customLinks: customLinksPayload(),
      colors: { primary: value('primary', '#5e5ce6'), accent: value('accent', '#ff785a'), bg: value('background', '#f5f6fa'), surface: value('surface', '#ffffff'), ink: value('text', '#172036'), sidebar: value('sidebar', '#101523') }
    };
  }
  function sendPreview() {
    if (iframe && iframe.contentWindow) iframe.contentWindow.postMessage({ type: 'wcas-preview:update', payload: previewPayload() }, origin);
  }
  function reloadPreviewContext() {
    if (!iframe || !window.URL) return;
    try {
      var url = new URL(iframe.src, window.location.href);
      url.searchParams.set('wcas-preview-language', previewLanguage());
      url.searchParams.set('wcas-preview-template', selectedTemplate());
      var loader = document.querySelector('.wcas-preview-loader');
      if (loader) loader.classList.remove('is-hidden');
      iframe.src = url.toString();
    } catch (error) { sendPreview(); }
  }
  function selectTemplate(card, applyPalette) {
    if (!card) return;
    document.querySelectorAll('.wcas-template-card').forEach(function (item) { item.classList.toggle('is-selected', item === card); });
    var radio = card.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
    if (applyPalette && card.dataset.palette) {
      try {
        var palette = JSON.parse(card.dataset.palette);
        Object.keys(palette).forEach(function (name) { var input = field(name); if (input) input.value = palette[name]; });
      } catch (error) { /* keep template selectable if a palette is missing */ }
    }
    reloadPreviewContext();
  }
  function bindTemplates() {
    document.querySelectorAll('.wcas-template-card').forEach(function (card) {
      card.addEventListener('click', function (event) { if (event.target.tagName !== 'INPUT') event.preventDefault(); selectTemplate(card, true); });
      card.querySelector('input[type="radio"]').addEventListener('change', function () { selectTemplate(card, true); });
    });
  }
  function bindPreview() {
    document.querySelectorAll('[data-preview-device]').forEach(function (button) {
      button.addEventListener('click', function () {
        document.querySelectorAll('[data-preview-device]').forEach(function (item) { var active = item === button; item.classList.toggle('is-active', active); item.setAttribute('aria-pressed', active ? 'true' : 'false'); });
        if (stage) stage.dataset.device = button.dataset.previewDevice;
      });
    });
    if (iframe) iframe.addEventListener('load', function () { var loader = document.querySelector('.wcas-preview-loader'); if (loader) loader.classList.add('is-hidden'); window.setTimeout(sendPreview, 80); });
  }
  function bindInputs() {
    if (!form) return;
    form.querySelectorAll('input, select, textarea').forEach(function (input) { input.addEventListener('input', sendPreview); input.addEventListener('change', sendPreview); });
    var language = field('language_mode');
    if (language) language.addEventListener('change', reloadPreviewContext);
  }
  function bindLogoPicker() {
    $('#wcas-select-logo').on('click', function (event) {
      event.preventDefault();
      if (!window.wp || !wp.media) return;
      if (mediaFrame) { mediaFrame.open(); return; }
      var strings = (window.wcasStudio && window.wcasStudio.strings) || {};
      mediaFrame = wp.media({ title: strings.selectLogo || 'Select logo', button: { text: strings.useThisLogo || 'Use this logo' }, multiple: false });
      mediaFrame.on('select', function () { var attachment = mediaFrame.state().get('selection').first().toJSON(); $('#wcas-logo-id').val(attachment.id); $('#wcas-logo-preview').html('<img src="' + attachment.url + '" alt="">'); sendPreview(); });
      mediaFrame.open();
    });
    $('#wcas-remove-logo').on('click', function (event) { event.preventDefault(); $('#wcas-logo-id').val(''); $('#wcas-logo-preview').empty(); sendPreview(); });
  }
  window.addEventListener('message', function (event) { if (event.origin === origin && event.data && event.data.type === 'wcas-preview:ready') sendPreview(); });
  bindTemplates(); bindPreview(); bindInputs(); bindLogoPicker();
});
