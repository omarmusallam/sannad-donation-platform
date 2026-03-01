import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            fontFamily: {
                // اجعل Cairo هو الافتراضي للموقع (عربي/إنجليزي ممتاز)
                sans: ["Cairo", ...defaultTheme.fontFamily.sans],
            },

            // Design tokens based on CSS variables (Light/Dark)
            colors: {
                bg: "rgb(var(--bg) / <alpha-value>)",
                surface: "rgb(var(--surface) / <alpha-value>)",
                muted: "rgb(var(--muted) / <alpha-value>)",
                border: "rgb(var(--border) / <alpha-value>)",
                text: "rgb(var(--text) / <alpha-value>)",
                subtext: "rgb(var(--subtext) / <alpha-value>)",

                brand: "rgb(var(--brand) / <alpha-value>)",
                brand2: "rgb(var(--brand2) / <alpha-value>)",
                danger: "rgb(var(--danger) / <alpha-value>)",
                success: "rgb(var(--success) / <alpha-value>)",
                warning: "rgb(var(--warning) / <alpha-value>)",
            },

            borderRadius: {
                "2xl": "1.25rem",
                "3xl": "1.75rem",
                "4xl": "2.25rem",
            },

            boxShadow: {
                soft: "0 6px 24px rgba(15, 23, 42, 0.08)",
                softer: "0 4px 18px rgba(15, 23, 42, 0.06)",
                glow: "0 10px 40px rgba(79, 70, 229, 0.18)",
            },
        },
    },
    plugins: [forms],
};
