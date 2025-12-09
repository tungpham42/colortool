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
                        <button id="viewColorDetails" class="btn btn-gradient mt-3 text-align-center" onclick="viewColorDetails(document.getElementById('colorInput').value)">
                            <i class="fas fa-eye me-2"></i>View Color Details
                        </button>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-desktop me-2"></i>Screen Color Picker</h5>
                                <p class="card-text small text-muted">Pick colors from anywhere on your screen</p>
                                <button class="btn-gradient w-100" onclick="startScreenColorPicker()">
                                    <i class="fas fa-crosshairs me-2"></i>Pick from Screen
                                </button>
                                <div class="mt-2 text-center">
                                    <small class="text-muted">Click anywhere on screen to pick color</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-image me-2"></i>Image Color Picker</h5>
                                <p class="card-text small text-muted">Upload an image or paste from clipboard to pick colors from it</p>

                                <!-- File Upload -->
                                <input type="file" id="imageUpload" accept="image/*" class="form-control mb-2" onchange="handleImageUpload(event)">

                                <!-- Paste Area -->
                                <div class="paste-area" id="pasteArea" style="display: none;">
                                    <div class="paste-zone border rounded p-4 text-center" style="border-style: dashed !important;">
                                        <i class="fas fa-paste fa-2x text-muted mb-2"></i>
                                        <p class="mb-1">Paste Image (Ctrl+V or Cmd+V)</p>
                                        <p class="small text-muted">Or drag and drop image here</p>
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div id="imagePreviewContainer" class="d-none">
                                    <div class="position-relative">
                                        <img id="imagePreview" class="img-fluid rounded w-100" style="cursor: crosshair;">
                                        <canvas id="imageCanvas" class="d-none"></canvas>
                                        <div class="text-center mt-2">
                                            <small class="text-muted">Click on image to pick color</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Recently Picked Colors</h4>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2" id="recentColors">
                    <!-- Will be populated by JavaScript -->
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

                <div>
                    <h5>Analogous Colors</h5>
                    <div class="d-flex gap-2 justify-content-center" id="analogousColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Tetradic Colors</h5>
                    <div class="d-flex gap-2 justify-content-center" id="tetradicColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Triadic Colors</h5>
                    <div class="d-flex gap-3 justify-content-center" id="triadicColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Split Complementary</h5>
                    <div class="d-flex gap-3 justify-content-center" id="splitComplementaryColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Complementary</h5>
                    <div class="d-flex gap-3 justify-content-center" id="complementaryColors">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Screen Picker Overlay -->
