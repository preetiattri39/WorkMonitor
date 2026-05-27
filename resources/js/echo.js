import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const protocol = import.meta.env.VITE_REVERB_SCHEME ?? 'http';
const host = import.meta.env.VITE_REVERB_HOST ?? '127.0.0.1';
const port = Number(import.meta.env.VITE_REVERB_PORT ?? 8080);

export const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: protocol === 'https',
    enabledTransports: ['ws', 'wss'],
});
