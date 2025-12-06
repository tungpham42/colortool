@extends('layouts.app')

@section('title', 'Color System - Home')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-home me-2"></i>Welcome to Color System</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <h2 class="mb-4">Professional Color Management Tools</h2>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-eye-dropper fa-3x text-primary"></i>
                                        </div>
                                        <h5>Color Picker</h5>
                                        <p>Pick any color and get detailed information in HEX, RGB, HSL formats.</p>
                                        <a href="{{ route('color-picker') }}" class="btn btn-outline-primary">Try Now</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-blender fa-3x text-success"></i>
                                        </div>
                                        <h5>Color Mixer</h5>
                                        <p>Mix two colors with adjustable ratios to create perfect blends.</p>
                                        <a href="{{ route('color-mixer') }}" class="btn btn-outline-success">Try Now</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-image fa-3x text-warning"></i>
                                        </div>
                                        <h5>Image Extractor</h5>
                                        <p>Upload images to extract dominant colors and create palettes.</p>
                                        <a href="{{ route('image-extractor') }}" class="btn btn-outline-warning">Try Now</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fas fa-search fa-3x text-info"></i>
                                        </div>
                                        <h5>Color Lookup</h5>
                                        <p>Search and browse through thousands of named colors.</p>
                                        <a href="{{ route('color-lookup') }}" class="btn btn-outline-info">Try Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Color Preview</h5>
                            </div>
                            <div class="card-body text-center">
                                <div id="homeColorPreview" class="color-preview mb-3" style="background-color: #3498db;"></div>
                                <input type="color" id="homeColorPicker" value="#3498db" class="form-control form-control-color mb-3"
                                       onchange="document.getElementById('homeColorPreview').style.backgroundColor = this.value;">
                                <div class="mb-3">
                                    <input type="text" id="homeColorInput" value="#3498db" class="form-control text-center"
                                           oninput="if(/^#[0-9A-F]{6}$/i.test(this.value)) document.getElementById('homeColorPreview').style.backgroundColor = this.value;" onkeyup="if(event.key === 'Enter') viewColorDetails(this.value);">
                                </div>
                                <button id="viewColorDetails" class="btn btn-gradient w-100" onclick="viewColorDetails(document.getElementById('homeColorInput').value)">
                                    <i class="fas fa-eye me-2"></i>View Color Details
                                </button>
                            </div>
                        </div>

                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Popular Color Palettes</h5>
                            </div>
                            <div class="card-body">
                                <div class="color-grid">
                                    @foreach([
                                        ['#264653', '#2a9d8f', '#e9c46a', '#f4a261', '#e76f51'],
                                        ['#ffcdb2', '#ffb4a2', '#e5989b', '#b5838d', '#6d6875'],
                                        ['#03045e', '#0077b6', '#00b4d8', '#90e0ef', '#caf0f8'],
                                        ['#ff9f1c', '#ffbf69', '#ffffff', '#cbf3f0', '#2ec4b6']
                                    ] as $palette)
                                        <div class="palette mb-3">
                                            <div class="d-flex">
                                                @foreach($palette as $color)
                                                    <div style="flex: 1; height: 40px; background: {{ $color }};"></div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('homeColorPicker').addEventListener('input', function(e) {
        document.getElementById('homeColorInput').value = e.target.value;
    });

    document.getElementById('homeColorInput').addEventListener('input', function(e) {
        if (/^#[0-9A-F]{6}$/i.test(e.target.value)) {
            document.getElementById('homeColorPicker').value = e.target.value;
            document.getElementById('homeColorPreview').style.backgroundColor = e.target.value;
        }
    });
</script>
@endpush
