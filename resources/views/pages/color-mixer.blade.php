@extends('layouts.app')

@section('title', 'Color Mixer')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-blender me-2"></i>Color Mixer</h4>
                    <div class="form-check form-switch" style="cursor: pointer;">
                        <input class="form-check-input" style="cursor: pointer;" type="checkbox" id="multiColorMode" onchange="toggleMultiColorMode()">
                        <label class="form-check-label" style="cursor: pointer;" for="multiColorMode">Multi-Color Mixing</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Two Color Mixing Interface (Default) -->
                <div id="twoColorInterface">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="text-center mb-4">
                                <div id="color1Preview" class="color-preview mx-auto mb-2" style="background-color: #ff0000;"></div>
                                <input type="color" id="color1Picker" value="#ff0000" class="form-control form-control-color mb-2"
                                       onchange="updateColor1(this.value)">
                                <input type="text" id="color1Input" value="#ff0000" class="form-control text-center"
                                       oninput="if(/^#[0-9A-F]{6}$/i.test(this.value)) updateColor1(this.value)">
                            </div>
                        </div>

                        <div class="col-md-2 text-center">
                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                <i class="fas fa-plus fa-2x mb-3 text-muted"></i>

                                <div class="mb-3">
                                    <label class="form-label">Mix Ratio</label>
                                    <input type="range" id="mixRatio" class="form-range" min="0" max="100" value="50"
                                           oninput="document.getElementById('ratioValue').textContent = this.value + '%'; mixColors();">
                                    <div class="d-flex justify-content-between">
                                        <small>Color 1</small>
                                        <span id="ratioValue" class="fw-bold">50%</span>
                                        <small>Color 2</small>
                                    </div>
                                </div>

                                <button class="btn-gradient mt-3" onclick="mixColors()">
                                    <i class="fas fa-blender me-2"></i>Mix Colors
                                </button>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="text-center mb-4">
                                <div id="color2Preview" class="color-preview mx-auto mb-2" style="background-color: #0000ff;"></div>
                                <input type="color" id="color2Picker" value="#0000ff" class="form-control form-control-color mb-2"
                                       onchange="updateColor2(this.value)">
                                <input type="text" id="color2Input" value="#0000ff" class="form-control text-center"
                                       oninput="if(/^#[0-9A-F]{6}$/i.test(this.value)) updateColor2(this.value)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Multi Color Mixing Interface (Hidden by Default) -->
                <div id="multiColorInterface" style="display: none;">
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>Color Palette</h5>
                                <div>
                                    <button class="btn btn-sm btn-outline-primary me-2" onclick="addColorSlot()">
                                        <i class="fas fa-plus me-1"></i>Add Color
                                    </button>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="randomizeAllColors()">
                                        <i class="fas fa-random me-1"></i>Randomize
                                    </button>
                                </div>
                            </div>

                            <div id="multiColorSlots" class="row g-3">
                                <!-- Color slots will be dynamically added here -->
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Mixing Method</label>
                            <select id="mixingMethod" class="form-select" onchange="mixMultiColors()">
                                <option value="average">Average (Equal Weight)</option>
                                <option value="weighted">Weighted Average</option>
                                <option value="median">Median Value</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Number of Colors: <span id="colorCount">3</span></label>
                            <input type="range" id="colorCountSlider" class="form-range" min="2" max="8" value="3"
                                   oninput="updateColorCount(this.value)">
                        </div>
                    </div>

                    <div id="weightControls" style="display: none;">
                        <h6>Color Weights</h6>
                        <div id="weightSlots" class="row g-2 mb-3">
                            <!-- Weight controls will be dynamically added here -->
                        </div>
                    </div>

                    <div class="text-center">
                        <button class="btn-gradient" onclick="mixMultiColors()">
                            <i class="fas fa-blender me-2"></i>Mix All Colors
                        </button>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <h4>Mixed Color Result</h4>
                    <div id="mixedColorPreview" class="color-preview mx-auto mb-3" style="background-color: #800080; width: 200px; height: 200px;"></div>
                    <button id="viewColorDetails" class="btn btn-gradient mb-3 text-align-center" onclick="viewColorDetails(document.getElementById('mixedHex').textContent)">
                        <i class="fas fa-eye me-2"></i>View Color Details
                    </button>
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>HEX</th>
                                        <td id="mixedHex">#800080</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard(document.getElementById('mixedHex').textContent)">Copy</button></td>
                                    </tr>
                                    <tr>
                                        <th>RGB</th>
                                        <td id="mixedRgb">rgb(128, 0, 128)</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard(document.getElementById('mixedRgb').textContent)">Copy</button></td>
                                    </tr>
                                    <tr>
                                        <th>CMYK</th>
                                        <td id="mixedCmyk">cmyk(0%, 100%, 0%, 50%)</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard(document.getElementById('mixedCmyk').textContent)">Copy</button></td>
                                    </tr>
                                    <tr>
                                        <th>HSL</th>
                                        <td id="mixedHsl">hsl(300, 100%, 25%)</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard(document.getElementById('mixedHsl').textContent)">Copy</button></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4" id="colorComponents" style="display: none;">
                        <h5>Color Components</h5>
                        <div class="row g-2 justify-content-center" id="componentColors">
                            <!-- Component colors will be shown here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Recent Mixes</h4>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-danger" onclick="showClearAllModal()" title="Clear All">
                            <i class="fas fa-trash me-1"></i>Clear All
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="recentMixes">
                    <div class="text-center text-muted">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <p>No recent mixes yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Color Mixing Ideas</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Popular Mixes</h5>
                    <div class="row g-2">
                        @foreach([
                            ['Red + Yellow', '#ff0000', '#ffff00', '#ff8000'],
                            ['Blue + Yellow', '#0000ff', '#ffff00', '#008000'],
                            ['Red + Blue', '#ff0000', '#0000ff', '#800080'],
                            ['Green + Blue', '#00ff00', '#0000ff', '#008080'],
                            ['Pink + Blue', '#ff69b4', '#0000ff', '#8034b2']
                        ] as $mix)
                            <div class="col-12">
                                <div class="p-3 border rounded cursor-pointer" onclick="setMixColors('{{ $mix[1] }}', '{{ $mix[2] }}')">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex me-3">
                                            <div style="width: 30px; height: 30px; background: {{ $mix[1] }}; border-radius: 5px 0 0 5px;"></div>
                                            <div style="width: 30px; height: 30px; background: {{ $mix[2] }}; border-radius: 0 5px 5px 0;"></div>
                                        </div>
                                        <div>
                                            <strong>{{ $mix[0] }}</strong>
                                            <div class="small text-muted">Result: <span style="color: {{ $mix[3] }}">{{ $mix[3] }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Multi-Color Mixes</h5>
                    <div class="row g-2">
                        @foreach([
                            ['Primary Colors', ['#ff0000', '#00ff00', '#0000ff'], '#555555'],
                            ['Rainbow', ['#ff0000', '#ffa500', '#ffff00', '#00ff00', '#0000ff', '#4b0082', '#8f00ff'], '#6a6a6a'],
                            ['Pastel Colors', ['#ffb3ba', '#ffdfba', '#ffffba', '#baffc9', '#bae1ff'], '#e0e0e0'],
                            ['Warm Colors', ['#ff0000', '#ff9900', '#ffcc00', '#ff6600'], '#ff8c33'],
                            ['Cool Colors', ['#0000ff', '#00ffff', '#00ff00', '#0080ff'], '#00a0a0']
                        ] as $mix)
                            <div class="col-12">
                                <div class="p-3 border rounded cursor-pointer" onclick="setMultiColors({{ json_encode($mix[1]) }})">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex me-3">
                                            @foreach($mix[1] as $color)
                                                <div style="width: 20px; height: 30px; background: {{ $color }}; {{ !$loop->first ? 'margin-left: -5px;' : '' }}"></div>
                                            @endforeach
                                        </div>
                                        <div>
                                            <strong>{{ $mix[0] }}</strong>
                                            <div class="small text-muted">Result: <span style="color: {{ $mix[2] }}">{{ $mix[2] }}</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <h5>Color Theory</h5>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>About Color Mixing</h6>
                        <p class="mb-0 small">Color mixing follows the RGB additive model. Mixing multiple colors creates new shades based on the selected mixing method.</p>
                    </div>
                </div>

                <div>
                    <h5>Save Mix</h5>
                    <form id="saveMixForm">
                        @csrf
                        <div class="mb-3">
                            <input type="text" name="mix_name" class="form-control" placeholder="Give this mix a name">
                        </div>
                        <button type="submit" class="btn-gradient w-100">
                            <i class="fas fa-save me-2"></i>Save to Collection
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                    <p id="deleteModalMessage">Are you sure you want to delete this mix?</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Clear All Confirmation Modal -->
<div class="modal fade" id="clearAllConfirmModal" tabindex="-1" aria-labelledby="clearAllConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearAllConfirmModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Clear All Recent Mixes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-broom fa-3x text-warning mb-3"></i>
                    <p class="fw-bold">Warning: This action cannot be undone!</p>
                    <p>Are you sure you want to clear all recent mixes?</p>
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        This will permanently remove all your recent color mixes.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmClearAllBtn">
                    <i class="fas fa-broom me-2"></i>Clear All
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .color-slot {
        position: relative;
        transition: transform 0.2s;
    }
    .color-slot:hover {
        transform: translateY(-2px);
    }
    .color-slot .remove-btn {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #dc3545;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .color-slot:hover .remove-btn {
        opacity: 1;
    }
    .weight-control {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .weight-control input[type="range"] {
        flex: 1;
    }
    .recent-mix-item {
        position: relative;
        transition: background-color 0.2s;
    }
    .recent-mix-item:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .modal-content {
        border-radius: 10px;
        overflow: hidden;
    }
    .modal-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
    let multiColors = ['#ff0000', '#00ff00', '#0000ff'];
    let colorWeights = [1, 1, 1];
    let currentDeleteIndex = null;

    // Modal instances
    let deleteConfirmModal = null;
    let clearAllConfirmModal = null;

    function updateColor1(color) {
        document.getElementById('color1Preview').style.backgroundColor = color;
        document.getElementById('color1Input').value = color;
        document.getElementById('color1Picker').value = color;
        mixColors();
    }

    function updateColor2(color) {
        document.getElementById('color2Preview').style.backgroundColor = color;
        document.getElementById('color2Input').value = color;
        document.getElementById('color2Picker').value = color;
        mixColors();
    }

    function setMixColors(color1, color2) {
        updateColor1(color1);
        updateColor2(color2);
        mixColors();
        showToast('Colors set for mixing');
    }

    function setMultiColors(colors) {
        toggleMultiColorMode(true);
        multiColors = [...colors];
        colorWeights = colors.map(() => 1);
        updateColorCount(colors.length);
        renderColorSlots();
        mixMultiColors();
        showToast(`${colors.length} colors loaded`);
    }

    function toggleMultiColorMode(forceEnable = false) {
        const isMulti = forceEnable || document.getElementById('multiColorMode').checked;
        document.getElementById('twoColorInterface').style.display = isMulti ? 'none' : 'block';
        document.getElementById('multiColorInterface').style.display = isMulti ? 'block' : 'none';

        if (isMulti && multiColors.length === 0) {
            initializeMultiColors();
        }

        if (isMulti) {
            mixMultiColors();
        } else {
            mixColors();
        }
    }

    function initializeMultiColors() {
        multiColors = ['#ff0000', '#00ff00', '#0000ff', '#ffff00', '#ff00ff'];
        colorWeights = multiColors.map(() => 1);
        updateColorCount(multiColors.length);
        renderColorSlots();
        renderWeightControls();
    }

    function updateColorCount(count) {
        const currentCount = multiColors.length;
        count = parseInt(count);
        document.getElementById('colorCount').textContent = count;
        document.getElementById('colorCountSlider').value = count;

        if (count > currentCount) {
            for (let i = currentCount; i < count; i++) {
                multiColors.push(getRandomColor());
                colorWeights.push(1);
            }
        } else if (count < currentCount) {
            multiColors = multiColors.slice(0, count);
            colorWeights = colorWeights.slice(0, count);
        }

        renderColorSlots();
        renderWeightControls();
        mixMultiColors();
    }

    function addColorSlot() {
        multiColors.push(getRandomColor());
        colorWeights.push(1);
        updateColorCount(multiColors.length);
    }

    function removeColorSlot(index) {
        multiColors.splice(index, 1);
        colorWeights.splice(index, 1);
        updateColorCount(multiColors.length);
    }

    // Keep the original updateMultiColor function but add element updates
    function updateMultiColor(index, color) {
        multiColors[index] = color;

        // Update all related elements
        const preview = document.getElementById(`multiColorPreview${index}`);
        const weightPreview = document.getElementById(`multiColorWeightPreview${index}`);
        const picker = document.getElementById(`multiColorPicker${index}`);
        const input = document.getElementById(`multiColorInput${index}`);

        if (preview) preview.style.backgroundColor = color;
        if (weightPreview) weightPreview.style.backgroundColor = color;
        if (picker) picker.value = color;
        if (input) input.value = color;

        mixMultiColors();
    }

    function randomizeAllColors() {
        multiColors = multiColors.map(() => getRandomColor());
        renderColorSlots();
        mixMultiColors();
    }

    function getRandomColor() {
        const letters = '0123456789ABCDEF';
        let color = '#';
        for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    function renderColorSlots() {
        const container = document.getElementById('multiColorSlots');
        let html = '';

        multiColors.forEach((color, index) => {
            html += `
                <div class="col-md-3 col-sm-4 col-6">
                    <div class="color-slot">
                        <button class="remove-btn" onclick="removeColorSlot(${index})" title="Remove color">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="text-center">
                            <div id="multiColorPreview${index}" class="color-preview mx-auto mb-2" style="background-color: ${color};"></div>
                            <input type="color" id="multiColorPicker${index}" value="${color}" class="form-control form-control-color mb-2"
                                onchange="updateMultiColorFromPicker(${index})">
                            <input type="text" id="multiColorInput${index}" value="${color}" class="form-control text-center small"
                                oninput="updateMultiColorFromInput(${index})">
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Add these helper functions
    function updateMultiColorFromPicker(index) {
        const color = document.getElementById(`multiColorPicker${index}`).value;
        updateMultiColor(index, color);
    }

    function updateMultiColorFromInput(index) {
        const input = document.getElementById(`multiColorInput${index}`);
        const color = input.value;

        if (/^#[0-9A-F]{6}$/i.test(color)) {
            updateMultiColor(index, color);
        }
    }

    function renderWeightControls() {
        const container = document.getElementById('weightSlots');
        const method = document.getElementById('mixingMethod').value;
        const weightContainer = document.getElementById('weightControls');

        if (method === 'weighted') {
            weightContainer.style.display = 'block';
            let html = '';

            multiColors.forEach((color, index) => {
                const weight = colorWeights[index];
                html += `
                    <div class="col-md-6">
                        <div class="weight-control">
                            <div id="multiColorWeightPreview${index}" class="color-preview" style="width: 30px; height: 30px; background: ${color};"></div>
                            <input type="range" class="form-range" min="1" max="10" value="${weight}"
                                   oninput="updateColorWeight(${index}, this.value); document.getElementById('weightValue${index}').textContent = this.value;">
                            <span class="badge bg-secondary" id="weightValue${index}">${weight}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        } else {
            weightContainer.style.display = 'none';
        }
    }

    function updateColorWeight(index, weight) {
        colorWeights[index] = parseInt(weight);
        mixMultiColors();
    }

    function mixMultiColors() {
        const method = document.getElementById('mixingMethod').value;
        let mixedRgb = { r: 0, g: 0, b: 0 };

        if (method === 'average') {
            // Simple average
            const total = multiColors.length;
            multiColors.forEach(color => {
                const rgb = hexToRgb(color);
                mixedRgb.r += rgb.r;
                mixedRgb.g += rgb.g;
                mixedRgb.b += rgb.b;
            });
            mixedRgb.r = Math.round(mixedRgb.r / total);
            mixedRgb.g = Math.round(mixedRgb.g / total);
            mixedRgb.b = Math.round(mixedRgb.b / total);
        } else if (method === 'weighted') {
            // Weighted average
            let totalWeight = 0;
            multiColors.forEach((color, index) => {
                const rgb = hexToRgb(color);
                const weight = colorWeights[index];
                mixedRgb.r += rgb.r * weight;
                mixedRgb.g += rgb.g * weight;
                mixedRgb.b += rgb.b * weight;
                totalWeight += weight;
            });
            mixedRgb.r = Math.round(mixedRgb.r / totalWeight);
            mixedRgb.g = Math.round(mixedRgb.g / totalWeight);
            mixedRgb.b = Math.round(mixedRgb.b / totalWeight);
        } else if (method === 'median') {
            // Median value for each channel
            const rValues = multiColors.map(color => hexToRgb(color).r).sort((a, b) => a - b);
            const gValues = multiColors.map(color => hexToRgb(color).g).sort((a, b) => a - b);
            const bValues = multiColors.map(color => hexToRgb(color).b).sort((a, b) => a - b);

            const mid = Math.floor(multiColors.length / 2);
            mixedRgb.r = multiColors.length % 2 ? rValues[mid] : Math.round((rValues[mid-1] + rValues[mid]) / 2);
            mixedRgb.g = multiColors.length % 2 ? gValues[mid] : Math.round((gValues[mid-1] + gValues[mid]) / 2);
            mixedRgb.b = multiColors.length % 2 ? bValues[mid] : Math.round((bValues[mid-1] + bValues[mid]) / 2);
        }

        // Show color components
        showColorComponents(mixedRgb);

        const mixedHex = rgbToHex(mixedRgb.r, mixedRgb.g, mixedRgb.b);
        const mixedCmyk = rgbToCmyk(mixedRgb.r, mixedRgb.g, mixedRgb.b);
        const mixedHsl = rgbToHsl(mixedRgb.r, mixedRgb.g, mixedRgb.b);

        // Update display
        updateMixedColorDisplay(mixedHex, mixedRgb, mixedCmyk, mixedHsl);

        // Save to recent mixes
        saveRecentMixMulti(multiColors, mixedHex, method);
    }

    function showColorComponents(resultRgb) {
        const container = document.getElementById('componentColors');
        let html = '';

        // Show original colors
        multiColors.forEach((color, index) => {
            const rgb = hexToRgb(color);
            const contribution = colorWeights[index];
            html += `
                <div class="col-auto">
                    <div class="text-center">
                        <div class="color-preview mb-1" style="background: ${color}; width: 50px; height: 30px;"></div>
                        <div class="small">
                            ${document.getElementById('mixingMethod').value === 'weighted' ? `Weight: ${contribution}` : ''}
                        </div>
                    </div>
                </div>
            `;

            if (index < multiColors.length - 1) {
                html += '<div class="col-auto d-flex align-items-center"><i class="fas fa-plus text-muted"></i></div>';
            }
        });

        html += '<div class="col-auto d-flex align-items-center"><i class="fas fa-equals text-muted"></i></div>';
        html += `
            <div class="col-auto">
                <div class="text-center">
                    <div class="color-preview mb-1" style="background: rgb(${resultRgb.r}, ${resultRgb.g}, ${resultRgb.b}); width: 50px; height: 30px;"></div>
                    <div class="small">Result</div>
                </div>
            </div>
        `;

        container.innerHTML = html;
        document.getElementById('colorComponents').style.display = 'block';
    }

    function mixColors() {
        const color1 = document.getElementById('color1Input').value;
        const color2 = document.getElementById('color2Input').value;
        const ratio = parseInt(document.getElementById('mixRatio').value) / 100;

        const rgb1 = hexToRgb(color1);
        const rgb2 = hexToRgb(color2);

        // Linear interpolation
        const mixedRgb = {
            r: Math.round(rgb1.r * (1 - ratio) + rgb2.r * ratio),
            g: Math.round(rgb1.g * (1 - ratio) + rgb2.g * ratio),
            b: Math.round(rgb1.b * (1 - ratio) + rgb2.b * ratio)
        };

        const mixedHex = rgbToHex(mixedRgb.r, mixedRgb.g, mixedRgb.b);
        const mixedCmyk = rgbToCmyk(mixedRgb.r, mixedRgb.g, mixedRgb.b);
        const mixedHsl = rgbToHsl(mixedRgb.r, mixedRgb.g, mixedRgb.b);

        // Update display
        updateMixedColorDisplay(mixedHex, mixedRgb, mixedCmyk, mixedHsl);

        // Hide color components for two-color mode
        document.getElementById('colorComponents').style.display = 'none';

        // Save to recent mixes
        saveRecentMix(color1, color2, mixedHex);
    }

    function updateMixedColorDisplay(hex, rgb, cmyk, hsl) {
        document.getElementById('mixedColorPreview').style.backgroundColor = hex;
        document.getElementById('mixedHex').textContent = hex;
        document.getElementById('mixedRgb').textContent = `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`;
        document.getElementById('mixedCmyk').textContent = `cmyk(${cmyk.c}%, ${cmyk.m}%, ${cmyk.y}%, ${cmyk.k}%)`;
        document.getElementById('mixedHsl').textContent = `hsl(${Math.round(hsl.h)}, ${Math.round(hsl.s)}%, ${Math.round(hsl.l)}%)`;
    }

    function saveRecentMix(color1, color2, result) {
        let recentMixes = JSON.parse(localStorage.getItem('recentMixes') || '[]');

        recentMixes.unshift({
            type: 'two-color',
            color1: color1,
            color2: color2,
            result: result,
            timestamp: new Date().toLocaleString()
        });

        recentMixes = recentMixes.slice(0, 10);
        localStorage.setItem('recentMixes', JSON.stringify(recentMixes));
        updateRecentMixesDisplay();
    }

    function saveRecentMixMulti(colors, result, method) {
        let recentMixes = JSON.parse(localStorage.getItem('recentMixes') || '[]');

        recentMixes.unshift({
            type: 'multi-color',
            colors: [...colors],
            result: result,
            method: method,
            count: colors.length,
            timestamp: new Date().toLocaleString()
        });

        recentMixes = recentMixes.slice(0, 10);
        localStorage.setItem('recentMixes', JSON.stringify(recentMixes));
        updateRecentMixesDisplay();
    }

    function updateRecentMixesDisplay() {
        const recentMixes = JSON.parse(localStorage.getItem('recentMixes') || '[]');
        const container = document.getElementById('recentMixes');

        if (recentMixes.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p>No recent mixes yet</p>
                </div>
            `;
            return;
        }

        let html = '';
        recentMixes.forEach((mix, index) => {
            if (mix.type === 'two-color') {
                html += `
                    <div class="mb-3 pb-3 recent-mix-item ${index < recentMixes.length - 1 ? 'border-bottom' : ''}">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-3">
                                <div style="width: 20px; height: 40px; background: ${mix.color1}; border-radius: 5px 0 0 5px;"></div>
                                <div style="width: 20px; height: 40px; background: ${mix.color2}; border-radius: 0 5px 5px 0;"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div style="width: 40px; height: 20px; background: ${mix.result}; border-radius: 5px; margin-bottom: 5px;"></div>
                                <div class="small text-muted">${mix.timestamp}</div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="setMixColors('${mix.color1}', '${mix.color2}')" title="Reuse">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-mix-btn" onclick="showDeleteModal(${index})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="mb-3 pb-3 recent-mix-item ${index < recentMixes.length - 1 ? 'border-bottom' : ''}">
                        <div class="d-flex align-items-center">
                            <div class="d-flex me-3" style="flex-wrap: wrap; max-width: 100px;">
                                ${mix.colors.map(color =>
                                    `<div style="width: 15px; height: 15px; background: ${color}; margin: 1px;"></div>`
                                ).join('')}
                            </div>
                            <div class="flex-grow-1">
                                <div style="width: 40px; height: 20px; background: ${mix.result}; border-radius: 5px; margin-bottom: 5px;"></div>
                                <div class="small text-muted">
                                    ${mix.count} colors (${mix.method})<br>
                                    ${mix.timestamp}
                                </div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="setMultiColors(${JSON.stringify(mix.colors)})" title="Reuse">
                                    <i class="fas fa-redo"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-mix-btn" onclick="showDeleteModal(${index})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;
    }

    function showDeleteModal(index) {
        currentDeleteIndex = index;
        const recentMixes = JSON.parse(localStorage.getItem('recentMixes') || '[]');

        if (index >= 0 && index < recentMixes.length) {
            const mix = recentMixes[index];
            let message = '';

            if (mix.type === 'two-color') {
                message = `Are you sure you want to delete the mix of <span class="fw-bold" style="color: ${mix.color1}">${mix.color1}</span> and <span class="fw-bold" style="color: ${mix.color2}">${mix.color2}</span>?`;
            } else {
                message = `Are you sure you want to delete this ${mix.count}-color mix?`;
            }

            document.getElementById('deleteModalMessage').innerHTML = message;
            deleteConfirmModal.show();
        }
    }

    function deleteRecentMix() {
        if (currentDeleteIndex !== null) {
            let recentMixes = JSON.parse(localStorage.getItem('recentMixes') || '[]');

            if (currentDeleteIndex >= 0 && currentDeleteIndex < recentMixes.length) {
                recentMixes.splice(currentDeleteIndex, 1);
                localStorage.setItem('recentMixes', JSON.stringify(recentMixes));
                updateRecentMixesDisplay();
                showToast('Mix deleted successfully');
            }

            currentDeleteIndex = null;
            deleteConfirmModal.hide();
        }
    }

    function showClearAllModal() {
        clearAllConfirmModal.show();
    }

    function clearAllRecentMixes() {
        localStorage.removeItem('recentMixes');
        updateRecentMixesDisplay();
        showToast('All recent mixes cleared');
        clearAllConfirmModal.hide();
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
        clearAllConfirmModal = new bootstrap.Modal(document.getElementById('clearAllConfirmModal'));

        // Set up modal button handlers
        document.getElementById('confirmDeleteBtn').addEventListener('click', deleteRecentMix);
        document.getElementById('confirmClearAllBtn').addEventListener('click', clearAllRecentMixes);

        mixColors();
        updateRecentMixesDisplay();
        renderColorSlots();
        renderWeightControls();

        // Form submission for saving mixes
        document.getElementById('saveMixForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const mixName = this.elements.mix_name.value;
            if (mixName) {
                const isMulti = document.getElementById('multiColorMode').checked;
                if (isMulti) {
                    showToast(`Multi-color mix "${mixName}" saved to collection`);
                } else {
                    showToast(`Mix "${mixName}" saved to collection`);
                }
                this.reset();
            }
        });

        // Mixing method change
        document.getElementById('mixingMethod').addEventListener('change', function() {
            renderWeightControls();
            mixMultiColors();
        });
    });
</script>
@endpush
