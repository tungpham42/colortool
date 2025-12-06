@extends('layouts.app')

@section('title', 'Color Lookup & Database')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-search me-2"></i>Search Colors</h4>
            </div>
            <div class="card-body">
                <form id="searchForm" data-ajax>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Search by Name or HEX</label>
                        <input type="text" id="searchQuery" name="query" class="form-control"
                               placeholder="Search colors..." onkeyup="searchColors()">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color Category</label>
                        <select id="categoryFilter" name="category" class="form-select" onchange="searchColors()">
                            <option value="">All Categories</option>
                            <option value="Red">Red</option>
                            <option value="Green">Green</option>
                            <option value="Blue">Blue</option>
                            <option value="Yellow">Yellow</option>
                            <option value="Orange">Orange</option>
                            <option value="Purple">Purple</option>
                            <option value="Pink">Pink</option>
                            <option value="Brown">Brown</option>
                            <option value="Gray">Gray</option>
                            <option value="Black">Black</option>
                            <option value="White">White</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort By</label>
                        <select id="sortBy" name="sort" class="form-select" onchange="searchColors()">
                            <option value="name">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                            <option value="hex">HEX Code</option>
                            <option value="category">Category</option>
                            <option value="popular">Most Popular</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Results Per Page</label>
                        <select id="perPage" name="per_page" class="form-select" onchange="searchColors()">
                            <option value="12">12 Colors</option>
                            <option value="24">24 Colors</option>
                            <option value="48">48 Colors</option>
                            <option value="96">96 Colors</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn-gradient flex-grow-1" onclick="searchColors()">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetSearch()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                    </div>
                </form>

                <hr class="my-4">

                <div>
                    <h5>Quick Categories</h5>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach(['Red', 'Green', 'Blue', 'Yellow', 'Purple', 'Pink', 'Orange', 'Brown', 'Gray'] as $category)
                            <span class="badge bg-light text-dark cursor-pointer category-badge"
                                  onclick="filterByCategory('{{ $category }}')">
                                {{ $category }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4">
                    <h5>Popular Searches</h5>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach(['Blue', 'Red', 'Green', 'Purple', 'Pink', 'Gold', 'Silver', 'Black', 'White'] as $popular)
                            <span class="badge bg-primary cursor-pointer"
                                  onclick="searchByKeyword('{{ $popular }}')">
                                {{ $popular }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-star me-2"></i>Featured Colors</h4>
            </div>
            <div class="card-body">
                <div class="color-grid">
                    @foreach([
                        ['name' => 'Azure', 'hex' => '#007FFF'],
                        ['name' => 'Crimson', 'hex' => '#DC143C'],
                        ['name' => 'Emerald', 'hex' => '#50C878'],
                        ['name' => 'Amber', 'hex' => '#FFBF00'],
                        ['name' => 'Violet', 'hex' => '#8F00FF'],
                        ['name' => 'Rose', 'hex' => '#FF007F']
                    ] as $featured)
                        <div class="color-item" style="background-color: {{ $featured['hex'] }};"
                             onclick="selectColor('{{ $featured['hex'] }}', '{{ $featured['name'] }}')">
                            <div class="color-label">
                                <span class="color-hex">{{ $featured['hex'] }}</span>
                                <span class="color-rgb">{{ $featured['name'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-database me-2"></i>Color Database</h4>
                <span class="badge bg-primary" id="resultCount">0 Colors</span>
            </div>
            <div class="card-body">
                <div id="colorResults">
                    <!-- Results will be loaded here -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Search for colors</h4>
                        <p class="text-muted">Use the search form to find colors from our database</p>
                    </div>
                </div>

                <nav aria-label="Page navigation" class="mt-4" id="paginationNav" style="display: none;">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be generated here -->
                    </ul>
                </nav>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Create Palette</h5>
                    </div>
                    <div class="card-body">
                        <div id="selectedColors" class="mb-3">
                            <p class="text-muted">No colors selected for palette</p>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn-gradient flex-grow-1" onclick="createPalette()">
                                <i class="fas fa-plus me-2"></i>Create Palette
                            </button>
                            <button class="btn btn-outline-secondary" onclick="clearPalette()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-download me-2"></i>Export</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Export Format</label>
                            <select id="exportFormat" class="form-select">
                                <option value="json">JSON</option>
                                <option value="css">CSS Variables</option>
                                <option value="scss">SCSS Variables</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea id="exportOutput" class="form-control" rows="3" readonly placeholder="Export will appear here"></textarea>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn-gradient flex-grow-1" onclick="exportColors()">
                                <i class="fas fa-file-export me-2"></i>Export
                            </button>
                            <button class="btn btn-outline-secondary" onclick="copyExport()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Color Details Modal -->
<div class="modal fade" id="colorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Color Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn-gradient" onclick="copyCurrentColor()">
                    <i class="fas fa-copy me-2"></i>Copy Color
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .category-badge {
        cursor: pointer;
        transition: all 0.2s;
        padding: 8px 12px;
        border-radius: 20px;
    }

    .category-badge:hover {
        background-color: #3498db !important;
        color: white !important;
        transform: translateY(-2px);
    }

    .color-result-item {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .color-result-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #3498db;
    }

    .color-swatch-small {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        flex-shrink: 0;
    }

    .color-info {
        flex-grow: 1;
    }

    .color-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .color-hex-code {
        font-family: monospace;
        font-size: 14px;
        color: #3498db;
        margin-bottom: 5px;
    }

    .color-category {
        font-size: 12px;
        color: #7f8c8d;
        background: #f8f9fa;
        padding: 3px 8px;
        border-radius: 12px;
        display: inline-block;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .pagination .page-link {
        color: #3498db;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #3498db;
        border-color: #3498db;
        color: white;
    }

    .selected-color-item {
        display: inline-flex;
        align-items: center;
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 20px;
        margin: 5px;
    }

    .selected-color-swatch {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        margin-right: 8px;
        border: 1px solid #dee2e6;
    }

    .recent-color-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s;
    }

    .recent-color-item:hover {
        background: #f8f9fa;
    }

    .recent-color-swatch {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        margin-right: 10px;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .recent-color-info {
        flex-grow: 1;
    }

    .recent-color-name {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 2px;
    }

    .recent-color-hex {
        font-size: 12px;
        color: #7f8c8d;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentPage = 1;
    let totalPages = 1;
    let selectedColors = [];
    let currentSelectedColor = null;

    // Search colors function
    function searchColors(page = 1) {
        currentPage = page;

        const query = document.getElementById('searchQuery').value;
        const category = document.getElementById('categoryFilter').value;
        const sort = document.getElementById('sortBy').value;
        const perPage = document.getElementById('perPage').value;

        showLoader();

        fetch('{{ route("api.search-colors") }}?' + new URLSearchParams({
            query: query,
            category: category,
            sort: sort,
            page: page,
            per_page: perPage
        }))
        .then(response => response.json())
        .then(data => {
            hideLoader();

            if (data.success) {
                displaySearchResults(data.colors);
                updateResultCount(data.count);
                updatePagination(data.total_pages || 1);
            } else {
                showToast('Search failed: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            hideLoader();
            showToast('Search error: ' + error.message, 'danger');
            console.error('Search error:', error);
        });
    }

    // Display search results
    function displaySearchResults(colors) {
        const resultsDiv = document.getElementById('colorResults');

        if (!colors || colors.length === 0) {
            resultsDiv.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No colors found</h4>
                    <p class="text-muted">Try adjusting your search criteria</p>
                </div>
            `;
            document.getElementById('paginationNav').style.display = 'none';
            return;
        }

        let html = '<div class="row">';

        colors.forEach(color => {
            const textColor = calculateBrightness(color.hex) > 0.5 ? '#000000' : '#ffffff';

            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="color-result-item" onclick="showColorDetails('${color.hex}', '${color.name}')">
                        <div class="color-swatch-small" style="background-color: ${color.hex};"></div>
                        <div class="color-info">
                            <div class="color-name">${color.name}</div>
                            <div class="color-hex-code">${color.hex}</div>
                            <div class="color-category">${color.category}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        resultsDiv.innerHTML = html;
        document.getElementById('paginationNav').style.display = 'block';
    }

    // Update result count
    function updateResultCount(count) {
        document.getElementById('resultCount').textContent = count + ' Colors';
    }

    // Update pagination
    function updatePagination(totalPages) {
        const paginationDiv = document.getElementById('pagination');
        totalPages = totalPages || 1;

        let html = '';

        // Previous button
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="searchColors(${currentPage - 1}); return false;">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Page numbers
        const maxPagesToShow = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="searchColors(${i}); return false;">${i}</a>
                </li>
            `;
        }

        // Next button
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="searchColors(${currentPage + 1}); return false;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        paginationDiv.innerHTML = html;
    }

    // Show color details
    function showColorDetails(hex, name) {
        currentSelectedColor = { hex, name };

        // Add to recent colors
        addToRecentColors(hex, name);

        // Show modal with color details
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');

        modalTitle.textContent = name;

        const rgb = hexToRgb(hex);
        const hsl = rgbToHsl(rgb.r, rgb.g, rgb.b);
        const cmyk = rgbToCmyk(rgb.r, rgb.g, rgb.b);
        const brightness = calculateBrightness(hex);
        const textColor = brightness > 0.5 ? '#000000' : '#ffffff';

        modalContent.innerHTML = `
            <div class="text-center mb-4">
                <div class="color-preview mx-auto mb-3" style="background-color: ${hex}; width: 150px; height: 150px; border: 3px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1);"></div>
                <h4>${name}</h4>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">HEX</th>
                        <td><code>${hex}</code></td>
                        <td width="20%"><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('${hex}')">Copy</button></td>
                    </tr>
                    <tr>
                        <th>RGB</th>
                        <td>rgb(${rgb.r}, ${rgb.g}, ${rgb.b})</td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('rgb(${rgb.r}, ${rgb.g}, ${rgb.b})')">Copy</button></td>
                    </tr>
                    <tr>
                        <th>HSL</th>
                        <td>hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)</td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)')">Copy</button></td>
                    </tr>
                    <tr>
                        <th>CMYK</th>
                        <td>cmyk(${cmyk.c}%, ${cmyk.m}%, ${cmyk.y}%, ${cmyk.k}%)</td>
                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('cmyk(${cmyk.c}%, ${cmyk.m}%, ${cmyk.y}%, ${cmyk.k}%)')">Copy</button></td>
                    </tr>
                    <tr>
                        <th>Brightness</th>
                        <td colspan="2">${(brightness * 100).toFixed(1)}% (${brightness > 0.5 ? 'Light' : 'Dark'})</td>
                    </tr>
                </table>
            </div>

            <div class="mt-4">
                <h5>Color Harmonies</h5>
                <div class="row g-2 mt-2">
                    <div class="col-6">
                        <div class="text-center p-2 border rounded">
                            <div style="width: 100%; height: 40px; background: ${hex}; border-radius: 5px; margin-bottom: 5px;"></div>
                            <small>Original</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-2 border rounded">
                            <div style="width: 100%; height: 40px; background: ${generateComplementary(hex)}; border-radius: 5px; margin-bottom: 5px;"></div>
                            <small>Complementary</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5>Add to Palette</h5>
                <button class="btn-gradient w-100" onclick="selectColor('${hex}', '${name}'); $('#colorModal').modal('hide');">
                    <i class="fas fa-plus me-2"></i>Add to Current Palette
                </button>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('colorModal'));
        modal.show();
    }

    // Copy current color from modal
    function copyCurrentColor() {
        if (currentSelectedColor) {
            copyToClipboard(currentSelectedColor.hex);
            $('#colorModal').modal('hide');
        }
    }

    // Select color for palette
    function selectColor(hex, name) {
        // Check if color already exists
        if (!selectedColors.some(color => color.hex === hex)) {
            selectedColors.push({ hex, name });
            updateSelectedColorsDisplay();
            showToast(`Added ${name} to palette`);
        } else {
            showToast(`${name} is already in the palette`, 'warning');
        }

        // Add to recent colors
        addToRecentColors(hex, name);
    }

    // Update selected colors display
    function updateSelectedColorsDisplay() {
        const container = document.getElementById('selectedColors');

        if (selectedColors.length === 0) {
            container.innerHTML = '<p class="text-muted">No colors selected for palette</p>';
            return;
        }

        let html = '<div class="d-flex flex-wrap">';
        selectedColors.forEach((color, index) => {
            html += `
                <div class="selected-color-item">
                    <div class="selected-color-swatch" style="background-color: ${color.hex};"></div>
                    <span style="font-size: 14px;">${color.name}</span>
                    <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromPalette(${index})" style="padding: 0 6px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        html += '</div>';

        container.innerHTML = html;
    }

    // Remove color from palette
    function removeFromPalette(index) {
        const removedColor = selectedColors.splice(index, 1)[0];
        updateSelectedColorsDisplay();
        showToast(`Removed ${removedColor.name} from palette`);
    }

    // Clear palette
    function clearPalette() {
        selectedColors = [];
        updateSelectedColorsDisplay();
        showToast('Palette cleared');
    }

    // Create palette
    function createPalette() {
        if (selectedColors.length === 0) {
            showToast('Please select some colors first', 'warning');
            return;
        }

        const paletteName = prompt('Enter a name for your palette:', 'My Color Palette ' + new Date().toLocaleDateString());
        if (paletteName) {
            // Save to localStorage
            let savedPalettes = JSON.parse(localStorage.getItem('savedPalettes') || '[]');

            savedPalettes.push({
                name: paletteName,
                colors: selectedColors,
                created: new Date().toISOString()
            });

            localStorage.setItem('savedPalettes', JSON.stringify(savedPalettes));

            showToast(`Palette "${paletteName}" saved successfully!`);
            clearPalette();
        }
    }

    // Export colors
    function exportColors() {
        if (selectedColors.length === 0) {
            showToast('No colors to export', 'warning');
            return;
        }

        const format = document.getElementById('exportFormat').value;
        let output = '';

        switch (format) {
            case 'json':
                output = JSON.stringify(selectedColors, null, 2);
                break;

            case 'css':
                output = `/* Color Palette */\n:root {\n`;
                selectedColors.forEach((color, index) => {
                    output += `  --color-${index + 1}: ${color.hex}; /* ${color.name} */\n`;
                });
                output += `}`;
                break;

            case 'scss':
                output = `// Color Palette\n`;
                selectedColors.forEach((color, index) => {
                    output += `$color-${index + 1}: ${color.hex}; // ${color.name}\n`;
                });
                break;

            case 'csv':
                output = 'Name,HEX,RGB\n';
                selectedColors.forEach(color => {
                    const rgb = hexToRgb(color.hex);
                    output += `"${color.name}",${color.hex},${rgb.r},${rgb.g},${rgb.b}\n`;
                });
                break;
        }

        document.getElementById('exportOutput').value = output;
        showToast(`Exported ${selectedColors.length} colors in ${format.toUpperCase()} format`);
    }

    // Copy export
    function copyExport() {
        const output = document.getElementById('exportOutput');
        if (output.value) {
            copyToClipboard(output.value);
        } else {
            showToast('Generate export first', 'warning');
        }
    }

    // Recent colors functionality
    function addToRecentColors(hex, name) {
        let recentColors = JSON.parse(localStorage.getItem('recentColors') || '[]');

        // Remove if already exists
        recentColors = recentColors.filter(color => color.hex !== hex);

        // Add to beginning
        recentColors.unshift({
            hex: hex,
            name: name,
            timestamp: new Date().toISOString()
        });

        // Keep only last 10
        recentColors = recentColors.slice(0, 10);
        localStorage.setItem('recentColors', JSON.stringify(recentColors));

        updateRecentColorsDisplay();
    }

    function updateRecentColorsDisplay() {
        const recentColors = JSON.parse(localStorage.getItem('recentColors') || '[]');
        const container = document.getElementById('recentColors');

        if (recentColors.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p>No recently viewed colors</p>
                </div>
            `;
            return;
        }

        let html = '';
        recentColors.forEach(color => {
            html += `
                <div class="recent-color-item" onclick="showColorDetails('${color.hex}', '${color.name}')">
                    <div class="recent-color-swatch" style="background-color: ${color.hex};"></div>
                    <div class="recent-color-info">
                        <div class="recent-color-name">${color.name}</div>
                        <div class="recent-color-hex">${color.hex}</div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); selectColor('${color.hex}', '${color.name}')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Helper functions
    function filterByCategory(category) {
        document.getElementById('categoryFilter').value = category;
        searchColors();
    }

    function searchByKeyword(keyword) {
        document.getElementById('searchQuery').value = keyword;
        searchColors();
    }

    function resetSearch() {
        document.getElementById('searchQuery').value = '';
        document.getElementById('categoryFilter').value = '';
        document.getElementById('sortBy').value = 'name';
        document.getElementById('perPage').value = '12';
        searchColors();
    }

    function generateComplementary(hex) {
        const rgb = hexToRgb(hex);
        const complementaryRgb = {
            r: 255 - rgb.r,
            g: 255 - rgb.g,
            b: 255 - rgb.b
        };
        return rgbToHex(complementaryRgb.r, complementaryRgb.g, complementaryRgb.b);
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Load initial data
        searchColors();
        updateRecentColorsDisplay();

        // Set up search form
        document.getElementById('searchForm').action = '{{ route("api.search-colors") }}';

        // Add Enter key support for search
        document.getElementById('searchQuery').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchColors();
            }
        });

        // Auto-search when typing (with debounce)
        let searchTimeout;
        document.getElementById('searchQuery').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchColors();
            }, 500);
        });
    });
</script>
@endpush
