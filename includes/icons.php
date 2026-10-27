<?php
function icon(string $name, int $size = 20, string $class = ''): string {
    $icons = [
        'logo'    => '<path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'dash'    => '<rect x="3" y="3" width="7" height="9" rx="1.5" stroke="currentColor" stroke-width="2" fill="none"/><rect x="14" y="3" width="7" height="5" rx="1.5" stroke="currentColor" stroke-width="2" fill="none"/><rect x="14" y="12" width="7" height="9" rx="1.5" stroke="currentColor" stroke-width="2" fill="none"/><rect x="3" y="16" width="7" height="5" rx="1.5" stroke="currentColor" stroke-width="2" fill="none"/>',
        'devices' => '<path d="M4 4h10v10H4zM14 4h6v16h-6z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
        'auto'    => '<circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M4.9 19.1l2.1-2.1M17 7l2.1-2.1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'stats'   => '<path d="M3 20h18M6 16v-5M11 16V8M16 16v-9M21 16V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'bell'    => '<path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'power'   => '<path d="M12 2v9M18.4 5.6a9 9 0 11-12.8 0" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'plus'    => '<path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'search'  => '<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none"/><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'eye'     => '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" stroke="currentColor" stroke-width="2" fill="none"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" fill="none"/>',
        'eye-off' => '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M1 1l22 22" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'light'   => '<path d="M9 18h6M10 22h4M12 2a7 7 0 00-4 12.7V17h8v-2.3A7 7 0 0012 2z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'thermo'  => '<path d="M14 14.76V4a2 2 0 10-4 0v10.76a4 4 0 104 0z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'lock'    => '<rect x="4" y="11" width="16" height="10" rx="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 11V7a4 4 0 118 0v4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'camera'  => '<path d="M23 7l-7 5 7 5V7zM14 5H3a2 2 0 00-2 2v10a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
        'speaker' => '<path d="M11 5L6 9H2v6h4l5 4V5zM19.07 4.93a10 10 0 010 14.14M15.54 8.46a5 5 0 010 7.07" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'plug'    => '<path d="M9 2v6M15 2v6M6 8h12v3a6 6 0 01-6 6v5M12 17v-0" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'fan'     => '<circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 10c0-3 1-5 1-7M12 14c0 3-1 5-1 7M10 12c-3 0-5 1-7 1M14 12c3 0 5-1 7-1" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'sensor'  => '<circle cx="12" cy="12" r="2" stroke="currentColor" stroke-width="2" fill="none"/><path d="M8 8a6 6 0 000 8M16 8a6 6 0 010 8M5 5a10 10 0 000 14M19 5a10 10 0 010 14" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'trash'   => '<path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'moon'    => '<path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'sun'     => '<circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
        'check'   => '<path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'x'       => '<path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round"/>',
        'arrow'   => '<path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'download'=> '<path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'shield'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
        'clock'   => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" fill="none"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/>',
        'chart'   => '<path d="M3 3v18h18M7 14l4-4 3 3 5-6" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
        'zap'     => '<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" stroke="currentColor" stroke-width="2" fill="none" stroke-linejoin="round"/>',
        'edit'    => '<path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.12 2.12 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>',
    ];
    $path = $icons[$name] ?? $icons['logo'];
    return '<svg class="icon ' . htmlspecialchars($class) . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" aria-hidden="true">' . $path . '</svg>';
}