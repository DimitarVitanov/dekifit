// Works out how to play a video link: YouTube / Vimeo embeds, a direct video
// file, or (anything else) a plain external link.
export function parseVideo(url) {
    let parsed;
    try {
        // Site-relative paths (e.g. /videos/clip.mp4) resolve against the current site.
        parsed = new URL(url, window.location.origin);
    } catch {
        return { type: 'link', url };
    }

    const host = parsed.hostname.replace(/^www\./, '');

    if (host === 'youtu.be' || host.endsWith('youtube.com') || host.endsWith('youtube-nocookie.com')) {
        const id = host === 'youtu.be'
            ? parsed.pathname.slice(1)
            : parsed.searchParams.get('v') || parsed.pathname.match(/\/(?:embed|shorts|live)\/([\w-]+)/)?.[1];
        if (id && /^[\w-]{6,20}$/.test(id)) {
            return {
                type: 'embed',
                src: `https://www.youtube-nocookie.com/embed/${id}?autoplay=1&rel=0`,
                thumbnail: `https://i.ytimg.com/vi/${id}/hqdefault.jpg`,
            };
        }
    }

    if (host === 'vimeo.com' || host === 'player.vimeo.com') {
        const id = parsed.pathname.match(/(\d{6,})/)?.[1];
        if (id) return { type: 'embed', src: `https://player.vimeo.com/video/${id}?autoplay=1`, thumbnail: null };
    }

    if (/\.(mp4|webm|mov|m4v)$/i.test(parsed.pathname)) return { type: 'file', src: url, thumbnail: null };

    return { type: 'link', url };
}
