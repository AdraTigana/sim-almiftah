/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",
    content: [
        './app/Views/**/*.php',
        './public/assets/js/**/*.js',
        './public/sw.js',
    ],

    theme: {
        extend: {
            colors: {
                "primary": "#046C4E",
                "primary-hover": "#03543F",
                "primary-active": "#02402F",
                "primary-container": "#D1FAE5",
                "primary-fixed": "#F0FDFA",
                "primary-fixed-dim": "#D1FAE5",
                "on-primary": "#ffffff",
                "on-primary-container": "#022C22",
                "on-primary-fixed": "#022C22",
                "on-primary-fixed-variant": "#03543F",
                "inverse-primary": "#10B981",

                "secondary": "#D1FAE5",
                "secondary-container": "#F0FDFA",
                "secondary-fixed": "#F0FDFA",
                "secondary-fixed-dim": "#D1FAE5",
                "on-secondary": "#022C22",
                "on-secondary-container": "#03543F",
                "on-secondary-fixed": "#022C22",
                "on-secondary-fixed-variant": "#046C4E",

                "accent": "#D4A017",

                "tertiary": "#059669",
                "tertiary-container": "#D1FAE5",
                "tertiary-fixed": "#D1FAE5",
                "tertiary-fixed-dim": "#A7F3D0",
                "on-tertiary": "#ffffff",
                "on-tertiary-container": "#022C22",
                "on-tertiary-fixed": "#022C22",
                "on-tertiary-fixed-variant": "#03543F",

                "background": "#F4F7F6",
                "on-background": "#022C22",
                "surface": "#FFFFFF",
                "surface-bright": "#FFFFFF",
                "surface-dim": "#E2E8F0",
                "surface-container": "#E2E8F0",
                "surface-container-low": "#F1F5F9",
                "surface-container-lowest": "#FFFFFF",
                "surface-container-high": "#CBD5E1",
                "surface-container-highest": "#94A3B8",
                "on-surface": "#334155",
                "on-surface-variant": "#64748B",
                "inverse-surface": "#334155",
                "inverse-on-surface": "#F4F7F6",

                "outline": "#94A3B8",
                "outline-variant": "#E2E8F0",

                "success": "#22C55E",
                "warning": "#F59E0B",
                "error": "#DC2626",
                "error-container": "#FEE2E2",
                "on-error-container": "#991B1B",
                "on-error": "#ffffff",
                "status-offline": "#DC2626",

                "glass-border": "rgba(255, 255, 255, 0.3)",
                "glass-surface": "rgba(255, 255, 255, 0.7)",
                "emerald-deep": "#02402F",
                "arabic-text": "#022C22",
                "surface-tint": "#046C4E"
            },
            borderRadius: {
                DEFAULT: "0.25rem",
                lg: "0.5rem",
                xl: "0.75rem",
                "2xl": "1rem",
                "3xl": "1rem",
                full: "9999px"
            },
            spacing: {
                base: "4px",
                "margin-mobile": "0px",
                md: "1.5rem",
                gutter: "16px",
                xl: "3rem",
                sm: "1rem",
                lg: "2rem",
                xs: "0.5rem",
                "margin-desktop": "0px"
            },
            fontFamily: {
                heading: ["Poppins"],
                "arabic-display": ["Amiri"],
                "headline-sm": ["Poppins"],
                "headline-md": ["Poppins"],
                "headline-lg": ["Poppins"],
                "body-md": ["Inter"],
                "label-md": ["Inter"],
                "body-lg": ["Inter"]
            },
            fontSize: {
                "arabic-display": ["28px", {"lineHeight": "1.8", "fontWeight": "400"}],
                "headline-sm": ["20px", {"lineHeight": "1.4", "fontWeight": "600"}],
                "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
                "headline-lg": ["32px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "body-md": ["14px", {"lineHeight": "1.5", "fontWeight": "400"}],
                "label-md": ["12px", {"lineHeight": "1.2", "letterSpacing": "0.05em", "fontWeight": "600"}],
                "body-lg": ["16px", {"lineHeight": "1.6", "fontWeight": "400"}]
            }
        }
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/container-queries'),
    ],
}
