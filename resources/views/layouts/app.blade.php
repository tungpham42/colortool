<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Color System')</title>
    <meta name="description" content="A comprehensive color management tool for designers and developers. Pick, mix, extract, and lookup colors with advanced tools.">
    <meta name="keywords" content="color picker, color mixer, color extractor, color tools, design tools, hex, rgb, hsl, cmyk">
    <meta name="author" content="Tung Pham">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Color System')">
    @if(request()->routeIs('color.details') && isset($hex))
        <!-- Dynamic OG image for color details page -->
        @php
            $color = '#' . $hex;
            $colorName = $colorName ?? 'Color';
            $imageUrl = "https://singlecolorimage.com/get/${hex}/1200x630";
        @endphp
        <meta property="og:description" content="Explore the color {{ $colorName }} (#{{ strtoupper($hex) }}) - view its hex code, RGB values, color harmonies, and use it in your design projects. A comprehensive color tool for designers and developers.">
        <meta property="og:image" content="{{ $imageUrl }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Color preview for {{ $color }}">
    @else
        <meta property="og:description" content="A comprehensive color management tool for designers and developers. Pick, mix, extract, and lookup colors with advanced tools.">
        <!-- Default OG image for other pages -->
        <meta property="og:image" content="{{ asset('images/og-image.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="@yield('title', 'Color System')">
    @if(request()->routeIs('color.details') && isset($hex))
        <!-- Dynamic Twitter image for color details page -->
        @php
            $color = '#' . $hex;
            $colorName = $colorName ?? 'Color';
            $twitterImageUrl = "https://singlecolorimage.com/get/${hex}/1200x600";
        @endphp
        <meta property="twitter:description" content="Explore the color {{ $colorName }} (#{{ strtoupper($hex) }}) - view its hex code, RGB values, color harmonies, and use it in your design projects. A comprehensive color tool for designers and developers.">
        <meta property="twitter:image" content="{{ $twitterImageUrl }}">
    @else
        <meta property="twitter:description" content="A comprehensive color management tool for designers and developers. Pick, mix, extract, and lookup colors with advanced tools.">
        <!-- Default Twitter image for other pages -->
        <meta property="twitter:image" content="{{ asset('images/twitter-image.png') }}">
    @endif

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Color Picker Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/themes/classic.min.css">

    <!-- Google tag (gtag.js) -->
    <script
      async
      src="https://www.googletagmanager.com/gtag/js?id=G-HHXZSNQ65X"
    ></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());

      gtag("config", "G-HHXZSNQ65X");
    </script>
    <script
      async
      src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3585118770961536"
      crossorigin="anonymous"
    ></script>

    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "t9evh6woav");
    </script>

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar {
            background: white;
            min-height: calc(100vh - 56px);
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: #495057;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover {
            background: #f8f9fa;
            color: #3498db;
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .main-content {
            padding: 30px;
            background: #f8f9fa;
            min-height: calc(100vh - 56px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            font-weight: 600;
            border: none;
        }

        .color-preview {
            width: 100%;
            height: 120px;
            border-radius: 10px;
            border: 3px solid #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 15px;
        }

        .color-input-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .color-input {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: monospace;
            font-size: 16px;
        }

        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            color: white !important;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .color-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .color-item {
            height: 120px;
            border-radius: 10px;
            position: relative;
            cursor: pointer;
            transition: transform 0.2s;
            overflow: hidden;
        }

        .color-item:hover {
            transform: scale(1.05);
        }

        .color-label {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
        }

        .color-hex {
            display: block;
            font-family: monospace;
        }

        .color-rgb {
            font-size: 10px;
            color: #666;
        }

        .harmony-circle {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: conic-gradient(
                #ff0000 0deg 60deg,
                #ffff00 60deg 120deg,
                #00ff00 120deg 180deg,
                #00ffff 180deg 240deg,
                #0000ff 240deg 300deg,
                #ff00ff 300deg 360deg
            );
            position: relative;
            margin: 30px auto;
        }

        .harmony-dot {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 3px solid white;
            transform: translate(-50%, -50%);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                margin-bottom: 20px;
            }

            .main-content {
                padding: 20px;
            }

            .color-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }

        .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loader.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="spinner"></div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-palette me-2"></i>Color System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('color-picker') }}">Color Picker</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('color-mixer') }}">Color Mixer</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('image-extractor') }}">Image Extractor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('color-lookup') }}">Color Lookup</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2 col-md-3 d-none d-md-block sidebar">
                <div class="d-flex flex-column flex-shrink-0 p-3">
                    <h5 class="mb-3 mt-3">Color Tools</h5>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="{{ route('color-picker') }}" class="nav-link {{ request()->routeIs('color-picker') ? 'active' : '' }}">
                                <i class="fas fa-eye-dropper me-2"></i> Color Picker
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('color-mixer') }}" class="nav-link {{ request()->routeIs('color-mixer') ? 'active' : '' }}">
                                <i class="fas fa-blender me-2"></i> Color Mixer
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('image-extractor') }}" class="nav-link {{ request()->routeIs('image-extractor') ? 'active' : '' }}">
                                <i class="fas fa-image me-2"></i> Image Extractor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('color-lookup') }}" class="nav-link {{ request()->routeIs('color-lookup') ? 'active' : '' }}">
                                <i class="fas fa-search me-2"></i> Color Lookup
                            </a>
                        </li>
                    </ul>

                    <hr>
                    <div class="p-3">
                        <h6>Quick Colors</h6>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @foreach(['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c'] as $quickColor)
                                <div class="color-swatch"
                                     style="width: 30px; height: 30px; background: {{ $quickColor }}; border-radius: 5px; cursor: pointer;"
                                     onclick="setColor('{{ $quickColor }}')"
                                     title="{{ $quickColor }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10 col-md-9 main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Color System</h5>
                    <p>A comprehensive color management tool built by <a class="text-light" href="https://tungpham42.github.io" target="_blank">Tung Pham</a> and <a class="text-light" href="https://tanphatdigital.com/" target="_blank">Tan Phat Digital</a></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; {{ date('Y') }} Color System. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.2/dist/pickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom JavaScript -->
    <script>
        // Global functions
        function showLoader() {
            document.getElementById('loader').classList.add('active');
        }

        function hideLoader() {
            document.getElementById('loader').classList.remove('active');
        }

        function showToast(message, type = 'success', duration = 1500) {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();

            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            toastContainer.innerHTML += toastHtml;
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();

            // Automatically hide after the specified duration (default: 1000ms = 1 second)
            setTimeout(() => {
                toast.hide();
            }, duration);

            // Remove after hide
            document.getElementById(toastId).addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showToast('Copied: ' + text);
            }).catch(err => {
                console.error('Copy failed:', err);
                showToast('Copy failed', 'danger');
            });
        }

        function setColor(color) {
            // This function will be used by color swatches
            if (document.getElementById('colorInput')) {
                document.getElementById('colorInput').value = color;

                // Try to call the more comprehensive update function from color-picker.blade.php
                if (typeof updateColorInfo === 'function') {
                    updateColorInfo(color);
                } else {
                    // Fallback to basic preview update
                    updateColorPreview(color);
                }
            }

            // Update any color picker instance if it exists
            if (window.pickr && typeof window.pickr.setColor === 'function') {
                window.pickr.setColor(color);
            }

            showToast('Color set to ' + color);
        }

        function updateColorPreview(color) {
            const preview = document.getElementById('colorPreview');
            if (preview) {
                preview.style.backgroundColor = color;
            }

            // Also update RGB inputs if they exist
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                const rgb = hexToRgb(color);
                const rgbR = document.getElementById('rgbR');
                const rgbG = document.getElementById('rgbG');
                const rgbB = document.getElementById('rgbB');

                if (rgbR) rgbR.value = rgb.r;
                if (rgbG) rgbG.value = rgb.g;
                if (rgbB) rgbB.value = rgb.b;

                // Update color info table if it exists
                updateColorInfoTable(color, rgb);
            }
        }

        // Helper function to update color info table
        function updateColorInfoTable(hex, rgb) {
            if (!rgb) {
                rgb = hexToRgb(hex);
            }

            // Update HEX
            const infoHex = document.getElementById('infoHex');
            if (infoHex) infoHex.textContent = hex;

            // Update RGB
            const infoRgb = document.getElementById('infoRgb');
            if (infoRgb) infoRgb.textContent = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;

            // Update HSL (if we have the function)
            if (typeof rgbToHsl === 'function') {
                const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
                const infoHsl = document.getElementById('infoHsl');
                if (infoHsl) infoHsl.textContent = `hsl(${Math.round(hsl.h)}, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%)`;

                // Update CMYK if we have the function
                if (typeof rgbToCmyk === 'function') {
                    const cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);
                    const infoCmyk = document.getElementById('infoCmyk');
                    if (infoCmyk) infoCmyk.textContent = `cmyk(${Math.round(cmyk.c)}%, ${Math.round(cmyk.m)}%, ${Math.round(cmyk.y)}%, ${Math.round(cmyk.k)}%)`;
                }
            }
        }

        function hexToRgb(hex) {
            // Remove # if present
            hex = hex.replace(/^#/, '');

            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);

            return { r, g, b };
        }

        function rgbToHex(r, g, b) {
            const componentToHex = (c) => {
                const hex = c.toString(16);
                return hex.length === 1 ? '0' + hex : hex;
            };

            return '#' + componentToHex(r) + componentToHex(g) + componentToHex(b);
        }

        function rgbToHsl(r, g, b) {
            r /= 255;
            g /= 255;
            b /= 255;

            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0; // achromatic
            } else {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

                switch (max) {
                    case r:
                        h = (g - b) / d + (g < b ? 6 : 0);
                        break;
                    case g:
                        h = (b - r) / d + 2;
                        break;
                    case b:
                        h = (r - g) / d + 4;
                        break;
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

        function calculateBrightness(hex) {
            const rgb = hexToRgb(hex);
            return (0.299 * rgb.r + 0.587 * rgb.g + 0.114 * rgb.b) / 255;
        }

        function viewColorDetails(hex) {
            if (/^#[0-9A-F]{6}$/i.test(hex)) {
                const cleanHex = hex.substring(1).toUpperCase();
                window.location.href = `/` + cleanHex;
            } else {
                showToast('Please enter a valid HEX color code.');
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize color picker if element exists
            if (document.getElementById('colorPicker')) {
                window.pickr = Pickr.create({
                    el: '#colorPicker',
                    theme: 'classic',
                    default: '#3498db',
                    swatches: [
                        '#3498db', '#e74c3c', '#2ecc71', '#f39c12',
                        '#9b59b6', '#1abc9c', '#34495e', '#e67e22'
                    ],
                    components: {
                        preview: true,
                        opacity: true,
                        hue: true,
                        interaction: {
                            hex: true,
                            rgba: true,
                            hsva: true,
                            input: true,
                            clear: true,
                            save: true
                        }
                    }
                });

                window.pickr.on('change', (color) => {
                    const hexColor = color.toHEXA().toString();
                    document.getElementById('colorInput').value = hexColor;

                    // Try to call the comprehensive update function
                    if (typeof updateColorInfo === 'function') {
                        updateColorInfo(hexColor);
                    } else {
                        updateColorPreview(hexColor);
                    }
                });
            }

            // Auto-update preview when typing hex color
            const colorInput = document.getElementById('colorInput');
            if (colorInput) {
                let typingTimer;
                colorInput.addEventListener('input', function(e) {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        const color = e.target.value;
                        if (/^#[0-9A-F]{6}$/i.test(color)) {
                            if (typeof updateColorInfo === 'function') {
                                updateColorInfo(color);
                            } else {
                                updateColorPreview(color);
                            }

                            // Update picker if it exists
                            if (window.pickr && typeof window.pickr.setColor === 'function') {
                                window.pickr.setColor(color);
                            }
                        }
                    }, 300);
                });
            }
        });

        // Form submission handler
        document.addEventListener('submit', function(e) {
            if (e.target.matches('form[data-ajax]')) {
                e.preventDefault();
                showLoader();

                const form = e.target;
                const formData = new FormData(form);

                fetch(form.action, {
                    method: form.method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    hideLoader();
                    if (data.success) {
                        showToast(data.message || 'Operation successful');
                        if (data.redirect) {
                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);
                        }
                        if (data.update) {
                            // Update specific element
                            Object.keys(data.update).forEach(selector => {
                                const element = document.querySelector(selector);
                                if (element) {
                                    element.innerHTML = data.update[selector];
                                }
                            });
                        }
                    } else {
                        showToast(data.message || 'Operation failed', 'danger');
                    }
                })
                .catch(error => {
                    hideLoader();
                    showToast('An error occurred: ' + error.message, 'danger');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
