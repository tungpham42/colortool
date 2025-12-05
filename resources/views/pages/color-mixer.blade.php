@extends('layouts.app')

@section('title', 'Color Mixer')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-blender me-2"></i>Color Mixer</h4>
            </div>
            <div class="card-body">
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

                <div class="text-center mt-5">
                    <h4>Mixed Color Result</h4>
                    <div id="mixedColorPreview" class="color-preview mx-auto mb-3" style="background-color: #800080; width: 200px; height: 200px;"></div>

                    <div class="row justify-content-center">
                        <div class="col-md-8">
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
                                        <th>HSL</th>
                                        <td id="mixedHsl">hsl(300, 100%, 25%)</td>
                                        <td><button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard(document.getElementById('mixedHsl').textContent)">Copy</button></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
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
                    <h5>Color Theory</h5>
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>About Color Mixing</h6>
                        <p class="mb-0 small">Color mixing follows the RGB additive model. Mixing primary colors (Red, Green, Blue) creates secondary colors.</p>
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

        <div class="card mt-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-history me-2"></i>Recent Mixes</h4>
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
</div>
@endsection

@push('scripts')
<script>
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
        const mixedHsl = rgbToHsl(mixedRgb.r, mixedRgb.g, mixedRgb.b);

        // Update display
        document.getElementById('mixedColorPreview').style.backgroundColor = mixedHex;
        document.getElementById('mixedHex').textContent = mixedHex;
        document.getElementById('mixedRgb').textContent = `rgb(${mixedRgb.r}, ${mixedRgb.g}, ${mixedRgb.b})`;
        document.getElementById('mixedHsl').textContent = `hsl(${Math.round(mixedHsl.h)}, ${Math.round(mixedHsl.s)}%, ${Math.round(mixedHsl.l)}%)`;

        // Save to recent mixes
        saveRecentMix(color1, color2, mixedHex);
    }

    function saveRecentMix(color1, color2, result) {
        let recentMixes = JSON.parse(localStorage.getItem('recentMixes') || '[]');

        // Add new mix
        recentMixes.unshift({
            color1: color1,
            color2: color2,
            result: result,
            timestamp: new Date().toLocaleString()
        });

        // Keep only last 5
        recentMixes = recentMixes.slice(0, 5);
        localStorage.setItem('recentMixes', JSON.stringify(recentMixes));

        // Update display
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
            html += `
                <div class="mb-3 pb-3 ${index < recentMixes.length - 1 ? 'border-bottom' : ''}">
                    <div class="d-flex align-items-center">
                        <div class="d-flex me-3">
                            <div style="width: 20px; height: 40px; background: ${mix.color1}; border-radius: 5px 0 0 5px;"></div>
                            <div style="width: 20px; height: 40px; background: ${mix.color2}; border-radius: 0 5px 5px 0;"></div>
                        </div>
                        <div class="flex-grow-1">
                            <div style="width: 40px; height: 20px; background: ${mix.result}; border-radius: 5px; margin-bottom: 5px;"></div>
                            <div class="small text-muted">${mix.timestamp}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="setMixColors('${mix.color1}', '${mix.color2}')">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        mixColors();
        updateRecentMixesDisplay();

        // Form submission for saving mixes
        document.getElementById('saveMixForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const mixName = this.elements.mix_name.value;
            if (mixName) {
                showToast(`Mix "${mixName}" saved to collection`);
                this.reset();
            }
        });
    });
</script>
@endpush
