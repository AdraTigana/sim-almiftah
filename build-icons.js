const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const sizes = [48, 72, 96, 128, 192, 512];
const svgDir = path.join(__dirname, 'public/icons');
const outDir = svgDir;

async function generateIcons() {
    const svg192 = fs.readFileSync(path.join(svgDir, 'icon-192x192.svg'), 'utf-8');
    const svg512 = fs.readFileSync(path.join(svgDir, 'icon-512x512.svg'), 'utf-8');

    for (const size of sizes) {
        // Regular PNG
        const svg = size <= 192 ? svg192.replace('width="192"', `width="${size}"`).replace('height="192"', `height="${size}"`) : svg512.replace('width="512"', `width="${size}"`).replace('height="512"', `height="${size}"`);

        await sharp(Buffer.from(svg))
            .resize(size, size)
            .png()
            .toFile(path.join(outDir, `icon-${size}x${size}.png`));
        console.log(`Generated icon-${size}x${size}.png`);

        // Maskable version (for sizes >= 192)
        if (size >= 192) {
            // For maskable: identical PNG but with a larger safe zone.
            // The SVG already has green bg with centered "م", so same
            // PNG works for maskable too. We just need a separate file.
            await sharp(Buffer.from(svg))
                .resize(size, size)
                .png()
                .toFile(path.join(outDir, `icon-${size}x${size}-maskable.png`));
            console.log(`Generated icon-${size}x${size}-maskable.png`);
        }
    }
}

generateIcons().catch(err => {
    console.error('Icon generation failed:', err);
    process.exit(1);
});
