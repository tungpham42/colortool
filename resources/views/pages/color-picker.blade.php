@extends('layouts.app')

@section('title', 'Color Picker')

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-eye-dropper me-2"></i>Color Picker</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div id="colorPreview" class="color-preview mx-auto" style="background-color: #3498db;"></div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Color Picker</label>
                    <div class="text-center">
                        <div id="colorPicker"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hex Color</label>
                    <div class="color-input-group">
                        <input type="text" id="colorInput" value="#3498db" class="color-input">
                        <button class="btn-gradient" onclick="copyToClipboard(document.getElementById('colorInput').value)">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Red</label>
                            <input type="number" id="rgbR" value="52" min="0" max="255" class="form-control"
                                   oninput="updateFromRgb()">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Green</label>
                            <input type="number" id="rgbG" value="152" min="0" max="255" class="form-control"
                                   oninput="updateFromRgb()">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Blue</label>
                            <input type="number" id="rgbB" value="219" min="0" max="255" class="form-control"
                                   oninput="updateFromRgb()">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Color Information</label>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>HEX</th>
                                <td id="infoHex">#3498db</td>
                            </tr>
                            <tr>
                                <th>RGB</th>
                                <td id="infoRgb">rgb(52, 152, 219)</td>
                            </tr>
                            <tr>
                                <th>HSL</th>
                                <td id="infoHsl">hsl(204, 70%, 53%)</td>
                            </tr>
                            <tr>
                                <th>CMYK</th>
                                <td id="infoCmyk">cmyk(76%, 31%, 0%, 14%)</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-palette me-2"></i>Color Harmonies</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Monochromatic</h5>
                    <div class="color-grid" id="monochromaticColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Shades & Tints</h5>
                    <div class="mb-3">
                        <h6>Shades</h6>
                        <div class="d-flex gap-2" id="shadesColors">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Tints</h6>
                        <div class="d-flex gap-2" id="tintsColors">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Complementary</h5>
                    <div class="d-flex gap-3 justify-content-center" id="complementaryColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div>
                    <h5>Analogous Colors</h5>
                    <div class="d-flex gap-2 justify-content-center" id="analogousColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-exchange-alt me-2"></i>Color Converter</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">From Format</label>
                            <select id="fromFormat" class="form-select">
                                <option value="hex">HEX</option>
                                <option value="rgb">RGB</option>
                                <option value="hsl">HSL</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" id="fromValue" class="form-control" value="#3498db">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">To Format</label>
                            <select id="toFormat" class="form-select">
                                <option value="rgb">RGB</option>
                                <option value="hsl">HSL</option>
                                <option value="hex">HEX</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" id="toValue" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <button class="btn-gradient w-100" onclick="convertColor()">
                    <i class="fas fa-sync-alt me-2"></i>Convert Color
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Color harmony functions
    function generateMonochromatic(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        const monochromaticDiv = document.getElementById('monochromaticColors');
        monochromaticDiv.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const lightness = 20 + (i * 15);
            const newHsl = { h: hsl.h, s: hsl.s, l: lightness };
            const newRgb = hslToRgb(newHsl.h, newHsl.s, newHsl.l);
            const newHex = rgbToHex(newRgb.r, newRgb.g, newRgb.b);

            monochromaticDiv.innerHTML += `
                <div class="color-item" style="background-color: ${newHex};" onclick="setColor('${newHex}')">
                    <div class="color-label">
                        <span class="color-hex">${newHex}</span>
                        <span class="color-rgb">${lightness}% light</span>
                    </div>
                </div>
            `;
        }
    }

    function generateShadesTints(baseHex) {
        const rgb = hexToRgb(baseHex);

        // Shades (darker)
        const shadesDiv = document.getElementById('shadesColors');
        shadesDiv.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const factor = 1 - (i * 0.15);
            const shadeRgb = {
                r: Math.round(rgb.r * factor),
                g: Math.round(rgb.g * factor),
                b: Math.round(rgb.b * factor)
            };
            const shadeHex = rgbToHex(shadeRgb.r, shadeRgb.g, shadeRgb.b);

            shadesDiv.innerHTML += `
                <div style="flex: 1; height: 60px; background: ${shadeHex}; border-radius: 5px; cursor: pointer;"
                     onclick="setColor('${shadeHex}')" title="${shadeHex}">
                </div>
            `;
        }

        // Tints (lighter)
        const tintsDiv = document.getElementById('tintsColors');
        tintsDiv.innerHTML = '';

        for (let i = 0; i < 5; i++) {
            const factor = i * 0.15;
            const tintRgb = {
                r: Math.round(rgb.r + (255 - rgb.r) * factor),
                g: Math.round(rgb.g + (255 - rgb.g) * factor),
                b: Math.round(rgb.b + (255 - rgb.b) * factor)
            };
            const tintHex = rgbToHex(tintRgb.r, tintRgb.g, tintRgb.b);

            tintsDiv.innerHTML += `
                <div style="flex: 1; height: 60px; background: ${tintHex}; border-radius: 5px; cursor: pointer;"
                     onclick="setColor('${tintHex}')" title="${tintHex}">
                </div>
            `;
        }
    }

    function generateAnalogous(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        const analogousDiv = document.getElementById('analogousColors');
        analogousDiv.innerHTML = '';

        for (let i = -2; i <= 2; i++) {
            if (i === 0) continue; // Skip base color

            const newHue = (hsl.h + (i * 30) + 360) % 360;
            const newHsl = { h: newHue, s: hsl.s, l: hsl.l };
            const newRgb = hslToRgb(newHsl.h, newHsl.s, newHsl.l);
            const newHex = rgbToHex(newRgb.r, newRgb.g, newRgb.b);

            analogousDiv.innerHTML += `
                <div class="text-center">
                    <div class="color-item" style="background-color: ${newHex}; width: 80px; height: 80px;"
                         onclick="setColor('${newHex}')">
                        <div class="color-label">
                            <span class="color-hex">${newHex}</span>
                        </div>
                    </div>
                    <small>${i < 0 ? i : '+' + i}</small>
                </div>
            `;
        }
    }

    function generateComplementary(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        // Calculate complementary hue (180 degrees opposite)
        const complementaryHue = (hsl.h + 180) % 360;
        const complementaryHsl = { h: complementaryHue, s: hsl.s, l: hsl.l };
        const complementaryRgb = hslToRgb(complementaryHsl.h, complementaryHsl.s, complementaryHsl.l);
        const complementaryHex = rgbToHex(complementaryRgb.r, complementaryRgb.g, complementaryRgb.b);

        const complementaryDiv = document.getElementById('complementaryColors');
        complementaryDiv.innerHTML = `
            <div class="text-center">
                <div class="color-item" style="background-color: ${complementaryHex};" onclick="setColor('${complementaryHex}')">
                    <div class="color-label">
                        <span class="color-hex">${complementaryHex}</span>
                    </div>
                </div>
                <small>Complementary</small>
            </div>
        `;
    }

    // Also add a function to generate split complementary colors (optional enhancement)
    function generateSplitComplementary(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        // Split complementary: base hue, and two hues ±150 degrees
        const hue1 = (hsl.h + 150) % 360;
        const hue2 = (hsl.h + 210) % 360; // or (hsl.h - 150 + 360) % 360

        const hsl1 = { h: hue1, s: hsl.s, l: hsl.l };
        const hsl2 = { h: hue2, s: hsl.s, l: hsl.l };

        const rgb1 = hslToRgb(hsl1.h, hsl1.s, hsl1.l);
        const rgb2 = hslToRgb(hsl2.h, hsl2.s, hsl2.l);

        return {
            color1: rgbToHex(rgb1.r, rgb1.g, rgb1.b),
            color2: rgbToHex(rgb2.r, rgb2.g, rgb2.b)
        };
    }

    // Also add triadic color generation (optional enhancement)
    function generateTriadic(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        // Triadic: three hues 120 degrees apart
        const hue1 = (hsl.h + 120) % 360;
        const hue2 = (hsl.h + 240) % 360;

        const hsl1 = { h: hue1, s: hsl.s, l: hsl.l };
        const hsl2 = { h: hue2, s: hsl.s, l: hsl.l };

        const rgb1 = hslToRgb(hsl1.h, hsl1.s, hsl1.l);
        const rgb2 = hslToRgb(hsl2.h, hsl2.s, hsl2.l);

        return {
            color1: rgbToHex(rgb1.r, rgb1.g, rgb1.b),
            color2: rgbToHex(rgb2.r, rgb2.g, rgb2.b)
        };
    }

    // Update the updateColorInfo function to include complementary generation
    function updateColorInfo(hex) {
        const rgb = hexToRgb(hex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        const cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);

        // Update preview
        document.getElementById('colorPreview').style.backgroundColor = hex;

        // Update inputs
        document.getElementById('colorInput').value = hex;
        document.getElementById('rgbR').value = rgb.r;
        document.getElementById('rgbG').value = rgb.g;
        document.getElementById('rgbB').value = rgb.b;

        // Update info table
        document.getElementById('infoHex').textContent = hex;
        document.getElementById('infoRgb').textContent = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;
        document.getElementById('infoHsl').textContent = `hsl(${Math.round(hsl.h)}, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%)`;
        document.getElementById('infoCmyk').textContent = `cmyk(${Math.round(cmyk.c)}%, ${Math.round(cmyk.m)}%, ${Math.round(cmyk.y)}%, ${Math.round(cmyk.k)}%)`;

        // Generate harmonies
        generateMonochromatic(hex);
        generateShadesTints(hex);
        generateAnalogous(hex);
        generateComplementary(hex); // Add this line
    }

    function updateFromRgb() {
        const r = parseInt(document.getElementById('rgbR').value) || 0;
        const g = parseInt(document.getElementById('rgbG').value) || 0;
        const b = parseInt(document.getElementById('rgbB').value) || 0;

        const hex = rgbToHex(r, g, b);
        updateColorInfo(hex);
    }

    function convertColor() {
        const fromFormat = document.getElementById('fromFormat').value;
        const fromValue = document.getElementById('fromValue').value;
        const toFormat = document.getElementById('toFormat').value;

        let hex, rgb, hsl;

        // Parse input based on format
        switch(fromFormat) {
            case 'hex':
                hex = fromValue;
                rgb = hexToRgb(hex);
                hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
                break;
            case 'rgb':
                const rgbMatch = fromValue.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
                if (rgbMatch) {
                    rgb = { r: parseInt(rgbMatch[1]), g: parseInt(rgbMatch[2]), b: parseInt(rgbMatch[3]) };
                    hex = rgbToHex(rgb.r, rgb.g, rgb.b);
                    hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
                }
                break;
            case 'hsl':
                const hslMatch = fromValue.match(/hsl\((\d+),\s*(\d+)%,\s*(\d+)%\)/);
                if (hslMatch) {
                    hsl = { h: parseInt(hslMatch[1]), s: parseInt(hslMatch[2]), l: parseInt(hslMatch[3]) };
                    rgb = hslToRgb(hsl.h, hsl.s, hsl.l);
                    hex = rgbToHex(rgb.r, rgb.g, rgb.b);
                }
                break;
        }

        // Convert to output format
        let result = '';
        switch(toFormat) {
            case 'hex':
                result = hex;
                break;
            case 'rgb':
                result = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;
                break;
            case 'hsl':
                result = `hsl(${Math.round(hsl.h)}, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%)`;
                break;
        }

        document.getElementById('toValue').value = result;
        showToast('Color converted successfully');
    }

    // Helper functions
    function rgbToHsl(r, g, b) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch(max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }

        return {
            h: Math.round(h * 360),
            s: Math.round(s * 100),
            l: Math.round(l * 100)
        };
    }

    function hslToRgb(h, s, l) {
        h /= 360; s /= 100; l /= 100;
        let r, g, b;

        if (s === 0) {
            r = g = b = l;
        } else {
            const hue2rgb = (p, q, t) => {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1/6) return p + (q - p) * 6 * t;
                if (t < 1/2) return q;
                if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
                return p;
            };

            const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            const p = 2 * l - q;

            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
        }

        return {
            r: Math.round(r * 255),
            g: Math.round(g * 255),
            b: Math.round(b * 255)
        };
    }

    function rgbToCmyk(r, g, b) {
        if (r === 0 && g === 0 && b === 0) {
            return { c: 0, m: 0, y: 0, k: 100 };
        }

        const c = 1 - (r / 255);
        const m = 1 - (g / 255);
        const y = 1 - (b / 255);

        const k = Math.min(c, m, y);

        return {
            c: Math.round(((c - k) / (1 - k)) * 100),
            m: Math.round(((m - k) / (1 - k)) * 100),
            y: Math.round(((y - k) / (1 - k)) * 100),
            k: Math.round(k * 100)
        };
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateColorInfo('#3498db');

        // Update color when input changes
        document.getElementById('colorInput').addEventListener('input', function(e) {
            const color = e.target.value;
            if (/^#[0-9A-F]{6}$/i.test(color) || /^#[0-9A-F]{3}$/i.test(color)) {
                // Ensure full hex format
                let fullHex = color;
                if (color.length === 4) { // #abc format
                    fullHex = '#' + color[1] + color[1] + color[2] + color[2] + color[3] + color[3];
                }
                updateColorInfo(fullHex);
            }
        });

        // Also update when RGB inputs change
        document.getElementById('rgbR').addEventListener('input', updateFromRgb);
        document.getElementById('rgbG').addEventListener('input', updateFromRgb);
        document.getElementById('rgbB').addEventListener('input', updateFromRgb);
    });
</script>
@endpush
