document.addEventListener('DOMContentLoaded',()=>{

  // Shared theme preference for public pages, member pages and admin pages.
  // The system preference is used on first visit; explicit choices persist across visits.
  const root=document.documentElement;
  const themeKey='atacjl-theme';
  const media=window.matchMedia('(prefers-color-scheme: dark)');
  const themeOrder=['system','light','dark'];
  const nextPreference=current=>themeOrder[(themeOrder.indexOf(current)+1)%themeOrder.length];
  const labelFor=preference=>preference==='system'?'System':preference==='dark'?'Dark':'Light';
  const applyTheme=preference=>{
    const resolved=preference==='system'?(media.matches?'dark':'light'):preference;
    root.dataset.theme=resolved;
    root.dataset.themePreference=preference;
    document.querySelectorAll('[data-theme-icon]').forEach(el=>{el.textContent=resolved==='dark'?'🌙':'☀️';});
    document.querySelectorAll('[data-theme-label]').forEach(el=>{el.textContent=labelFor(preference);});
    document.querySelectorAll('[data-theme-toggle]').forEach(button=>{
      const next=labelFor(nextPreference(preference)).toLowerCase();
      const label=`Color theme: ${labelFor(preference)}. Activate to switch to ${next}.`;
      button.setAttribute('aria-label',label);
      button.setAttribute('title',label);
    });
    const themeColor=document.querySelector('meta[name="theme-color"]');
    if(themeColor)themeColor.setAttribute('content',resolved==='dark'?'#191A18':'#173B59');
  };
  let preference='system';
  try{preference=localStorage.getItem(themeKey)||'system';}catch(_error){/* Theme still works when browser storage is unavailable. */}
  if(!themeOrder.includes(preference))preference='system';
  applyTheme(preference);
  const onSystemThemeChange=()=>{if((root.dataset.themePreference||'system')==='system')applyTheme('system');};
  if(typeof media.addEventListener==='function')media.addEventListener('change',onSystemThemeChange);
  else if(typeof media.addListener==='function')media.addListener(onSystemThemeChange);
  document.querySelectorAll('[data-theme-toggle]').forEach(button=>button.addEventListener('click',()=>{
    preference=nextPreference(root.dataset.themePreference||'system');
    try{localStorage.setItem(themeKey,preference);}catch(_error){/* In-memory theme remains functional. */}
    applyTheme(preference);
  }));

  const navToggle=document.querySelector('[data-nav-toggle]');
  const nav=document.querySelector('[data-nav]');
  if(navToggle&&nav){
    navToggle.addEventListener('click',()=>{
      const open=nav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded',String(open));
      navToggle.setAttribute('aria-label',open?'Close navigation':'Open navigation');
    });
    document.addEventListener('keydown',event=>{if(event.key==='Escape'&&nav.classList.contains('open')){nav.classList.remove('open');navToggle.setAttribute('aria-expanded','false');navToggle.focus();}});
  }
  const userToggle=document.querySelector('.user-menu-toggle');
  const userPanel=document.querySelector('.user-menu-panel');
  if(userToggle&&userPanel){
    userToggle.addEventListener('click',event=>{event.stopPropagation();const open=userPanel.classList.toggle('open');userToggle.setAttribute('aria-expanded',String(open));});
    document.addEventListener('click',event=>{if(!event.target.closest('.user-menu')){userPanel.classList.remove('open');userToggle.setAttribute('aria-expanded','false');}});
    document.addEventListener('keydown',event=>{if(event.key==='Escape'){userPanel.classList.remove('open');userToggle.setAttribute('aria-expanded','false');}});
  }
  const sidebarToggle=document.querySelector('[data-sidebar-toggle]');
  const sidebar=document.getElementById('appSidebar');
  if(sidebarToggle&&sidebar){
    const mobileSidebar=()=>window.matchMedia('(max-width: 1060px)').matches;
    const collapsedKey='atacjl-sidebar-collapsed';
    const sidebarIcon=sidebarToggle.querySelector('[data-sidebar-toggle-icon]');
    const setSidebarButton=(expanded,mobile)=>{
      const label=mobile?(expanded?'Close navigation':'Open navigation'):(expanded?'Collapse sidebar':'Expand sidebar');
      sidebarToggle.setAttribute('aria-expanded',String(expanded));
      sidebarToggle.setAttribute('aria-label',label);
      sidebarToggle.setAttribute('title',label);
      if(sidebarIcon)sidebarIcon.textContent=mobile?'☰':(expanded?'‹':'›');
    };
    const sidebarLinks=[...sidebar.querySelectorAll('.sidebar-link')];
    sidebarLinks.forEach(link=>{
      const label=[...link.childNodes]
        .filter(node=>!(node.nodeType===1&&node.classList.contains('nav-glyph')))
        .map(node=>node.textContent||'').join(' ').replace(/\s+/g,' ').trim();
      if(label){
        if(!link.hasAttribute('title'))link.setAttribute('title',label);
        if(!link.hasAttribute('aria-label'))link.setAttribute('aria-label',label);
      }
    });
    const initMobile=mobileSidebar();
    if(!initMobile){setSidebarButton(root.dataset.sidebarCollapsed!=='true',false);}
    else{setSidebarButton(false,true);}
    sidebarToggle.addEventListener('click',()=>{
      if(mobileSidebar()){
        const open=sidebar.classList.toggle('is-open');
        setSidebarButton(open,true);
        return;
      }
      sidebar.classList.remove('is-open');
      const collapsed=root.dataset.sidebarCollapsed!=='true';
      root.dataset.sidebarCollapsed=String(collapsed);
      try{localStorage.setItem(collapsedKey,String(collapsed));}catch(_error){/* The current-page toggle still works. */}
      setSidebarButton(!collapsed,false);
    });
    document.addEventListener('click',event=>{
      if(mobileSidebar()&&sidebar.classList.contains('is-open')&&!sidebar.contains(event.target)&&!sidebarToggle.contains(event.target)){
        sidebar.classList.remove('is-open');
        setSidebarButton(false,true);
      }
    });
    document.addEventListener('keydown',event=>{
      if(event.key==='Escape'&&sidebar.classList.contains('is-open')){
        sidebar.classList.remove('is-open');
        setSidebarButton(false,true);
        sidebarToggle.focus();
      }
    });
    window.addEventListener('resize',()=>{
      sidebar.classList.remove('is-open');
      setSidebarButton(mobileSidebar()?false:root.dataset.sidebarCollapsed!=='true',mobileSidebar());
    });
  }
  document.querySelectorAll('[data-language-switch]').forEach(select=>select.addEventListener('change',()=>{if(select.value)select.form.requestSubmit();}));
  document.querySelectorAll('[data-translation-editor]').forEach(editor=>{
    const tabs=[...editor.querySelectorAll('[data-language-tab]')];
    const panels=[...editor.querySelectorAll('[data-language-panel]')];
    tabs.forEach(tab=>tab.addEventListener('click',()=>{
      const language=tab.dataset.languageTab;
      tabs.forEach(item=>{const active=item===tab;item.classList.toggle('is-active',active);item.setAttribute('aria-selected',String(active));});
      panels.forEach(panel=>{panel.hidden=panel.dataset.languagePanel!==language;});
      const target=panels.find(panel=>panel.dataset.languagePanel===language);
      if(target){const field=target.querySelector('input,textarea');if(field)field.focus({preventScroll:true});}
    }));
  });
  document.querySelectorAll('[data-confirm]').forEach(button=>button.addEventListener('click',event=>{if(!window.confirm(button.dataset.confirm||'Are you sure?'))event.preventDefault();}));
  document.querySelectorAll('.flash').forEach(flash=>{if(flash.style.position!=='static'){window.setTimeout(()=>flash.remove(),5500);}});

  // The application endpoint is tried first, keeping private API keys on the server.
  // If the local PHP host cannot reach its provider, use the official community mirrors
  // directly from the browser. Only visible public copy is submitted (never form values).
  const translationMirrors = [
    'https://translate.cutie.dating/translate',
    'https://translate.fedilab.app/translate'
  ];

  const fetchWithTimeout = async (url, options, timeoutMs=6500) => {
    const controller = new AbortController();
    const timer = window.setTimeout(() => controller.abort(), timeoutMs);
    try {
      return await fetch(url, {...options, signal: controller.signal});
    } finally {
      window.clearTimeout(timer);
    }
  };

  const parseTranslationResponse = async (response, expectedCount) => {
    let data = {};
    try { data = await response.json(); }
    catch (_error) { throw new Error('the provider returned invalid JSON'); }
    if (!response.ok) throw new Error(data.error || `HTTP ${response.status}`);
    let translated = data.translatedTexts ?? data.translatedText;
    if (typeof translated === 'string') translated = [translated];
    if (!Array.isArray(translated) || translated.length !== expectedCount) {
      throw new Error('the provider returned an incomplete translation');
    }
    return translated.map(value => typeof value === 'string' ? value : '');
  };

  const requestTranslationBatch = async (texts, target, api, skipServer=false) => {
    let serverError = '';
    if (!skipServer && api) {
      try {
        const response = await fetchWithTimeout(api, {
          method: 'POST',
          headers: {'Content-Type':'application/json','Accept':'application/json'},
          body: JSON.stringify({texts, target})
        }, 6000);
        const translatedTexts = await parseTranslationResponse(response, texts.length);
        return {translatedTexts, provider:'configured server API'};
      } catch (error) {
        serverError = error instanceof Error ? error.message : 'server API unavailable';
      }
    }

    const mirrorErrors = [];
    for (const endpoint of translationMirrors) {
      try {
        const response = await fetchWithTimeout(endpoint, {
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json'},
          body:JSON.stringify({q:texts.length===1?texts[0]:texts,source:'auto',target,format:'text'})
        }, 6500);
        const translatedTexts = await parseTranslationResponse(response, texts.length);
        return {translatedTexts, provider:new URL(endpoint).hostname};
      } catch (error) {
        mirrorErrors.push(`${new URL(endpoint).hostname}: ${error instanceof Error ? error.message : 'request failed'}`);
      }
    }
    const details = [];
    if (serverError) details.push(`server API: ${serverError}`);
    if (mirrorErrors.length) details.push(mirrorErrors.join('; '));
    throw new Error((details.join(' · ') || 'no translation endpoint is configured') + '. Check the internet connection or configure a reachable LibreTranslate endpoint/API key in Admin → Site settings.');
  };

  // Used by both automatic page translation and the administrator's connection-test button.
  window.ATACJLTranslation = {
    translateTexts: (texts, target, api) => requestTranslationBatch(texts, target, api, false)
  };

  // Submit a changed language as soon as the visitor selects it.
  document.querySelectorAll('form.language-switch').forEach(form => {
    const select = form.querySelector('[data-language-switch]');
    if (select) select.addEventListener('change', () => form.requestSubmit());
  });

  const targetLanguage = document.documentElement.lang || 'en';
  const explicitlySelectedLanguage = document.querySelector('meta[name="translation-explicit"]')?.content === '1';
  if (targetLanguage !== 'en' || explicitlySelectedLanguage) {
    const api = document.querySelector('meta[name="translation-api"]')?.content;
    const excluded = 'script,style,noscript,textarea,input,select,option,code,pre,[contenteditable="true"],[data-no-translate]';
    const attributeExcluded = 'script,style,noscript,textarea,input,select,option,code,pre,[contenteditable="true"],[data-no-translate]';
    const nodes = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {acceptNode(node) {
      if (!node.nodeValue || !node.nodeValue.trim()) return NodeFilter.FILTER_REJECT;
      const parent = node.parentElement;
      if (!parent || parent.closest(excluded) || parent.closest('[aria-hidden="true"]')) return NodeFilter.FILTER_REJECT;
      return NodeFilter.FILTER_ACCEPT;
    }});
    while (walker.nextNode()) {
      const node = walker.currentNode;
      nodes.push({kind:'text',node,original:node.nodeValue,trimmed:node.nodeValue.trim()});
    }
    // The tab title is outside <body>, so include it explicitly for complete language switching.
    const titleTextNode = document.querySelector('head title')?.firstChild;
    if (titleTextNode?.nodeValue?.trim()) {
      nodes.push({kind:'text',node:titleTextNode,original:titleTextNode.nodeValue,trimmed:titleTextNode.nodeValue.trim()});
    }
    // Translate visible UI attributes such as placeholders and accessible labels.
    document.querySelectorAll('[placeholder],[title],[aria-label],[alt]').forEach(el => {
      if (el.closest(attributeExcluded) || el.hasAttribute('data-no-translate')) return;
      ['placeholder','title','aria-label','alt'].forEach(attr => {
        const value = el.getAttribute(attr);
        if (value && value.trim()) nodes.push({kind:'attr',node:el,attr,original:value,trimmed:value.trim()});
      });
    });
    const showTranslationError = message => {
      let status = document.querySelector('[data-translation-status]');
      if (!status) {
        status = document.createElement('div');
        status.dataset.translationStatus = '';
        status.className = 'translation-status';
        status.setAttribute('role','status');
        status.setAttribute('aria-live','polite');
        document.body.appendChild(status);
      }
      status.textContent = `Automatic translation could not complete: ${message}`;
      status.hidden = false;
    };
    const hideTranslationError = () => {
      const status = document.querySelector('[data-translation-status]');
      if (status) status.hidden = true;
    };
    const applyTranslation = (item, value) => {
      if (item.kind === 'text') item.node.nodeValue = item.original.replace(item.trimmed, value);
      else item.node.setAttribute(item.attr, item.original.replace(item.trimmed, value));
    };
    const cacheKey = `atacjl:translations:${targetLanguage}`;
    let translationCache = {};
    try { translationCache = JSON.parse(localStorage.getItem(cacheKey) || '{}') || {}; }
    catch (_error) { translationCache = {}; }
    const uniqueByText = new Map();
    nodes.forEach(item => {
      const cached = translationCache[item.trimmed];
      if (typeof cached === 'string') { applyTranslation(item,cached); return; }
      if (uniqueByText.has(item.trimmed)) uniqueByText.get(item.trimmed).refs.push(item);
      else uniqueByText.set(item.trimmed,{trimmed:item.trimmed,refs:[item]});
    });
    const phrases = [...uniqueByText.values()];
    const chunks = [];
    let chunk = [];
    let chars = 0;
    phrases.forEach(item => {
      const size = item.trimmed.length;
      if (chunk.length >= 30 || (chars + size) > 8500) {
        if (chunk.length) chunks.push(chunk);
        chunk = []; chars = 0;
      }
      chunk.push(item); chars += size;
    });
    if (chunk.length) chunks.push(chunk);
    let serverEndpointFailed = false;
    const translateChunk = async items => {
      const result = await requestTranslationBatch(items.map(item=>item.trimmed),targetLanguage,api,serverEndpointFailed);
      if (result.provider !== 'configured server API') serverEndpointFailed = true;
      items.forEach((item,index) => {
        const value = result.translatedTexts[index];
        if (!value) return;
        translationCache[item.trimmed] = value;
        item.refs.forEach(ref => applyTranslation(ref,value));
      });
      const cacheKeys = Object.keys(translationCache);
      if (cacheKeys.length > 600) cacheKeys.slice(0,cacheKeys.length-600).forEach(key=>delete translationCache[key]);
      try { localStorage.setItem(cacheKey,JSON.stringify(translationCache)); } catch (_error) { /* Browser storage is optional. */ }
      hideTranslationError();
    };
    (async () => {
      if (!api && translationMirrors.length === 0) { showTranslationError('no translation endpoint is configured'); return; }
      if (!chunks.length) return;
      let cursor = 0;
      let firstError = '';
      const worker = async () => {
        while (cursor < chunks.length) {
          const index = cursor++;
          try { await translateChunk(chunks[index]); }
          catch (error) { if (!firstError) firstError = error instanceof Error ? error.message : 'provider unavailable'; }
        }
      };
      await Promise.all([worker(),worker()]);
      if (firstError) showTranslationError(firstError);
    })();
  }
  // Gentle scroll reveals; disabled for users who request reduced motion.
  if(!window.matchMedia('(prefers-reduced-motion: reduce)').matches){
    document.body.classList.add('reveal-ready');
    const revealTargets=document.querySelectorAll('.section-head,.card,.feature,.about-highlight,.cta,.contact-form-panel,.contact-info-panel');
    revealTargets.forEach(el=>el.classList.add('reveal-on-scroll'));
    if('IntersectionObserver' in window){
      const observer=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-visible');observer.unobserve(entry.target);}}),{threshold:.08});
      revealTargets.forEach(el=>observer.observe(el));
    }else revealTargets.forEach(el=>el.classList.add('is-visible'));
  }

  // Prevent accidental double submission while preserving browser validation.
  document.querySelectorAll('form[data-disable-on-submit]').forEach(form=>form.addEventListener('submit',()=>{const button=form.querySelector('[type="submit"]');if(button&&!button.disabled){button.dataset.originalText=button.textContent;button.disabled=true;button.textContent='Please wait…';}}));
});
