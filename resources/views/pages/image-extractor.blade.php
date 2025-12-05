@extends('layouts.app')

@section('title', 'Image Color Extractor')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-image me-2"></i>Upload Image</h4>
            </div>
            <div class="card-body">
                <form id="uploadForm" enctype="multipart/form-data" data-ajax>
                    @csrf
                    <div class="mb-4">
                        <div class="upload-area" id="uploadArea"
                             onclick="document.getElementById('imageInput').click()">
                            <div class="text-center py-5">
                                <i class="fas fa-cloud-upload-alt fa-4x text-muted mb-3"></i>
                                <h4>Drag & Drop or Click to Upload</h4>
                                <p class="text-muted mb-0">Supports JPG, PNG, GIF (Max 5MB)</p>
                                <small class="text-muted">Recommended size: 800x600px or larger</small>
                            </div>
                        </div>
                        <input type="file" id="imageInput" name="image" accept="image/*" class="d-none"
                               onchange="previewImage(this)">
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Number of Colors to Extract</label>
                                <input type="range" id="colorCount" name="color_count" class="form-range"
                                       min="3" max="12" value="5"
                                       oninput="document.getElementById('countValue').textContent = this.value">
                                <div class="d-flex justify-content-between">
                                    <small>3</small>
                                    <span id="countValue" class="fw-bold">5</span>
                                    <small>12</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Color Accuracy</label>
                                <select id="accuracy" name="accuracy" class="form-select">
                                    <option value="low">Low (Faster)</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High (Slower)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn-gradient flex-grow-1">
                            <i class="fas fa-magic me-2"></i>Extract Colors
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearImage()">
                            <i class="fas fa-times me-2"></i>Clear
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-palette me-2"></i>Extracted Color Palette</h4>
            </div>
            <div class="card-body">
                <div id="colorPalette" class="color-grid">
                    <!-- Colors will be populated here -->
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-palette fa-3x mb-3"></i>
                        <h5>No colors extracted yet</h5>
                        <p>Upload an image to extract its dominant colors</p>
                    </div>
                </div>

                <div class="mt-4" id="paletteInfo" style="display: none;">
                    <h5>Palette Information</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Color</th>
                                    <th>HEX</th>
                                    <th>RGB</th>
                                    <th>Percentage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paletteTable">
                                <!-- Table rows will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-eye me-2"></i>Image Preview</h4>
            </div>
            <div class="card-body text-center">
                <div id="imagePreview" class="mb-3">
                    <img id="previewImage" src="" alt="Preview" class="img-fluid rounded" style="display: none; max-height: 300px;">
                    <div id="noPreview" class="text-muted py-5">
                        <i class="fas fa-image fa-4x mb-3"></i>
                        <p>No image selected</p>
                    </div>
                </div>

                <div class="mb-3" id="imageInfo" style="display: none;">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <small class="text-muted d-block">Dimensions</small>
                                <strong id="imageDimensions">-</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 border rounded">
                                <small class="text-muted d-block">Size</small>
                                <strong id="imageSize">-</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-star me-2"></i>Sample Images</h4>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach([
                        ['nature.jpg', 'Nature'],
                        ['art.jpg', 'Art'],
                        ['architecture.jpg', 'Architecture'],
                        ['food.jpg', 'Food'],
                        ['portrait.jpg', 'Portrait']
                    ] as $sample)
                        <div class="col-6">
                            <div class="sample-image cursor-pointer" onclick="loadSampleImage('{{ $sample[0] }}')">
                                <div class="ratio ratio-1x1 mb-2">
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-2x text-muted"></i>
                                    </div>
                                </div>
                                <small class="text-center d-block">{{ $sample[1] }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    <button class="btn btn-outline-primary w-100" onclick="loadRandomImage()">
                        <i class="fas fa-random me-2"></i>Load Random Image
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-download me-2"></i>Export Palette</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Export Format</label>
                    <select id="exportFormat" class="form-select">
                        <option value="json">JSON</option>
                        <option value="css">CSS Variables</option>
                        <option value="scss">SCSS Variables</option>
                        <option value="tailwind">Tailwind Config</option>
                        <option value="csv">CSV</option>
                    </select>
                </div>

                <div class="mb-3">
                    <textarea id="exportOutput" class="form-control" rows="4" readonly placeholder="Export will appear here"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn-gradient flex-grow-1" onclick="exportPalette()">
                        <i class="fas fa-file-export me-2"></i>Generate Export
                    </button>
                    <button class="btn btn-outline-secondary" onclick="copyExport()" id="copyBtn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>

                <div class="mt-3">
                    <button class="btn btn-outline-success w-100" onclick="savePalette()">
                        <i class="fas fa-save me-2"></i>Save to My Palettes
                    </button>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Recent Extractions</h4>
            </div>
            <div class="card-body">
                <div id="recentExtractions">
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <p>No recent extractions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .upload-area {
        border: 3px dashed #dee2e6;
        border-radius: 15px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.3s;
    }

    .upload-area:hover {
        border-color: #3498db;
        background: #e3f2fd;
    }

    .upload-area.dragover {
        border-color: #2ecc71;
        background: #d4edda;
    }

    .sample-image {
        transition: transform 0.2s;
    }

    .sample-image:hover {
        transform: translateY(-5px);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .color-bar {
        height: 20px;
        border-radius: 10px;
        position: relative;
        overflow: hidden;
    }

    .color-bar-fill {
        height: 100%;
        transition: width 0.3s;
    }

    .recent-item {
        padding: 10px;
        border-bottom: 1px solid #dee2e6;
        transition: background 0.2s;
    }

    .recent-item:hover {
        background: #f8f9fa;
    }

    .recent-colors {
        display: flex;
        height: 20px;
        border-radius: 4px;
        overflow: hidden;
    }

    .recent-color {
        flex: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    let extractedColors = [];
    let currentImage = null;

    // Drag and drop functionality
    const uploadArea = document.getElementById('uploadArea');

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            handleImageFile(files[0]);
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            handleImageFile(input.files[0]);
        }
    }

    function handleImageFile(file) {
        // Validate file
        if (!file.type.startsWith('image/')) {
            showToast('Please select an image file', 'danger');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showToast('Image size should be less than 5MB', 'danger');
            return;
        }

        currentImage = file;

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewImage');
            preview.src = e.target.result;
            preview.style.display = 'block';
            document.getElementById('noPreview').style.display = 'none';

            // Get image dimensions
            const img = new Image();
            img.onload = function() {
                document.getElementById('imageDimensions').textContent =
                    `${this.width} × ${this.height}`;
                document.getElementById('imageInfo').style.display = 'block';
            };
            img.src = e.target.result;

            // Show file size
            const sizeInKB = Math.round(file.size / 1024);
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(1);
            document.getElementById('imageSize').textContent =
                sizeInKB < 1024 ? `${sizeInKB} KB` : `${sizeInMB} MB`;
        };
        reader.readAsDataURL(file);
    }

    function clearImage() {
        document.getElementById('imageInput').value = '';
        document.getElementById('previewImage').style.display = 'none';
        document.getElementById('noPreview').style.display = 'block';
        document.getElementById('imageInfo').style.display = 'none';
        currentImage = null;
        clearColors();
        showToast('Image cleared');
    }

    function clearColors() {
        extractedColors = [];
        document.getElementById('colorPalette').innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="fas fa-palette fa-3x mb-3"></i>
                <h5>No colors extracted yet</h5>
                <p>Upload an image to extract its dominant colors</p>
            </div>
        `;
        document.getElementById('paletteInfo').style.display = 'none';
        document.getElementById('exportOutput').value = '';
    }

    // Form submission
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!currentImage) {
            showToast('Please select an image first', 'warning');
            return;
        }

        showLoader();

        const formData = new FormData(this);
        if (currentImage) {
            formData.append('image', currentImage);
        }

        fetch('{{ route("api.extract-colors") }}', {
            method: 'POST',
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
                extractedColors = data.colors;
                displayExtractedColors(data.colors);
                showToast('Successfully extracted ' + data.colors.length + ' colors');

                // Save to recent extractions
                saveRecentExtraction(data.colors, data.image_url);
            } else {
                showToast(data.message || 'Extraction failed', 'danger');
            }
        })
        .catch(error => {
            hideLoader();
            showToast('Error: ' + error.message, 'danger');
            console.error('Extraction error:', error);
        });
    });

    function displayExtractedColors(colors) {
        const paletteDiv = document.getElementById('colorPalette');
        const tableBody = document.getElementById('paletteTable');

        // Clear existing content
        paletteDiv.innerHTML = '';
        tableBody.innerHTML = '';

        // Create color swatches
        colors.forEach((color, index) => {
            // Color swatch for grid
            const swatch = document.createElement('div');
            swatch.className = 'color-item';
            swatch.style.backgroundColor = color.hex;
            swatch.onclick = () => setColor(color.hex);

            const label = document.createElement('div');
            label.className = 'color-label';
            label.innerHTML = `
                <span class="color-hex">${color.hex}</span>
                <span class="color-rgb">${color.percentage}%</span>
            `;

            swatch.appendChild(label);
            paletteDiv.appendChild(swatch);

            // Table row for detailed info
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div style="width: 30px; height: 30px; background: ${color.hex}; border-radius: 4px;"></div>
                </td>
                <td><code>${color.hex}</code></td>
                <td>rgb(${color.rgb.r}, ${color.rgb.g}, ${color.rgb.b})</td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="color-bar flex-grow-1 me-2" style="background: #f0f0f0;">
                            <div class="color-bar-fill" style="width: ${color.percentage}%; background: ${color.hex};"></div>
                        </div>
                        <span>${color.percentage}%</span>
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-1" onclick="setColor('${color.hex}')">
                        <i class="fas fa-eye-dropper"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="copyToClipboard('${color.hex}')">
                        <i class="fas fa-copy"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });

        // Show palette info
        document.getElementById('paletteInfo').style.display = 'block';
    }

    function saveRecentExtraction(colors, imageUrl) {
        let recent = JSON.parse(localStorage.getItem('recentExtractions') || '[]');

        recent.unshift({
            colors: colors.slice(0, 5).map(c => c.hex),
            count: colors.length,
            timestamp: new Date().toLocaleString(),
            image: imageUrl || ''
        });

        // Keep only last 10
        recent = recent.slice(0, 10);
        localStorage.setItem('recentExtractions', JSON.stringify(recent));

        updateRecentExtractionsDisplay();
    }

    function updateRecentExtractionsDisplay() {
        const recent = JSON.parse(localStorage.getItem('recentExtractions') || '[]');
        const container = document.getElementById('recentExtractions');

        if (recent.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p>No recent extractions</p>
                </div>
            `;
            return;
        }

        let html = '';
        recent.forEach((extraction, index) => {
            html += `
                <div class="recent-item ${index < recent.length - 1 ? 'border-bottom' : ''}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <small class="text-muted">${extraction.timestamp}</small>
                        <span class="badge bg-primary">${extraction.count} colors</span>
                    </div>
                    <div class="recent-colors mb-2">
                        ${extraction.colors.map(color => `
                            <div class="recent-color" style="background: ${color};" title="${color}"></div>
                        `).join('')}
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary flex-grow-1"
                                onclick="loadRecentExtraction(${index})">
                            <i class="fas fa-redo me-1"></i>Reload
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                                onclick="deleteRecentExtraction(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function loadRecentExtraction(index) {
        const recent = JSON.parse(localStorage.getItem('recentExtractions') || '[]');
        if (recent[index]) {
            // Simulate loading the extraction
            extractedColors = recent[index].colors.map(hex => ({
                hex: hex,
                rgb: hexToRgb(hex),
                percentage: 100 / recent[index].colors.length
            }));

            displayExtractedColors(extractedColors);
            showToast('Loaded recent extraction');
        }
    }

    function deleteRecentExtraction(index) {
        let recent = JSON.parse(localStorage.getItem('recentExtractions') || '[]');
        recent.splice(index, 1);
        localStorage.setItem('recentExtractions', JSON.stringify(recent));
        updateRecentExtractionsDisplay();
        showToast('Extraction deleted');
    }

    // Sample images
    function loadSampleImage(filename) {
        // In a real app, this would load from server
        // For demo, we'll create a placeholder
        const sampleImages = {
            'nature.jpg': '#4CAF50',
            'art.jpg': '#FF9800',
            'architecture.jpg': '#2196F3',
            'food.jpg': '#F44336',
            'portrait.jpg': '#9C27B0'
        };

        const color = sampleImages[filename] || '#3498db';

        // Create a sample color palette based on the theme
        const baseColor = color;
        const colors = generateSamplePalette(baseColor);

        extractedColors = colors;
        displayExtractedColors(colors);

        // Update preview
        document.getElementById('previewImage').src = `https://placehold.co/600x400/${color.replace('#', '')}/ffffff?text=${filename}`;
        document.getElementById('previewImage').style.display = 'block';
        document.getElementById('noPreview').style.display = 'none';
        document.getElementById('imageInfo').style.display = 'block';
        document.getElementById('imageDimensions').textContent = '600 × 400';
        document.getElementById('imageSize').textContent = 'Sample';

        showToast(`Loaded sample: ${filename.split('.')[0]}`);
    }

    function loadRandomImage() {
        const samples = ['nature.jpg', 'art.jpg', 'architecture.jpg', 'food.jpg', 'portrait.jpg'];
        const randomSample = samples[Math.floor(Math.random() * samples.length)];
        loadSampleImage(randomSample);
    }

    function generateSamplePalette(baseColor) {
        const rgb = hexToRgb(baseColor);
        const colors = [];

        // Generate 5 related colors
        for (let i = 0; i < 5; i++) {
            const factor = 0.8 + (i * 0.1);
            const newRgb = {
                r: Math.min(255, Math.round(rgb.r * factor)),
                g: Math.min(255, Math.round(rgb.g * factor)),
                b: Math.min(255, Math.round(rgb.b * factor))
            };
            const newHex = rgbToHex(newRgb.r, newRgb.g, newRgb.b);

            colors.push({
                hex: newHex,
                rgb: newRgb,
                percentage: (100 / 5) * (5 - i) // First color has highest percentage
            });
        }

        return colors;
    }

    // Export functionality
    function exportPalette() {
        if (extractedColors.length === 0) {
            showToast('No colors to export', 'warning');
            return;
        }

        const format = document.getElementById('exportFormat').value;
        let output = '';

        switch (format) {
            case 'json':
                output = JSON.stringify(extractedColors, null, 2);
                break;

            case 'css':
                output = `/* Color Palette from Image */\n:root {\n`;
                extractedColors.forEach((color, index) => {
                    output += `  --color-${index + 1}: ${color.hex};\n`;
                });
                output += `}`;
                break;

            case 'scss':
                output = `// Color Palette from Image\n`;
                extractedColors.forEach((color, index) => {
                    output += `$color-${index + 1}: ${color.hex};\n`;
                });
                output += `\n// Usage: background: $color-1;`;
                break;

            case 'tailwind':
                output = `// Tailwind color palette\ntheme: {\n  extend: {\n    colors: {\n`;
                extractedColors.forEach((color, index) => {
                    output += `      'image-${index + 1}': '${color.hex}',\n`;
                });
                output += `    }\n  }\n}`;
                break;

            case 'csv':
                output = 'HEX,RGB,Percentage\n';
                extractedColors.forEach(color => {
                    output += `${color.hex},rgb(${color.rgb.r},${color.rgb.g},${color.rgb.b}),${color.percentage}%\n`;
                });
                break;
        }

        document.getElementById('exportOutput').value = output;
        showToast(`Exported in ${format.toUpperCase()} format`);
    }

    function copyExport() {
        const output = document.getElementById('exportOutput');
        if (output.value) {
            copyToClipboard(output.value);
        } else {
            showToast('Generate export first', 'warning');
        }
    }

    function savePalette() {
        if (extractedColors.length === 0) {
            showToast('No palette to save', 'warning');
            return;
        }

        const paletteName = prompt('Enter a name for this palette:', 'Image Palette ' + new Date().toLocaleDateString());
        if (paletteName) {
            let savedPalettes = JSON.parse(localStorage.getItem('savedPalettes') || '[]');

            savedPalettes.push({
                name: paletteName,
                colors: extractedColors,
                timestamp: new Date().toISOString()
            });

            localStorage.setItem('savedPalettes', JSON.stringify(savedPalettes));
            showToast(`Palette "${paletteName}" saved successfully`);
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateRecentExtractionsDisplay();

        // Set up form submission
        document.getElementById('uploadForm').action = '{{ route("api.extract-colors") }}';
        document.getElementById('uploadForm').method = 'POST';
    });
</script>
@endpush
