<svg {{ $attributes }} viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <!-- Background vibrant brand gradient -->
        <linearGradient id="sembark-brand-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#4F46E5" />
            <stop offset="55%" stop-color="#4338CA" />
            <stop offset="100%" stop-color="#2563EB" />
        </linearGradient>

        <!-- Cyan to sky accent gradient for the central link bridge -->
        <linearGradient id="sembark-accent-grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#38BDF8" />
            <stop offset="100%" stop-color="#818CF8" />
        </linearGradient>

        <!-- Subtle depth drop shadow -->
        <filter id="sembark-icon-shadow" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="2" stdDeviation="2.5" flood-color="#1E1B4B" flood-opacity="0.3" />
        </filter>
    </defs>

    <!-- Squircle badge container -->
    <rect width="100" height="100" rx="24" fill="url(#sembark-brand-grad)" />

    <!-- Glossy inner stroke -->
    <rect x="1" y="1" width="98" height="98" rx="23" stroke="white" stroke-opacity="0.22" stroke-width="2" fill="none" />

    <!-- Centered Interlocking URL Shortener Link Emblem -->
    <g transform="translate(50, 50) rotate(-45)" filter="url(#sembark-icon-shadow)">
        <!-- Left Link Loop -->
        <path d="M -5 -13 H -15 A 13 13 0 0 0 -15 13 H -5"
              stroke="white"
              stroke-width="7"
              stroke-linecap="round"
              stroke-linejoin="round"
              fill="none" />

        <!-- Right Link Loop -->
        <path d="M 5 -13 H 15 A 13 13 0 0 1 15 13 H 5"
              stroke="white"
              stroke-width="7"
              stroke-linecap="round"
              stroke-linejoin="round"
              fill="none" />

        <!-- Center Link Bridge (URL shortener connection) -->
        <line x1="-10" y1="0" x2="10" y2="0"
              stroke="url(#sembark-accent-grad)"
              stroke-width="7"
              stroke-linecap="round" />

        <!-- Subtle Core Link Pulses -->
        <circle cx="-15" cy="0" r="2.2" fill="#38BDF8" />
        <circle cx="15" cy="0" r="2.2" fill="#38BDF8" />
    </g>
</svg>