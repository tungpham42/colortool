@extends('layouts.app')

@section('title', 'Color Details - #' . $hexUpper)

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Color Preview & Basic Info -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Color Preview</h5>
                </div>
                <div class="card-body">
                    <div class="color-preview-large mb-4"
                         style="background-color: #{{ $hex }}; height: 200px; border-radius: 15px; border: 5px solid #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mb-0">#{{ $hexUpper }}</h4>
                        <button class="btn btn-gradient" onclick="copyToClipboard('#{{ $hex }}')">
                            <i class="fas fa-copy me-2"></i>Copy HEX
                        </button>
                    </div>

                    <div class="mb-3">
                        <h5>{{ $colorName }}</h5>
                    </div>
                </div>
            </div>

            <!-- Color Harmonies -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-compress-alt me-2"></i>Color Harmonies</h5>
                </div>
                <div class="card-body">
                    @foreach($harmonies as $type => $colors)
                        <div class="harmony-group mb-4">
                            <h6 class="text-capitalize mb-3">{{ str_replace('_', ' ', $type) }}</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($colors as $color)
                                    <a href="{{ route('color.details', str_replace('#', '', $color)) }}"
                                       class="harmony-color"
                                       style="width: 60px; height: 60px; background-color: #{{ $color }}; border-radius: 8px; cursor: pointer; position: relative;"
                                       title="#{{ strtoupper($color) }}">
                                        <div class="color-label" style="position: absolute; bottom: 0; left: 0; right: 0; background: rgba(255,255,255,0.9); padding: 3px; font-size: 10px; text-align: center;">
                                            #{{ strtoupper($color) }}
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Color Information -->
        <div class="col-lg-4 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Color Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <tbody>
                            <tr>
                                <th>HEX</th>
                                <td>#{{ $hexUpper }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('#{{ $hex }}')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>RGB</th>
                                <td>{{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }}</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('rgb({{ $rgb['r'] }}, {{ $rgb['g'] }}, {{ $rgb['b'] }})')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>HSL</th>
                                <td>{{ $hsl['h'] }}°, {{ $hsl['s'] }}%, {{ $hsl['l'] }}%</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('hsl({{ $hsl['h'] }}, {{ $hsl['s'] }}%, {{ $hsl['l'] }}%)')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>HSV</th>
                                <td>{{ $hsv['h'] }}°, {{ $hsv['s'] }}%, {{ $hsv['v'] }}%</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('hsv({{ $hsv['h'] }}, {{ $hsv['s'] }}%, {{ $hsv['v'] }}%)')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <th>CMYK</th>
                                <td>{{ $cmyk['c'] }}%, {{ $cmyk['m'] }}%, {{ $cmyk['y'] }}%, {{ $cmyk['k'] }}%</td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('cmyk({{ $cmyk['c'] }}%, {{ $cmyk['m'] }}%, {{ $cmyk['y'] }}%, {{ $cmyk['k'] }}%)')">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Color Properties -->
                    <div class="mt-4">
                        <h6>Color Properties</h6>
                        <div class="row mt-3">
                            <div class="col-6">
                                <div class="property-card text-center p-3 bg-light rounded">
                                    <div class="property-value">{{ $brightness > 0.5 ? 'Light' : 'Dark' }}</div>
                                    <small class="text-muted">Brightness</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="property-card text-center p-3 bg-light rounded">
                                    <div class="property-value">{{ $hsl['s'] > 50 ? 'High' : 'Low' }}</div>
                                    <small class="text-muted">Saturation</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shades and Tints -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Shades & Tints</h5>
                </div>
                <div class="card-body">
                    <!-- Shades -->
                    <div class="shades-group mb-4">
                        <h6 class="mb-3">Shades (Darker)</h6>
                        <div class="row g-2">
                            @foreach($shades as $index => $shade)
                                <div class="col-4 col-sm-2">
                                    <a href="{{ route('color.details', $shade) }}"
                                       class="d-block text-decoration-none"
                                       title="#{{ strtoupper($shade) }}">
                                        <div class="color-shade mb-2"
                                             style="background-color: #{{ $shade }}; height: 40px; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tints -->
                    <div class="tints-group">
                        <h6 class="mb-3">Tints (Lighter)</h6>
                        <div class="row g-2">
                            @foreach($tints as $index => $tint)
                                <div class="col-4 col-sm-2">
                                    <a href="{{ route('color.details', $tint) }}"
                                       class="d-block text-decoration-none"
                                       title="#{{ strtoupper($tint) }}">
                                        <div class="color-tint mb-2"
                                             style="background-color: #{{ $tint }}; height: 40px; border-radius: 6px; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accessibility -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-universal-access me-2"></i>Accessibility</h5>
                </div>
                <div class="card-body">
                    <div class="accessibility-test mb-4">
                        <h6>Contrast Ratios</h6>
                        <div class="contrast-ratios mt-3">
                            @php
                                $contrastLabels = [
                                    'white' => 'White',
                                    'black' => 'Black',
                                    'light_gray' => 'Light Gray',
                                    'dark_gray' => 'Dark Gray'
                                ];
                            @endphp

                            @foreach($contrasts as $key => $ratio)
                                @php
                                    $rating = $ratio >= 7 ? 'AAA' : ($ratio >= 4.5 ? 'AA' : ($ratio >= 3 ? 'AA Large' : 'Fail'));
                                    $ratingClass = $ratio >= 4.5 ? 'text-success' : ($ratio >= 3 ? 'text-warning' : 'text-danger');
                                @endphp
                                <div class="contrast-item d-flex align-items-center justify-content-between mb-2 p-2 bg-light rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="contrast-preview me-3"
                                             style="width: 30px; height: 30px; background-color: {{ $key === 'white' ? '#FFFFFF' : ($key === 'black' ? '#000000' : ($key === 'light_gray' ? '#F8F9FA' : '#343A40')) }}; border: 2px solid #{{ $hex }}; border-radius: 5px;"></div>
                                        <div>
                                            <div>{{ $contrastLabels[$key] }}</div>
                                            <small class="text-muted">Ratio: {{ $ratio }}:1</small>
                                        </div>
                                    </div>
                                    <span class="{{ $ratingClass }} fw-bold">{{ $rating }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        WCAG 2.1 requires a contrast ratio of at least 4.5:1 for normal text and 3:1 for large text.
                    </small>
                </div>
            </div>
        </div>

        <!-- Color Variations & Usage -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Color Variations</h5>
                </div>
                <div class="card-body">
                    <!-- Related Colors -->
                    <div class="variation-group mb-4">
                        <h6>Related Colors</h6>
                        <div class="row mt-3 g-2">
                            @foreach($relatedColors as $related)
                                <div class="col-4">
                                    <a href="{{ route('color.details', $related['hex']) }}"
                                       class="d-block text-decoration-none"
                                       title="{{ $related['name'] }}">
                                        <div class="color-variation mb-2"
                                             style="background-color: #{{ $related['hex'] }}; height: 60px; border-radius: 8px; border: 2px solid #fff; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                                        </div>
                                        <div class="text-center">
                                            <small>#{{ strtoupper($related['hex']) }}</small><br>
                                            <small class="text-muted">{{ $related['name'] }}</small>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Usage Examples -->
                    <div class="variation-group">
                        <h6>Usage Examples</h6>
                        <div class="row mt-3 g-3">
                            <div class="col-6">
                                <div class="usage-example p-3 rounded" style="background-color: #{{ $hex }}; color: {{ $brightness > 0.5 ? '#000' : '#fff' }};">
                                    <div class="fw-bold">Text Color</div>
                                    <small>Sample text using this color</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="usage-example p-3 rounded border" style="background-color: #fff; border-color: #{{ $hex }} !important;">
                                    <div class="fw-bold" style="color: #{{ $hex }};">Border Color</div>
                                    <small style="color: #{{ $hex }};">Sample border usage</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Similar Colors -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Similar Colors</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($similarColors as $similar)
                            <div class="col-6">
                                <a href="{{ route('color.details', $similar['hex']) }}"
                                   class="d-block text-decoration-none">
                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                        <div class="me-3"
                                             style="width: 40px; height: 40px; background-color: #{{ $similar['hex'] }}; border-radius: 6px; border: 2px solid #fff;">
                                        </div>
                                        <div>
                                            <div class="fw-bold">#{{ strtoupper($similar['hex']) }}</div>
                                            <small class="text-muted">{{ $similar['name'] }}</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    a {
        color: var(--primary-color) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize color details page
    document.addEventListener('DOMContentLoaded', function() {
        // Update page title with color
        document.title = `#{{ $hexUpper }} - Color Details | Color System`;

        // Set active color in any picker on the page
        if (window.pickr && typeof window.pickr.setColor === 'function') {
            window.pickr.setColor('#{{ $hex }}');
        }

        // Update color input if exists
        if (document.getElementById('colorInput')) {
            document.getElementById('colorInput').value = '#{{ $hex }}';
        }
    });
</script>
@endpush
