/* Protección común para formularios, jQuery y fetch del mismo origen. */
(function () {
    'use strict';
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) return;
    function local(url) { try { return new URL(url || location.href, location.href).origin === location.origin; } catch (_) { return false; } }
    function unsafe(method) { return !['GET','HEAD','OPTIONS'].includes(String(method || 'GET').toUpperCase()); }
    function protectForms() {
        document.querySelectorAll('form').forEach(form => {
            if (!local(form.action) || !unsafe(form.method)) return;
            let input = form.querySelector('input[name="_csrf"]');
            if (!input) { input=document.createElement('input');input.type='hidden';input.name='_csrf';form.appendChild(input); }
            input.value=token;
        });
    }
    protectForms();new MutationObserver(protectForms).observe(document.body,{childList:true,subtree:true});
    if (window.jQuery) window.jQuery.ajaxPrefilter(function (options, original, xhr) {
        if (local(options.url) && unsafe(options.type)) xhr.setRequestHeader('X-CSRF-Token',token);
    });
    const originalFetch=window.fetch;
    window.fetch=function(input,init) {
        const options=Object.assign({},init);
        const request=input instanceof Request;
        if(local(request?input.url:input) && unsafe(options.method || (request?input.method:'GET'))) {
            const headers=new Headers(options.headers || (request?input.headers:undefined));headers.set('X-CSRF-Token',token);options.headers=headers;
        }
        return originalFetch.call(this,input,options);
    };
})();
