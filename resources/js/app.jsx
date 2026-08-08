import './bootstrap';
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { route } from 'ziggy-js';

// @routes (Blade directive in resources/views/app.blade.php) declares a top-level
// `const Ziggy = {...}` before this module runs — it is NOT attached to `window`,
// so it must be referenced as the bare identifier here.
window.route = (name, params, absolute, config) => route(name, params, absolute, config ?? Ziggy);

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true });

        return pages[`./Pages/${name}.jsx`];
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
