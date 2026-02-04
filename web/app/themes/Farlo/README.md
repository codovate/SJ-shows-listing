# Farlo Theme

A WordPress starter theme with a Gulp-based build pipeline.

## Requirements

- Node.js (v18 or later recommended)
- npm

## Installation

```bash
cd web/app/themes/Farlo
npm install
```

## Build Commands

| Command | Description |
|---------|-------------|
| `npm run build` | Compile SCSS and JS for production |
| `npm run watch` | Watch files and rebuild on changes |
| `npm run dev` | Alias for watch |

## Project Structure

```
src/
  assets/
    scss/       → SCSS source files
    scripts/    → JavaScript source files
dist/
  theme.css     → Compiled CSS (with sourcemap)
  theme.js      → Compiled JS (with sourcemap)
```

## What the Build Does

**Styles:**
- Compiles SCSS to CSS
- Adds vendor prefixes (Autoprefixer)
- Minifies output (cssnano)
- Generates sourcemaps

**Scripts:**
- Concatenates JS files
- Minifies output (Terser)
- Generates sourcemaps