<div id="screenPickerOverlay" class="screen-picker-overlay d-none">
    <div class="screen-picker-magnifier" id="magnifier">
        <div class="magnifier-display">
            <div class="magnifier-pixel"></div>
            <div class="magnifier-color" id="magnifierColor"></div>
            <div class="magnifier-hex" id="magnifierHex">#000000</div>
        </div>
    </div>
    <div class="screen-picker-crosshair"></div>
    <div class="screen-picker-instruction">
        <div class="alert alert-info">
            <i class="fas fa-crosshairs me-2"></i>
            Click anywhere to pick color • Press ESC to cancel
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .screen-picker-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        cursor: crosshair;
        background: rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(1px);
    }

    .screen-picker-magnifier {
        position: absolute;
        width: 150px;
        height: 150px;
        border: 3px solid #fff;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        pointer-events: none;
        z-index: 10000;
    }

    .magnifier-display {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .magnifier-pixel {
        width: 20px;
        height: 20px;
        border: 2px solid #000;
        margin-bottom: 10px;
    }

    .magnifier-color {
        width: 50px;
        height: 20px;
        border: 1px solid #ccc;
        margin-bottom: 5px;
    }

    .magnifier-hex {
        font-family: monospace;
        font-weight: bold;
        font-size: 14px;
    }

    .screen-picker-crosshair {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 2px solid #fff;
        border-radius: 50%;
        pointer-events: none;
        z-index: 10001;
    }

    .screen-picker-crosshair:before,
    .screen-picker-crosshair:after {
        content: '';
        position: absolute;
        background: #fff;
    }

    .screen-picker-crosshair:before {
        width: 2px;
        height: 100%;
        left: 50%;
        transform: translateX(-50%);
    }

    .screen-picker-crosshair:after {
        width: 100%;
        height: 2px;
        top: 50%;
        transform: translateY(-50%);
    }

    .screen-picker-instruction {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 10002;
    }

    .recent-color-item {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        cursor: pointer;
        border: 2px solid #fff;
        transition: transform 0.2s;
    }

    .recent-color-item:hover {
        transform: scale(1.1);
        border-color: #007bff;
    }

    #imagePreview {
        cursor: crosshair;
        max-width: 100%;
    }

    .paste-zone {
        border: 2px dashed #dee2e6;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .paste-zone:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }

    .paste-zone.drag-over {
        background-color: #d4edda;
        border-color: #28a745;
        border-style: solid !important;
    }

    .paste-instruction {
        font-size: 0.875rem;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
    // Global variables
    let screenPickerActive = false;
    let recentColors = [];
    const MAX_RECENT_COLORS = 20;

    // Check if EyeDropper API is available
    const isEyeDropperSupported = 'EyeDropper' in window;

    // Screen Color Picker Functions using EyeDropper API
    async function startScreenColorPicker() {
        if (!isEyeDropperSupported) {
            showToast('EyeDropper API is not supported in your browser. Try using Chrome, Edge, or Opera on Desktop.', 'error');

            // Fallback: Show instructions for screenshot method
            showToast('Please take a screenshot and upload it instead.', 'info');
            return;
        }

        try {
            // Create EyeDropper instance
            const eyeDropper = new EyeDropper();

            // Show loading state
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Select color from screen...';
            button.disabled = true;

            // Open the eye dropper
            const result = await eyeDropper.open();

            // Get the selected color
            const hex = result.sRGBHex;

            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;

            // Set the color
            setColor(hex);
            showToast('Color picked from screen: ' + hex);

        } catch (error) {
            // Restore button state
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-crosshairs me-2"></i>Pick from Screen';
            button.disabled = false;

            // User cancelled or error occurred
            if (error.name === 'AbortError') {
                showToast('Color picker cancelled', 'info');
            } else {
                console.error('EyeDropper error:', error);
                showToast('Failed to pick color: ' + error.message, 'error');
            }
        }
    }

    // Image Color Picker Functions
    function handleImageUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.match('image.*')) {
            showToast('Please select a valid image file', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            loadImageFromDataURL(e.target.result);
        };
        reader.readAsDataURL(file);
    }

    // NEW: Handle pasted images
    function handlePaste(event) {
        const items = (event.clipboardData || window.clipboardData).items;

        for (let item of items) {
            if (item.type.indexOf('image') !== -1) {
                const blob = item.getAsFile();
                const reader = new FileReader();

                reader.onload = function(e) {
                    loadImageFromDataURL(e.target.result);
                    showToast('Image pasted successfully! Click on the image to pick colors.', 'success');
                };

                reader.readAsDataURL(blob);
                event.preventDefault();
                return;
            }
        }

        showToast('No image found in clipboard', 'warning');
    }

    // NEW: Handle drag and drop
    function handleDragOver(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.closest('.paste-zone').classList.add('drag-over');
    }

    function handleDragLeave(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.closest('.paste-zone').classList.remove('drag-over');
    }

    function handleDrop(event) {
        event.preventDefault();
        event.stopPropagation();
        event.target.closest('.paste-zone').classList.remove('drag-over');

        const files = event.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    loadImageFromDataURL(e.target.result);
                    showToast('Image loaded from drag & drop!', 'success');
                };
                reader.readAsDataURL(file);
            } else {
                showToast('Please drop a valid image file', 'error');
            }
        }
    }

    // Common function to load image from Data URL
    function loadImageFromDataURL(dataURL) {
        const img = document.getElementById('imagePreview');
        const canvas = document.getElementById('imageCanvas');
        const container = document.getElementById('imagePreviewContainer');
        const pasteArea = document.getElementById('pasteArea');

        img.src = dataURL;
        container.classList.remove('d-none');

        // Hide paste area when image is loaded
        if (pasteArea) {
            pasteArea.style.display = 'none';
        }

        // Set up canvas for color picking
        img.onload = function() {
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            // Add click event to image for color picking
            img.addEventListener('click', handleImageColorPick);
        };
    }

    function handleImageColorPick(e) {
        const img = document.getElementById('imagePreview');
        const canvas = document.getElementById('imageCanvas');
        const rect = img.getBoundingClientRect();

        // Calculate click position relative to image
        const scaleX = img.naturalWidth / rect.width;
        const scaleY = img.naturalHeight / rect.height;

        const x = (e.clientX - rect.left) * scaleX;
        const y = (e.clientY - rect.top) * scaleY;

        // Get pixel color from canvas
        const ctx = canvas.getContext('2d');
        const pixelData = ctx.getImageData(x, y, 1, 1).data;

        const hex = rgbToHex(pixelData[0], pixelData[1], pixelData[2]);
        setColor(hex);
        showToast('Color picked from image');
    }

    // NEW: Clear image function
    function clearImage() {
        document.getElementById('imagePreviewContainer').classList.add('d-none');
        document.getElementById('imagePreview').src = '';
        document.getElementById('imageUpload').value = '';

        // Show paste area again
        const pasteArea = document.getElementById('pasteArea');
        if (pasteArea) {
            pasteArea.style.display = 'block';
        }
    }

    // Recent Colors Functions
    function addToRecentColors(hex) {
        // Remove if already exists
        recentColors = recentColors.filter(color => color !== hex);

        // Add to beginning
        recentColors.unshift(hex);

        // Keep only max items
        if (recentColors.length > MAX_RECENT_COLORS) {
            recentColors.pop();
        }

        updateRecentColorsDisplay();

        // Save to localStorage
        localStorage.setItem('recentColors', JSON.stringify(recentColors));
    }

    function updateRecentColorsDisplay() {
        const container = document.getElementById('recentColors');
        container.innerHTML = '';

        recentColors.forEach(hex => {
            const colorItem = document.createElement('div');
            colorItem.className = 'recent-color-item';
            colorItem.style.backgroundColor = hex;
            colorItem.setAttribute('data-bs-toggle', 'tooltip');
            colorItem.setAttribute('data-bs-placement', 'top');
            colorItem.setAttribute('data-bs-title', hex);
            colorItem.title = hex;
            colorItem.onclick = () => setColor(hex);

            new bootstrap.Tooltip(colorItem, {
                delay: { show: 500, hide: 100 }
            });
            container.appendChild(colorItem);
        });
    }

    // Updated setColor function
    function setColor(hex) {
        if (document.getElementById('colorInput')) {
            document.getElementById('colorInput').value = hex;

            // Try to call the more comprehensive update function from color-picker.blade.php
            if (typeof updateColorInfo === 'function') {
                updateColorInfo(hex);
            } else {
                // Fallback to basic preview update
                updateColorPreview(hex);
            }
        }

        if (document.getElementById('fromValue')) {
            document.getElementById('fromValue').value = hex;
            document.getElementById('fromFormat').value = 'hex';
        }

        // Update any color picker instance if it exists
        if (window.pickr && typeof window.pickr.setColor === 'function') {
            window.pickr.setColor(hex);
        }

        showToast('Color set to ' + hex);
        updateColorInfo(hex);
        addToRecentColors(hex);
    }

    // Initialize recent colors from localStorage
    function loadRecentColors() {
        const saved = localStorage.getItem('recentColors');
        if (saved) {
            try {
                recentColors = JSON.parse(saved);
                updateRecentColorsDisplay();
            } catch (e) {
                recentColors = [];
            }
        }
    }

    // Color conversion utility functions
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    function rgbToHex(r, g, b) {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    }

    function rgbToHsl(r, g, b) {
        r /= 255, g /= 255, b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;

        if (max === min) {
            h = s = 0;
        } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
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
        h /= 360;
        s /= 100;
        l /= 100;
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

        let c = 1 - (r / 255);
        let m = 1 - (g / 255);
        let y = 1 - (b / 255);
        const k = Math.min(c, m, y);

        c = (c - k) / (1 - k);
        m = (m - k) / (1 - k);
        y = (y - k) / (1 - k);

        return {
            c: Math.round(c * 100),
            m: Math.round(m * 100),
            y: Math.round(y * 100),
            k: Math.round(k * 100)
        };
    }

    // Existing color harmony and converter functions remain the same
    function generateMonochromatic(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        const monochromaticDiv = document.getElementById('monochromaticColors');
        monochromaticDiv.innerHTML = '';

        for (let i = 0; i < 12; i++) {
            const lightness = 7.5 + (i * 7.5);
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
                <div class="color-item" style="flex: 1; height: 80px; background: ${shadeHex};"
                     onclick="setColor('${shadeHex}')" title="${shadeHex}">
                     <div class="color-label">
                        <span class="color-hex">${shadeHex}</span>
                    </div>
                </div>
            `;
        }

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
                <div class="color-item" style="flex: 1; height: 80px; background: ${tintHex};"
                     onclick="setColor('${tintHex}')" title="${tintHex}">
                        <div class="color-label">
                            <span class="color-hex">${tintHex}</span>
                        </div>
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
            if (i === 0) continue;

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

    function generateTriadic(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        const triadicDiv = document.getElementById('triadicColors');
        triadicDiv.innerHTML = '';

        const angles = [120, 240];
        const labels = ['+120°', '+240°'];

        angles.forEach((angle, index) => {
            const newHue = (hsl.h + angle) % 360;
            const newHsl = { h: newHue, s: hsl.s, l: hsl.l };
            const newRgb = hslToRgb(newHsl.h, newHsl.s, newHsl.l);
            const newHex = rgbToHex(newRgb.r, newRgb.g, newRgb.b);

            triadicDiv.innerHTML += `
                <div class="text-center">
                    <div class="color-item" style="background-color: ${newHex}; width: 80px; height: 80px;" onclick="setColor('${newHex}')">
                        <div class="color-label">
                            <span class="color-hex">${newHex}</span>
                        </div>
                    </div>
                    <small class="text-muted">${labels[index]}</small>
                </div>
            `;
        });
    }

    function generateSplitComplementary(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        const splitCompDiv = document.getElementById('splitComplementaryColors');
        splitCompDiv.innerHTML = '';

        const angles = [150, 210];
        const labels = ['+150°', '+210°'];

        angles.forEach((angle, index) => {
            const newHue = (hsl.h + angle) % 360;
            const newHsl = { h: newHue, s: hsl.s, l: hsl.l };
            const newRgb = hslToRgb(newHsl.h, newHsl.s, newHsl.l);
            const newHex = rgbToHex(newRgb.r, newRgb.g, newRgb.b);

            splitCompDiv.innerHTML += `
                <div class="text-center">
                    <div class="color-item" style="background-color: ${newHex}; width: 80px; height: 80px;" onclick="setColor('${newHex}')">
                        <div class="color-label">
                            <span class="color-hex">${newHex}</span>
                        </div>
                    </div>
                    <small class="text-muted">${labels[index]}</small>
                </div>
            `;
        });
    }

    function generateTetradic(baseHex) {
        const rgb = hexToRgb(baseHex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);

        const tetradicDiv = document.getElementById('tetradicColors');
        tetradicDiv.innerHTML = '';

        const angles = [60, 180, 240];
        const labels = ['+60°', '+180°', '+240°'];

        angles.forEach((angle, index) => {
            const newHue = (hsl.h + angle) % 360;
            const newHsl = { h: newHue, s: hsl.s, l: hsl.l };
            const newRgb = hslToRgb(newHsl.h, newHsl.s, newHsl.l);
            const newHex = rgbToHex(newRgb.r, newRgb.g, newRgb.b);

            tetradicDiv.innerHTML += `
                <div class="text-center">
                    <div class="color-item" style="background-color: ${newHex}; width: 80px; height: 80px;" onclick="setColor('${newHex}')">
                        <div class="color-label">
                            <span class="color-hex">${newHex}</span>
                        </div>
                    </div>
                    <small class="text-muted">${labels[index]}</small>
                </div>
            `;
        });
    }

    function updateColorInfo(hex) {
        const rgb = hexToRgb(hex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        const cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);

        document.getElementById('colorPreview').style.backgroundColor = hex;
        document.getElementById('colorInput').value = hex;
        document.getElementById('rgbR').value = rgb.r;
        document.getElementById('rgbG').value = rgb.g;
        document.getElementById('rgbB').value = rgb.b;

        document.getElementById('infoHex').textContent = hex;
        document.getElementById('infoRgb').textContent = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;
        document.getElementById('infoHsl').textContent = `hsl(${Math.round(hsl.h)}, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%)`;
        document.getElementById('infoCmyk').textContent = `cmyk(${Math.round(cmyk.c)}%, ${Math.round(cmyk.m)}%, ${Math.round(cmyk.y)}%, ${Math.round(cmyk.k)}%)`;

        generateMonochromatic(hex);
        generateShadesTints(hex);
        generateAnalogous(hex);
        generateComplementary(hex);
        generateTriadic(hex);
        generateSplitComplementary(hex);
        generateTetradic(hex);
    }

    function updateFromRgb() {
        const r = parseInt(document.getElementById('rgbR').value) || 0;
        const g = parseInt(document.getElementById('rgbG').value) || 0;
        const b = parseInt(document.getElementById('rgbB').value) || 0;

        const hex = rgbToHex(r, g, b);
        setColor(hex);
    }

    function convertColor() {
        const fromFormat = document.getElementById('fromFormat').value;
        const fromValue = document.getElementById('fromValue').value;
        const toFormat = document.getElementById('toFormat').value;

        let hex, rgb, hsl;

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

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Color copied to clipboard');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            showToast('Failed to copy to clipboard', 'error');
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateColorInfo('#3498db');
        loadRecentColors();

        // Set up paste event listener
        const pasteArea = document.getElementById('pasteArea');
        const pasteZone = document.querySelector('.paste-zone');

        if (pasteArea && pasteZone) {
            // Show paste area initially
            pasteArea.style.display = 'block';

            // Add event listeners for paste
            pasteZone.addEventListener('click', function() {
                this.focus();
                showToast('Press Ctrl+V (Cmd+V) to paste an image, or drag and drop an image here', 'info');
            });

            pasteZone.addEventListener('paste', handlePaste);

            // Add drag and drop events
            pasteZone.addEventListener('dragover', handleDragOver);
            pasteZone.addEventListener('dragleave', handleDragLeave);
            pasteZone.addEventListener('drop', handleDrop);

            // Add global paste listener for when area is focused
            document.addEventListener('paste', function(e) {
                if (e.target.closest('.paste-zone') || document.activeElement === pasteZone) {
                    handlePaste(e);
                }
            });

            // Add keyboard shortcut hint
            pasteZone.setAttribute('title', 'Click here then press Ctrl+V to paste an image');
        }

        // Show browser compatibility warning if needed
        if (!isEyeDropperSupported) {
            const screenPickerCard = document.querySelector('.card-body .card:first-child');
            if (screenPickerCard) {
                const warning = document.createElement('div');
                warning.className = 'alert alert-warning mt-2 small';
                warning.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Note:</strong> Screen color picker requires Chrome, Edge, or Opera on Desktop.
                    For other browsers, use the image upload or paste option.
                `;
                screenPickerCard.querySelector('.card-body').appendChild(warning);
            }
        }

        document.getElementById('colorInput').addEventListener('input', function(e) {
            const color = e.target.value;
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                setColor(color);
            }
        });

        document.getElementById('rgbR').addEventListener('input', updateFromRgb);
        document.getElementById('rgbG').addEventListener('input', updateFromRgb);
        document.getElementById('rgbB').addEventListener('input', updateFromRgb);
    });
</script>
@endpush
