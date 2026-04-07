

## Make Preloader Background Transparent

**What**: Change the `#intro` preloader overlay background from the current solid color (`var(--bg2)`, which is the dark navy `#010528`) to transparent, so the logo animation video plays over a transparent/clear backdrop.

**Single edit** in `public/mosivus.html`, line ~35:

Change `background:var(--bg2);` to `background:transparent;` in the `#intro` CSS rule.

This keeps the video centered, the fade-out transition, and the 6-second fallback — only the background color changes.

