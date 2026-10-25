// Date helpers for the portal. Instants (ISO strings from the server) are shown
// in the viewer's own timezone; plain calendar dates (YYYY-MM-DD) are not shifted.

const asDate = value => {
    if (value instanceof Date) return value;
    if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        const [y, m, d] = value.split('-').map(Number);
        return new Date(y, m - 1, d);
    }
    return new Date(value);
};

export const fmtDate = (value, opts = { day: 'numeric', month: 'short', year: 'numeric' }) =>
    value ? asDate(value).toLocaleDateString(undefined, opts) : '';

export const fmtShortDate = value => fmtDate(value, { day: 'numeric', month: 'short' });

export const fmtDay = value => fmtDate(value, { weekday: 'long', day: 'numeric', month: 'long' });

export const fmtTime = value =>
    value ? asDate(value).toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' }) : '';

export const fmtDateTime = value => (value ? `${fmtDate(value)}, ${fmtTime(value)}` : '');

export const timezoneName = () => Intl.DateTimeFormat().resolvedOptions().timeZone;

// Local YYYY-MM-DD (not UTC), for <input type="date"> defaults and day grouping.
export const localDateKey = (value = new Date()) => {
    const d = asDate(value);
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};

export function fromNow(value) {
    if (!value) return '';
    const diff = (asDate(value).getTime() - Date.now()) / 1000;
    const abs = Math.abs(diff);
    const rtf = new Intl.RelativeTimeFormat(undefined, { numeric: 'auto' });
    if (abs < 60) return rtf.format(Math.round(diff), 'second');
    if (abs < 3600) return rtf.format(Math.round(diff / 60), 'minute');
    if (abs < 86400) return rtf.format(Math.round(diff / 3600), 'hour');
    if (abs < 86400 * 30) return rtf.format(Math.round(diff / 86400), 'day');
    return fmtDate(value);
}

export const initials = name =>
    (name || '?')
        .split(/\s+/)
        .slice(0, 2)
        .map(part => part[0]?.toUpperCase() ?? '')
        .join('');
