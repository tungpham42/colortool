<template>
    <h1 style="font-size: 40px; color: red">Hello mẹ!</h1>
    <div class="advanced-color-tool">
        <!-- Header Section -->
        <div class="header-section">
            <h1>🎨 Advanced Color System</h1>
            <p class="subtitle">
                Explore, mix, extract, and analyze colors with professional
                tools
            </p>
        </div>

        <!-- Main Color Input Section -->
        <div class="main-color-section">
            <div class="color-input-card">
                <h2>Base Color Selection</h2>
                <div class="color-input-group">
                    <div
                        class="color-preview-large"
                        :style="{ backgroundColor: baseColor }"
                    ></div>
                    <div class="color-input-controls">
                        <input
                            type="color"
                            v-model="baseColor"
                            @change="updateAllHarmonies"
                            class="color-picker"
                        />
                        <input
                            type="text"
                            v-model="baseColor"
                            @input="validateAndUpdate"
                            placeholder="#3498db"
                            class="color-input"
                        />
                        <div class="color-info-box">
                            <div class="info-row">
                                <span class="label">HEX:</span>
                                <span class="value">{{
                                    colorInfo?.hex || baseColor
                                }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">RGB:</span>
                                <span class="value">{{
                                    colorInfo?.rgb
                                        ? `rgb(${colorInfo.rgb.r}, ${colorInfo.rgb.g}, ${colorInfo.rgb.b})`
                                        : ""
                                }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">HSL:</span>
                                <span class="value">{{
                                    colorInfo?.hsl
                                        ? `hsl(${colorInfo.hsl.h}, ${colorInfo.hsl.s}%, ${colorInfo.hsl.l}%)`
                                        : ""
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Color Palettes -->
                <div class="quick-palettes">
                    <h4>Quick Select:</h4>
                    <div class="palette-grid">
                        <div
                            v-for="color in quickColors"
                            :key="color"
                            class="palette-color"
                            :style="{ backgroundColor: color }"
                            @click="setBaseColor(color)"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs-navigation">
            <button
                v-for="tab in tabs"
                :key="tab.id"
                :class="['tab-button', { active: activeTab === tab.id }]"
                @click="activeTab = tab.id"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- Shades & Tints Tab -->
        <div v-if="activeTab === 'shades'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">🎨</span> Shades & Tints</h3>
                <div class="shades-tints-container">
                    <div class="shades-section">
                        <h4>Shades (Adding Black)</h4>
                        <p class="section-description">
                            Darker variations by adding black to the base color
                        </p>
                        <div class="gradient-strip">
                            <div
                                v-for="shade in shades"
                                :key="shade.hex"
                                class="gradient-color"
                                :style="{ backgroundColor: shade.hex }"
                                @click="copyToClipboard(shade.hex)"
                            >
                                <div class="color-details">
                                    <span class="hex-code">{{
                                        shade.hex
                                    }}</span>
                                    <span class="percentage"
                                        >{{
                                            Math.round(shade.percentage)
                                        }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tints-section">
                        <h4>Tints (Adding White)</h4>
                        <p class="section-description">
                            Lighter variations by adding white to the base color
                        </p>
                        <div class="gradient-strip">
                            <div
                                v-for="tint in tints"
                                :key="tint.hex"
                                class="gradient-color"
                                :style="{ backgroundColor: tint.hex }"
                                @click="copyToClipboard(tint.hex)"
                            >
                                <div class="color-details">
                                    <span class="hex-code">{{ tint.hex }}</span>
                                    <span class="percentage"
                                        >{{
                                            Math.round(tint.percentage)
                                        }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monochromatic Tab -->
        <div v-if="activeTab === 'monochromatic'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">🌈</span> Monochromatic Colors</h3>
                <p class="section-description">
                    Variations of the same hue with different lightness levels
                </p>

                <div class="color-grid-large">
                    <div
                        v-for="(color, index) in monochromatic"
                        :key="color.hex"
                        class="color-card-large"
                        :style="{ backgroundColor: color.hex }"
                        @click="copyToClipboard(color.hex)"
                    >
                        <div class="card-content">
                            <div
                                class="color-preview-small"
                                :style="{ backgroundColor: color.hex }"
                            ></div>
                            <div class="color-info">
                                <h5>Color {{ index + 1 }}</h5>
                                <p class="hex">{{ color.hex }}</p>
                                <p class="lightness">
                                    Lightness: {{ color.lightness }}%
                                </p>
                                <div class="color-values">
                                    <span
                                        >RGB: {{ color.rgb.r }},
                                        {{ color.rgb.g }},
                                        {{ color.rgb.b }}</span
                                    >
                                    <span
                                        >HSL: {{ color.hsl.h }}°,
                                        {{ color.hsl.s }}%,
                                        {{ color.hsl.l }}%</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analogous Tab -->
        <div v-if="activeTab === 'analogous'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">🎯</span> Analogous Colors</h3>
                <p class="section-description">
                    Colors that are next to each other on the color wheel
                </p>

                <div class="analogous-container">
                    <div class="color-wheel-container">
                        <div class="color-wheel">
                            <div class="wheel-center"></div>
                            <div
                                v-for="color in analogous"
                                :key="color.position"
                                class="wheel-color"
                                :style="{
                                    backgroundColor: color.hex,
                                    transform: getAnalogousTransform(
                                        color.position
                                    ),
                                }"
                                @click="copyToClipboard(color.hex)"
                            >
                                <div class="wheel-tooltip">
                                    {{
                                        color.position === "center"
                                            ? "Base"
                                            : color.position
                                    }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="analogous-colors">
                        <div
                            v-for="color in analogous"
                            :key="color.position"
                            class="analogous-color"
                            :style="{ backgroundColor: color.hex }"
                            @click="copyToClipboard(color.hex)"
                        >
                            <div class="color-label">
                                <span class="position">{{
                                    color.position === "center"
                                        ? "Base"
                                        : color.position
                                }}</span>
                                <span class="hex">{{ color.hex }}</span>
                                <span class="hue">Hue: {{ color.hsl.h }}°</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complementary & Split Complementary Tab -->
        <div v-if="activeTab === 'complementary'" class="tab-content">
            <div class="harmony-section">
                <h3>
                    <span class="icon">⚖️</span> Complementary & Split
                    Complementary
                </h3>

                <div class="complementary-grid">
                    <div class="complementary-card">
                        <h4>Complementary Color</h4>
                        <p class="section-description">
                            Colors opposite each other on the color wheel
                        </p>
                        <div class="complementary-pair">
                            <div
                                class="color-pair"
                                :style="{ backgroundColor: baseColor }"
                                @click="copyToClipboard(baseColor)"
                            >
                                <div class="pair-label">
                                    <span>Base</span>
                                    <span>{{ baseColor }}</span>
                                </div>
                            </div>
                            <div
                                class="color-pair"
                                v-if="complementary"
                                :style="{
                                    backgroundColor:
                                        complementary.complementary?.hex,
                                }"
                                @click="
                                    copyToClipboard(
                                        complementary.complementary?.hex
                                    )
                                "
                            >
                                <div class="pair-label">
                                    <span>Complementary</span>
                                    <span>{{
                                        complementary.complementary?.hex
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="split-complementary-card">
                        <h4>Split Complementary</h4>
                        <p class="section-description">
                            Base color plus two colors adjacent to its
                            complement
                        </p>
                        <div class="split-complementary-triangle">
                            <div
                                v-for="color in splitComplementary"
                                :key="color.position"
                                class="split-color"
                                :style="{ backgroundColor: color.hex }"
                                @click="copyToClipboard(color.hex)"
                            >
                                <div class="split-label">
                                    <span>{{ color.position }}</span>
                                    <span>{{ color.hex }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Triadic & Tetradic Tab -->
        <div v-if="activeTab === 'triadic'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">🔺</span> Triadic & Tetradic Colors</h3>

                <div class="triadic-tetradic-grid">
                    <div class="triadic-card">
                        <h4>Triadic Colors</h4>
                        <p class="section-description">
                            Three colors equally spaced on the color wheel
                        </p>
                        <div class="triadic-group">
                            <div
                                v-for="color in triadic"
                                :key="color.position"
                                class="triadic-color"
                                :style="{ backgroundColor: color.hex }"
                                @click="copyToClipboard(color.hex)"
                            >
                                <div class="triadic-label">
                                    <span>{{ color.position }}</span>
                                    <span>{{ color.hex }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tetradic-card">
                        <h4>Tetradic (Rectangular) Colors</h4>
                        <p class="section-description">
                            Four colors forming a rectangle on the color wheel
                        </p>
                        <div class="tetradic-group">
                            <div
                                v-for="color in tetradic"
                                :key="color.position"
                                class="tetradic-color"
                                :style="{ backgroundColor: color.hex }"
                                @click="copyToClipboard(color.hex)"
                            >
                                <div class="tetradic-label">
                                    <span>{{ color.position }}</span>
                                    <span>{{ color.hex }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Square Colors Tab -->
        <div v-if="activeTab === 'square'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">⬛</span> Square Colors</h3>
                <p class="section-description">
                    Four colors evenly spaced on the color wheel
                </p>

                <div class="square-container">
                    <div class="square-grid">
                        <div
                            v-for="color in squareColors"
                            :key="color.position"
                            class="square-color"
                            :style="{ backgroundColor: color.hex }"
                            @click="copyToClipboard(color.hex)"
                        >
                            <div class="square-label">
                                <span>{{ color.position }}</span>
                                <span>{{ color.hex }}</span>
                                <span>Hue: {{ color.hsl.h }}°</span>
                            </div>
                        </div>
                    </div>

                    <div class="square-diagram">
                        <div class="diagram-wheel">
                            <div
                                v-for="color in squareColors"
                                :key="color.position"
                                class="diagram-point"
                                :style="{
                                    backgroundColor: color.hex,
                                    transform: getSquareTransform(
                                        color.position
                                    ),
                                }"
                            ></div>
                            <div class="diagram-lines">
                                <div class="line"></div>
                                <div class="line"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Mixer Tab -->
        <div v-if="activeTab === 'mixer'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">🎭</span> Color Mixer</h3>

                <div class="mixer-container">
                    <div class="mixer-controls">
                        <div class="mixer-inputs">
                            <div class="color-input-mixer">
                                <input
                                    type="color"
                                    v-model="mixColor1"
                                    @change="mixColors"
                                />
                                <input
                                    type="text"
                                    v-model="mixColor1"
                                    @input="validateAndMix"
                                    placeholder="#ff0000"
                                />
                                <div
                                    class="color-preview-mixer"
                                    :style="{ backgroundColor: mixColor1 }"
                                ></div>
                            </div>

                            <div class="mixer-slider">
                                <div class="slider-labels">
                                    <span>{{ mixColor1 }}</span>
                                    <span>{{ mixRatio }}%</span>
                                    <span>{{ mixColor2 }}</span>
                                </div>
                                <input
                                    type="range"
                                    v-model="mixRatio"
                                    min="0"
                                    max="100"
                                    @input="mixColors"
                                    class="slider"
                                />
                                <div class="slider-ticks">
                                    <span>0%</span>
                                    <span>25%</span>
                                    <span>50%</span>
                                    <span>75%</span>
                                    <span>100%</span>
                                </div>
                            </div>

                            <div class="color-input-mixer">
                                <input
                                    type="color"
                                    v-model="mixColor2"
                                    @change="mixColors"
                                />
                                <input
                                    type="text"
                                    v-model="mixColor2"
                                    @input="validateAndMix"
                                    placeholder="#0000ff"
                                />
                                <div
                                    class="color-preview-mixer"
                                    :style="{ backgroundColor: mixColor2 }"
                                ></div>
                            </div>
                        </div>

                        <div class="mixed-result" v-if="mixedColor">
                            <h4>Mixed Color Result</h4>
                            <div
                                class="result-color"
                                :style="{ backgroundColor: mixedColor.hex }"
                            >
                                <div class="result-details">
                                    <p>
                                        <strong>HEX:</strong>
                                        {{ mixedColor.hex }}
                                    </p>
                                    <p>
                                        <strong>RGB:</strong> rgb({{
                                            mixedColor.rgb.r
                                        }}, {{ mixedColor.rgb.g }},
                                        {{ mixedColor.rgb.b }})
                                    </p>
                                    <p>
                                        <strong>HSL:</strong> hsl({{
                                            mixedColor.hsl.h
                                        }}, {{ mixedColor.hsl.s }}%,
                                        {{ mixedColor.hsl.l }}%)
                                    </p>
                                    <button
                                        @click="copyToClipboard(mixedColor.hex)"
                                        class="copy-btn"
                                    >
                                        Copy Color
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preset-mixes">
                        <h4>Popular Color Mixes</h4>
                        <div class="preset-grid">
                            <div
                                v-for="preset in presetMixes"
                                :key="preset.name"
                                class="preset-item"
                                @click="
                                    setMixColors(preset.color1, preset.color2)
                                "
                            >
                                <div class="preset-colors">
                                    <div
                                        class="preset-color"
                                        :style="{
                                            backgroundColor: preset.color1,
                                        }"
                                    ></div>
                                    <div
                                        class="preset-color"
                                        :style="{
                                            backgroundColor: preset.color2,
                                        }"
                                    ></div>
                                </div>
                                <span class="preset-name">{{
                                    preset.name
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Contrast Checker -->
        <div v-if="activeTab === 'contrast'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">📊</span> Color Contrast Checker</h3>
                <p class="section-description">
                    Check WCAG compliance for text readability
                </p>

                <div class="contrast-container">
                    <div class="contrast-inputs">
                        <div class="contrast-input-group">
                            <label>Foreground (Text)</label>
                            <input
                                type="color"
                                v-model="foregroundColor"
                                @change="checkContrast"
                            />
                            <input
                                type="text"
                                v-model="foregroundColor"
                                @input="validateAndCheckContrast"
                            />
                            <div
                                class="color-sample"
                                :style="{ backgroundColor: foregroundColor }"
                            ></div>
                        </div>

                        <div class="contrast-input-group">
                            <label>Background</label>
                            <input
                                type="color"
                                v-model="backgroundColor"
                                @change="checkContrast"
                            />
                            <input
                                type="text"
                                v-model="backgroundColor"
                                @input="validateAndCheckContrast"
                            />
                            <div
                                class="color-sample"
                                :style="{ backgroundColor: backgroundColor }"
                            ></div>
                        </div>
                    </div>

                    <div class="contrast-result" v-if="contrastRatio">
                        <div
                            class="contrast-preview"
                            :style="{
                                backgroundColor: backgroundColor,
                                color: foregroundColor,
                            }"
                        >
                            <h4>Preview Text</h4>
                            <p class="large-text">This is large text (18px+)</p>
                            <p class="normal-text">
                                This is normal text (14px-17px)
                            </p>
                            <p class="small-text">
                                This is small text (less than 14px)
                            </p>
                        </div>

                        <div class="contrast-details">
                            <div class="contrast-score">
                                <h4>
                                    Contrast Ratio:
                                    {{ contrastRatio.contrast_ratio }}:1
                                </h4>
                                <div class="score-bar">
                                    <div
                                        class="score-fill"
                                        :style="{
                                            width: calculateScoreWidth(),
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <div class="wcag-compliance">
                                <h4>WCAG Compliance</h4>
                                <div class="compliance-grid">
                                    <div
                                        v-for="(
                                            result, rating
                                        ) in contrastRatio.wcag_rating"
                                        :key="rating"
                                        :class="[
                                            'compliance-item',
                                            result === 'PASS' ? 'pass' : 'fail',
                                        ]"
                                    >
                                        <span class="rating-name">{{
                                            rating
                                        }}</span>
                                        <span class="rating-result">{{
                                            result
                                        }}</span>
                                        <span class="rating-info"
                                            >(≥
                                            {{
                                                getWcagThreshold(rating)
                                            }}:1)</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export & Tools -->
        <div v-if="activeTab === 'export'" class="tab-content">
            <div class="harmony-section">
                <h3><span class="icon">📤</span> Export & Tools</h3>

                <div class="export-container">
                    <div class="export-format-selector">
                        <h4>Export Format</h4>
                        <div class="format-buttons">
                            <button
                                v-for="format in exportFormats"
                                :key="format.id"
                                :class="[
                                    'format-btn',
                                    { active: exportFormat === format.id },
                                ]"
                                @click="selectExportFormat(format.id)"
                            >
                                {{ format.label }}
                            </button>
                        </div>
                    </div>

                    <div class="export-preview">
                        <h4>Preview</h4>
                        <textarea
                            v-model="exportText"
                            class="export-textarea"
                            rows="12"
                            readonly
                        ></textarea>
                        <div class="export-actions">
                            <button @click="copyExportText" class="action-btn">
                                📋 Copy to Clipboard
                            </button>
                            <button @click="downloadExport" class="action-btn">
                                ⬇️ Download File
                            </button>
                        </div>
                    </div>

                    <div class="export-options">
                        <h4>Export Options</h4>
                        <div class="options-grid">
                            <label class="option-item">
                                <input
                                    type="checkbox"
                                    v-model="exportOptions.includeHex"
                                    @change="generateExport"
                                />
                                Include HEX values
                            </label>
                            <label class="option-item">
                                <input
                                    type="checkbox"
                                    v-model="exportOptions.includeRgb"
                                    @change="generateExport"
                                />
                                Include RGB values
                            </label>
                            <label class="option-item">
                                <input
                                    type="checkbox"
                                    v-model="exportOptions.includeHsl"
                                    @change="generateExport"
                                />
                                Include HSL values
                            </label>
                            <label class="option-item">
                                <input
                                    type="checkbox"
                                    v-model="exportOptions.includeCmyk"
                                    @change="generateExport"
                                />
                                Include CMYK values
                            </label>
                            <label class="option-item">
                                <input
                                    type="checkbox"
                                    v-model="exportOptions.prettyPrint"
                                    @change="generateExport"
                                />
                                Pretty print
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div v-if="showToast" class="toast" :class="toastType">
            {{ toastMessage }}
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <p>🎨 Color System v1.0 | Made with Laravel & Vue.js</p>
            <p class="footer-note">
                Click on any color to copy its HEX code to clipboard
            </p>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            baseColor: "#3498db",
            colorInfo: null,
            shades: [],
            tints: [],
            monochromatic: [],
            analogous: [],
            complementary: null,
            triadic: [],
            splitComplementary: [],
            tetradic: [],
            squareColors: [],

            // Mixer
            mixColor1: "#ff0000",
            mixColor2: "#0000ff",
            mixRatio: 50,
            mixedColor: null,

            // Contrast Checker
            foregroundColor: "#000000",
            backgroundColor: "#ffffff",
            contrastRatio: null,

            // Export
            exportFormat: "json",
            exportText: "",
            exportOptions: {
                includeHex: true,
                includeRgb: true,
                includeHsl: true,
                includeCmyk: false,
                prettyPrint: true,
            },

            // UI State
            activeTab: "shades",
            showToast: false,
            toastMessage: "",
            toastType: "success",

            // Quick colors palette
            quickColors: [
                "#3498db",
                "#2ecc71",
                "#e74c3c",
                "#f39c12",
                "#9b59b6",
                "#1abc9c",
                "#34495e",
                "#e67e22",
                "#d35400",
                "#c0392b",
                "#16a085",
                "#27ae60",
                "#2980b9",
                "#8e44ad",
                "#2c3e50",
            ],

            // Preset color mixes
            presetMixes: [
                { name: "Red + Blue", color1: "#ff0000", color2: "#0000ff" },
                { name: "Yellow + Blue", color1: "#ffff00", color2: "#0000ff" },
                { name: "Red + Green", color1: "#ff0000", color2: "#00ff00" },
                {
                    name: "Purple + Orange",
                    color1: "#800080",
                    color2: "#ffa500",
                },
                { name: "Teal + Pink", color1: "#008080", color2: "#ff69b4" },
                { name: "Gold + Navy", color1: "#ffd700", color2: "#000080" },
            ],

            // Export formats
            exportFormats: [
                { id: "json", label: "JSON" },
                { id: "css", label: "CSS Variables" },
                { id: "scss", label: "SCSS Variables" },
                { id: "tailwind", label: "Tailwind Config" },
                { id: "csv", label: "CSV" },
            ],

            // Tabs
            tabs: [
                { id: "shades", label: "Shades & Tints" },
                { id: "monochromatic", label: "Monochromatic" },
                { id: "analogous", label: "Analogous" },
                { id: "complementary", label: "Complementary" },
                { id: "triadic", label: "Triadic & Tetradic" },
                { id: "square", label: "Square" },
                { id: "mixer", label: "Color Mixer" },
                { id: "contrast", label: "Contrast Checker" },
                { id: "export", label: "Export" },
            ],
        };
    },

    methods: {
        async updateAllHarmonies() {
            try {
                // Update color info
                const colorResponse = await axios.get("/api/colors/picker", {
                    params: { color: this.baseColor },
                });
                this.colorInfo = colorResponse.data;

                // Get all harmonies
                const harmoniesResponse = await axios.get(
                    "/api/colors/harmonies",
                    {
                        params: { color: this.baseColor },
                    }
                );

                const harmonies = harmoniesResponse.data.harmonies;
                this.shades = harmonies.shades || [];
                this.tints = harmonies.tints || [];
                this.monochromatic = harmonies.monochromatic || [];
                this.analogous = harmonies.analogous || [];
                this.complementary = harmonies.complementary || null;
                this.triadic = harmonies.triadic || [];
                this.splitComplementary = harmonies.split_complementary || [];
                this.tetradic = harmonies.tetradic || [];
                this.squareColors = harmonies.square || [];

                // Generate export text
                this.generateExport();
            } catch (error) {
                console.error("Error fetching harmonies:", error);
                this.showNotification("Error loading color harmonies", "error");
            }
        },

        async mixColors() {
            try {
                const response = await axios.post("/api/colors/mix", {
                    color1: this.mixColor1,
                    color2: this.mixColor2,
                    ratio: this.mixRatio / 100,
                });
                this.mixedColor = response.data;
            } catch (error) {
                console.error("Error mixing colors:", error);
                this.showNotification("Error mixing colors", "error");
            }
        },

        async checkContrast() {
            try {
                const response = await axios.get("/api/colors/contrast-ratio", {
                    params: {
                        color1: this.foregroundColor,
                        color2: this.backgroundColor,
                    },
                });
                this.contrastRatio = response.data;
            } catch (error) {
                console.error("Error checking contrast:", error);
                this.showNotification("Error checking contrast ratio", "error");
            }
        },

        validateAndUpdate() {
            const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            if (hexRegex.test(this.baseColor)) {
                this.updateAllHarmonies();
            }
        },

        validateAndMix() {
            const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            if (
                hexRegex.test(this.mixColor1) &&
                hexRegex.test(this.mixColor2)
            ) {
                this.mixColors();
            }
        },

        validateAndCheckContrast() {
            const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
            if (
                hexRegex.test(this.foregroundColor) &&
                hexRegex.test(this.backgroundColor)
            ) {
                this.checkContrast();
            }
        },

        setBaseColor(color) {
            this.baseColor = color;
            this.updateAllHarmonies();
        },

        setMixColors(color1, color2) {
            this.mixColor1 = color1;
            this.mixColor2 = color2;
            this.mixColors();
        },

        getAnalogousTransform(position) {
            const angles = {
                center: 0,
                "left-1": -30,
                "right-1": 30,
                "left-2": -60,
                "right-2": 60,
            };
            const angle = angles[position] || 0;
            return `rotate(${angle}deg) translate(120px) rotate(${-angle}deg)`;
        },

        getSquareTransform(position) {
            const angles = {
                primary: 0,
                secondary: 90,
                tertiary: 180,
                quaternary: 270,
            };
            const angle = angles[position] || 0;
            return `rotate(${angle}deg) translate(100px) rotate(${-angle}deg)`;
        },

        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                this.showNotification(`Copied: ${text}`, "success");
            } catch (error) {
                console.error("Error copying to clipboard:", error);
                this.showNotification("Error copying to clipboard", "error");
            }
        },

        selectExportFormat(format) {
            this.exportFormat = format;
            this.generateExport();
        },

        generateExport() {
            switch (this.exportFormat) {
                case "json":
                    this.generateJSON();
                    break;
                case "css":
                    this.generateCSS();
                    break;
                case "scss":
                    this.generateSCSS();
                    break;
                case "tailwind":
                    this.generateTailwind();
                    break;
                case "csv":
                    this.generateCSV();
                    break;
            }
        },

        generateJSON() {
            const palette = {
                base: {
                    hex: this.baseColor,
                    rgb: this.colorInfo?.rgb,
                    hsl: this.colorInfo?.hsl,
                },
                harmonies: {
                    shades: this.shades.map((s) => ({
                        hex: s.hex,
                        rgb: s.rgb,
                        percentage: s.percentage,
                    })),
                    tints: this.tints.map((t) => ({
                        hex: t.hex,
                        rgb: t.rgb,
                        percentage: t.percentage,
                    })),
                    monochromatic: this.monochromatic.map((m) => ({
                        hex: m.hex,
                        rgb: m.rgb,
                        hsl: m.hsl,
                        lightness: m.lightness,
                    })),
                    analogous: this.analogous.map((a) => ({
                        hex: a.hex,
                        rgb: a.rgb,
                        hsl: a.hsl,
                        position: a.position,
                    })),
                },
            };

            this.exportText = this.exportOptions.prettyPrint
                ? JSON.stringify(palette, null, 2)
                : JSON.stringify(palette);
        },

        generateCSS() {
            let css = `/* Color Palette - Base: ${this.baseColor} */\n`;
            css += `:root {\n`;

            // Base color
            css += `  /* Base Color */\n`;
            css += `  --color-base: ${this.baseColor};\n`;

            if (this.exportOptions.includeRgb && this.colorInfo?.rgb) {
                css += `  --color-base-rgb: ${this.colorInfo.rgb.r}, ${this.colorInfo.rgb.g}, ${this.colorInfo.rgb.b};\n`;
            }

            if (this.exportOptions.includeHsl && this.colorInfo?.hsl) {
                css += `  --color-base-hsl: ${this.colorInfo.hsl.h}, ${this.colorInfo.hsl.s}%, ${this.colorInfo.hsl.l}%;\n`;
            }

            // Shades
            css += `\n  /* Shades */\n`;
            this.shades.forEach((shade, index) => {
                css += `  --color-shade-${index + 1}: ${shade.hex};\n`;
            });

            // Tints
            css += `\n  /* Tints */\n`;
            this.tints.forEach((tint, index) => {
                css += `  --color-tint-${index + 1}: ${tint.hex};\n`;
            });

            // Monochromatic
            css += `\n  /* Monochromatic */\n`;
            this.monochromatic.forEach((color, index) => {
                css += `  --color-mono-${index + 1}: ${color.hex};\n`;
            });

            css += `}\n`;
            this.exportText = css;
        },

        generateSCSS() {
            let scss = `/* Color Palette SCSS Variables */\n\n`;

            // Base color
            scss += `// Base Color\n`;
            scss += `$color-base: ${this.baseColor};\n`;

            if (this.exportOptions.includeRgb && this.colorInfo?.rgb) {
                scss += `$color-base-rgb: ${this.colorInfo.rgb.r}, ${this.colorInfo.rgb.g}, ${this.colorInfo.rgb.b};\n`;
            }

            // Shades map
            scss += `\n// Shades Map\n`;
            scss += `$color-shades: (\n`;
            this.shades.forEach((shade, index) => {
                scss += `  '${index + 1}': ${shade.hex},\n`;
            });
            scss += `);\n`;

            // Tints map
            scss += `\n// Tints Map\n`;
            scss += `$color-tints: (\n`;
            this.tints.forEach((tint, index) => {
                scss += `  '${index + 1}': ${tint.hex},\n`;
            });
            scss += `);\n`;

            // Function to get shade
            scss += `\n// Function to get shade\n`;
            scss += `@function shade($level) {\n`;
            scss += `  @return map-get($color-shades, $level);\n`;
            scss += `}\n`;

            this.exportText = scss;
        },

        generateTailwind() {
            let config = `/** @type {import('tailwindcss').Config} */\n`;
            config += `module.exports = {\n`;
            config += `  theme: {\n`;
            config += `    extend: {\n`;
            config += `      colors: {\n`;

            // Base color
            config += `        'base': '${this.baseColor}',\n`;

            // Shades
            this.shades.forEach((shade, index) => {
                config += `        'shade-${index + 1}': '${shade.hex}',\n`;
            });

            // Tints
            this.tints.forEach((tint, index) => {
                config += `        'tint-${index + 1}': '${tint.hex}',\n`;
            });

            config += `      }\n`;
            config += `    }\n`;
            config += `  }\n`;
            config += `}\n`;

            this.exportText = config;
        },

        generateCSV() {
            let csv = "Type,Name,HEX,RGB,HSL,Percentage,Position\n";

            // Base color
            csv += `Base,Base Color,${this.baseColor}`;
            if (this.colorInfo?.rgb) {
                csv += `,"${this.colorInfo.rgb.r},${this.colorInfo.rgb.g},${this.colorInfo.rgb.b}"`;
            }
            if (this.colorInfo?.hsl) {
                csv += `,"${this.colorInfo.hsl.h},${this.colorInfo.hsl.s}%,${this.colorInfo.hsl.l}%"`;
            }
            csv += `,100%,center\n`;

            // Shades
            this.shades.forEach((shade, index) => {
                csv += `Shade,Shade ${index + 1},${shade.hex}`;
                if (shade.rgb) {
                    csv += `,"${shade.rgb.r},${shade.rgb.g},${shade.rgb.b}"`;
                }
                csv += `,,${shade.percentage}%,-\n`;
            });

            // Tints
            this.tints.forEach((tint, index) => {
                csv += `Tint,Tint ${index + 1},${tint.hex}`;
                if (tint.rgb) {
                    csv += `,"${tint.rgb.r},${tint.rgb.g},${tint.rgb.b}"`;
                }
                csv += `,,${tint.percentage}%,-\n`;
            });

            this.exportText = csv;
        },

        copyExportText() {
            this.copyToClipboard(this.exportText);
        },

        downloadExport() {
            const blob = new Blob([this.exportText], { type: "text/plain" });
            const url = URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download = `color-palette-${this.baseColor.replace("#", "")}.${
                this.exportFormat
            }`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            this.showNotification("Export downloaded successfully", "success");
        },

        calculateScoreWidth() {
            if (!this.contrastRatio) return "0%";
            const ratio = parseFloat(this.contrastRatio.contrast_ratio);
            return Math.min((ratio / 21) * 100, 100) + "%";
        },

        getWcagThreshold(rating) {
            const thresholds = {
                "AAA Large Text": 3.0,
                "AA Large Text": 3.0,
                "AAA Normal Text": 7.0,
                "AA Normal Text": 4.5,
                "AAA UI Components": 3.0,
                "AA UI Components": 3.0,
            };
            return thresholds[rating] || "N/A";
        },

        showNotification(message, type = "success") {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;

            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },
    },

    watch: {
        baseColor() {
            this.validateAndUpdate();
        },
        foregroundColor() {
            this.validateAndCheckContrast();
        },
        backgroundColor() {
            this.validateAndCheckContrast();
        },
        exportFormat() {
            this.generateExport();
        },
    },

    mounted() {
        this.updateAllHarmonies();
        this.mixColors();
        this.checkContrast();
        this.generateExport();
    },
};
console.log("AdvancedColorTool đang khởi động...");
</script>

<style scoped>
/* Base Styles */
.advanced-color-tool {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

/* Header */
.header-section {
    text-align: center;
    padding: 40px 20px;
    color: white;
    margin-bottom: 30px;
}

.header-section h1 {
    font-size: 3rem;
    margin-bottom: 10px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* Main Color Section */
.main-color-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.color-input-card h2 {
    color: #2c3e50;
    margin-bottom: 20px;
    font-size: 1.8rem;
}

.color-input-group {
    display: flex;
    gap: 30px;
    align-items: center;
    margin-bottom: 30px;
}

.color-preview-large {
    width: 150px;
    height: 150px;
    border-radius: 20px;
    border: 5px solid white;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.color-input-controls {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.color-picker {
    width: 60px;
    height: 60px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    align-self: flex-start;
}

.color-input {
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 16px;
    width: 200px;
    transition: border-color 0.3s;
}

.color-input:focus {
    outline: none;
    border-color: #667eea;
}

.color-info-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 5px solid #667eea;
}

.info-row {
    display: flex;
    margin-bottom: 8px;
    font-family: monospace;
}

.info-row:last-child {
    margin-bottom: 0;
}

.label {
    font-weight: bold;
    color: #2c3e50;
    min-width: 50px;
    margin-right: 10px;
}

.value {
    color: #34495e;
}

/* Quick Palettes */
.quick-palettes h4 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.palette-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(40px, 1fr));
    gap: 10px;
}

.palette-color {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 2px solid white;
}

.palette-color:hover {
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Tabs Navigation */
.tabs-navigation {
    display: flex;
    overflow-x: auto;
    gap: 5px;
    margin-bottom: 30px;
    background: white;
    border-radius: 15px;
    padding: 10px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.tab-button {
    padding: 15px 25px;
    border: none;
    background: #f8f9fa;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    transition: all 0.3s;
    white-space: nowrap;
}

.tab-button:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.tab-button.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

/* Tab Content */
.tab-content {
    background: white;
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.harmony-section h3 {
    color: #2c3e50;
    margin-bottom: 10px;
    font-size: 1.8rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.icon {
    font-size: 1.5rem;
}

.section-description {
    color: #7f8c8d;
    margin-bottom: 30px;
    font-size: 1rem;
    line-height: 1.6;
}

/* Shades & Tints */
.shades-tints-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.gradient-strip {
    display: flex;
    height: 120px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.gradient-color {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 10px;
    cursor: pointer;
    transition: transform 0.2s;
    position: relative;
    overflow: hidden;
}

.gradient-color:hover {
    transform: scale(1.05);
    z-index: 1;
}

.gradient-color:hover .color-details {
    opacity: 1;
    transform: translateY(0);
}

.color-details {
    background: rgba(255, 255, 255, 0.9);
    padding: 8px;
    border-radius: 8px;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s;
    text-align: center;
}

.hex-code {
    font-family: monospace;
    font-size: 12px;
    font-weight: bold;
    color: #2c3e50;
    display: block;
}

.percentage {
    font-size: 11px;
    color: #7f8c8d;
}

/* Color Grid */
.color-grid-large {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.color-card-large {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.3s;
}

.color-card-large:hover {
    transform: translateY(-10px);
}

.card-content {
    display: flex;
    height: 150px;
}

.color-preview-small {
    width: 80px;
    flex-shrink: 0;
}

.color-info {
    padding: 20px;
    flex: 1;
}

.color-info h5 {
    margin: 0 0 10px 0;
    color: #2c3e50;
}

.hex {
    font-family: monospace;
    font-size: 14px;
    color: #667eea;
    margin: 5px 0;
}

.lightness {
    font-size: 12px;
    color: #7f8c8d;
    margin: 5px 0;
}

.color-values {
    margin-top: 10px;
    font-size: 11px;
    color: #95a5a6;
}

.color-values span {
    display: block;
    margin-bottom: 3px;
}

/* Analogous */
.analogous-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.color-wheel-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.color-wheel {
    position: relative;
    width: 300px;
    height: 300px;
}

.wheel-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background-color: v-bind(baseColor);
    border-radius: 50%;
    border: 4px solid white;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

.wheel-color {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid white;
    transform-origin: 0 0;
    cursor: pointer;
    transition: transform 0.3s;
}

.wheel-color:hover {
    transform: scale(1.2);
}

.wheel-tooltip {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s;
}

.wheel-color:hover .wheel-tooltip {
    opacity: 1;
}

.analogous-colors {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.analogous-color {
    height: 120px;
    border-radius: 10px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.color-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.9);
    padding: 10px;
    transform: translateY(100%);
    transition: transform 0.3s;
}

.analogous-color:hover .color-label {
    transform: translateY(0);
}

.position {
    display: block;
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 3px;
}

.hex {
    display: block;
    font-family: monospace;
    font-size: 12px;
    color: #667eea;
    margin-bottom: 3px;
}

.hue {
    display: block;
    font-size: 11px;
    color: #7f8c8d;
}

/* Complementary Grid */
.complementary-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.complementary-pair {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.color-pair {
    flex: 1;
    height: 150px;
    border-radius: 15px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.pair-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.9);
    padding: 15px;
    text-align: center;
    transform: translateY(100%);
    transition: transform 0.3s;
}

.color-pair:hover .pair-label {
    transform: translateY(0);
}

.pair-label span {
    display: block;
}

.pair-label span:first-child {
    font-weight: bold;
    color: #2c3e50;
    margin-bottom: 5px;
}

.pair-label span:last-child {
    font-family: monospace;
    font-size: 14px;
    color: #667eea;
}

/* Split Complementary */
.split-complementary-triangle {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
    position: relative;
    margin-top: 20px;
}

.split-color {
    width: 120px;
    height: 120px;
    border-radius: 15px;
    cursor: pointer;
    position: absolute;
    transition: transform 0.3s;
}

.split-color:nth-child(1) {
    top: 0;
    left: 50%;
    transform: translateX(-50%);
}

.split-color:nth-child(2) {
    bottom: 0;
    left: 25%;
    transform: translateX(-25%);
}

.split-color:nth-child(3) {
    bottom: 0;
    right: 25%;
    transform: translateX(25%);
}

.split-color:hover {
    transform: scale(1.1);
    z-index: 1;
}

.split-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.9);
    padding: 8px;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.split-color:hover .split-label {
    opacity: 1;
}

.split-label span {
    display: block;
    font-size: 12px;
}

.split-label span:first-child {
    font-weight: bold;
    color: #2c3e50;
}

.split-label span:last-child {
    font-family: monospace;
    color: #667eea;
}

/* Triadic & Tetradic */
.triadic-tetradic-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.triadic-group {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.triadic-color {
    flex: 1;
    height: 150px;
    border-radius: 15px;
    cursor: pointer;
    position: relative;
    transition: transform 0.3s;
}

.triadic-color:hover {
    transform: translateY(-10px);
}

.triadic-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.9);
    padding: 10px;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.triadic-color:hover .triadic-label {
    opacity: 1;
}

.triadic-label span {
    display: block;
    margin-bottom: 3px;
}

.tetradic-group {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.tetradic-color {
    height: 120px;
    border-radius: 15px;
    cursor: pointer;
    position: relative;
    transition: transform 0.3s;
}

.tetradic-color:hover {
    transform: scale(1.05);
}

.tetradic-label {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.9);
    padding: 8px;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s;
}

.tetradic-color:hover .tetradic-label {
    opacity: 1;
}

/* Square Colors */
.square-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.square-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.square-color {
    height: 150px;
    border-radius: 15px;
    cursor: pointer;
    position: relative;
    transition: transform 0.3s;
}

.square-color:hover {
    transform: rotate(5deg);
}

.square-label {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.95);
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    opacity: 0;
    transition: opacity 0.3s;
    width: 80%;
}

.square-color:hover .square-label {
    opacity: 1;
}

.square-label span {
    display: block;
    margin-bottom: 5px;
    font-size: 12px;
}

.square-label span:first-child {
    font-weight: bold;
    color: #2c3e50;
    font-size: 14px;
}

.square-diagram {
    display: flex;
    justify-content: center;
    align-items: center;
}

.diagram-wheel {
    position: relative;
    width: 200px;
    height: 200px;
}

.diagram-point {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid white;
    transform-origin: 0 0;
}

.diagram-lines {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.diagram-lines .line {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 2px;
    height: 100px;
    background: #95a5a6;
    transform-origin: center;
}

.diagram-lines .line:nth-child(1) {
    transform: translate(-50%, -50%) rotate(45deg);
}

.diagram-lines .line:nth-child(2) {
    transform: translate(-50%, -50%) rotate(135deg);
}

/* Mixer */
.mixer-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

.mixer-controls {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 15px;
}

.mixer-inputs {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 30px;
}

.color-input-mixer {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
}

.mixer-slider {
    flex: 1;
}

.slider-labels {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    font-size: 14px;
    font-weight: 500;
}

.slider {
    width: 100%;
    height: 20px;
    appearance: none;
    -webkit-appearance: none;
    background: linear-gradient(to right, v-bind(mixColor1), v-bind(mixColor2));
    border-radius: 10px;
    outline: none;
}

.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: white;
    border: 3px solid #667eea;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.slider-ticks {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
    font-size: 12px;
    color: #7f8c8d;
}

.color-preview-mixer {
    width: 80px;
    height: 80px;
    border-radius: 15px;
    border: 3px solid white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.mixed-result {
    background: white;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e0e0e0;
}

.result-color {
    display: flex;
    gap: 30px;
    margin-top: 20px;
}

.result-color > div:first-child {
    width: 100px;
    height: 100px;
    border-radius: 15px;
    flex-shrink: 0;
}

.result-details {
    flex: 1;
}

.result-details p {
    margin-bottom: 10px;
    font-size: 14px;
}

.copy-btn {
    margin-top: 15px;
    padding: 10px 20px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.copy-btn:hover {
    background: #5a67d8;
}

.preset-mixes {
    background: white;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e0e0e0;
}

.preset-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.preset-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.preset-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.preset-colors {
    display: flex;
}

.preset-color {
    width: 30px;
    height: 30px;
    border: 2px solid white;
}

.preset-color:first-child {
    border-radius: 8px 0 0 8px;
}

.preset-color:last-child {
    border-radius: 0 8px 8px 0;
}

.preset-name {
    font-size: 14px;
    font-weight: 500;
    color: #2c3e50;
}

/* Contrast Checker */
.contrast-container {
    background: white;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e0e0e0;
}

.contrast-inputs {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
    margin-bottom: 30px;
}

.contrast-input-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.contrast-input-group label {
    font-weight: 600;
    color: #2c3e50;
    font-size: 14px;
}

.contrast-input-group input[type="color"] {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    border: 3px solid white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    align-self: flex-start;
}

.contrast-input-group input[type="text"] {
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
}

.color-sample {
    width: 100%;
    height: 60px;
    border-radius: 10px;
    border: 2px solid #e0e0e0;
}

.contrast-result {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.contrast-preview {
    padding: 30px;
    border-radius: 15px;
    min-height: 200px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.contrast-preview h4 {
    margin-bottom: 20px;
    font-size: 18px;
}

.contrast-preview p {
    margin-bottom: 15px;
    font-size: 16px;
}

.large-text {
    font-size: 20px;
    font-weight: bold;
}

.normal-text {
    font-size: 16px;
}

.small-text {
    font-size: 12px;
}

.contrast-details {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 15px;
}

.contrast-score h4 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.score-bar {
    height: 20px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 20px;
}

.score-fill {
    height: 100%;
    background: linear-gradient(90deg, #4caf50, #ffc107, #f44336);
    transition: width 0.5s ease;
}

.wcag-compliance h4 {
    margin-bottom: 15px;
    color: #2c3e50;
}

.compliance-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}

.compliance-item {
    padding: 12px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    gap: 5px;
    font-size: 12px;
}

.compliance-item.pass {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.compliance-item.fail {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.rating-name {
    font-weight: 600;
}

.rating-result {
    font-weight: bold;
    font-size: 11px;
}

.rating-info {
    color: rgba(0, 0, 0, 0.6);
    font-size: 10px;
}

/* Export */
.export-container {
    display: grid;
    grid-template-columns: 1fr 2fr 1fr;
    gap: 30px;
}

.export-format-selector {
    background: white;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e0e0e0;
}

.format-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 15px;
}

.format-btn {
    padding: 12px;
    border: 2px solid #e0e0e0;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    text-align: left;
    transition: all 0.3s;
}

.format-btn:hover {
    border-color: #667eea;
    transform: translateX(5px);
}

.format-btn.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #667eea;
}

.export-preview {
    background: white;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e0e0e0;
}

.export-textarea {
    width: 100%;
    padding: 20px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-family: "Courier New", monospace;
    font-size: 14px;
    line-height: 1.5;
    margin: 15px 0;
    resize: vertical;
    background: #f8f9fa;
}

.export-actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    flex: 1;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: transform 0.3s;
}

.action-btn:hover {
    transform: translateY(-3px);
}

.export-options {
    background: white;
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #e0e0e0;
}

.options-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    margin-top: 15px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.option-item:hover {
    border-color: #667eea;
    background: #f8f9fa;
}

.option-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Toast Notification */
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    padding: 15px 25px;
    border-radius: 10px;
    color: white;
    font-weight: 500;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    animation: slideIn 0.3s ease;
}

.toast.success {
    background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
}

.toast.error {
    background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Footer */
.footer-section {
    text-align: center;
    padding: 30px;
    color: white;
    margin-top: 50px;
    opacity: 0.8;
}

.footer-note {
    font-size: 14px;
    margin-top: 10px;
    opacity: 0.7;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .export-container {
        grid-template-columns: 1fr;
    }

    .analogous-container,
    .complementary-grid,
    .triadic-tetradic-grid,
    .square-container,
    .mixer-container,
    .contrast-result {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .color-input-group {
        flex-direction: column;
        align-items: flex-start;
    }

    .color-preview-large {
        width: 120px;
        height: 120px;
    }

    .tabs-navigation {
        flex-wrap: wrap;
    }

    .tab-button {
        flex: 1;
        min-width: 100px;
    }

    .shades-tints-container {
        grid-template-columns: 1fr;
    }

    .color-grid-large {
        grid-template-columns: 1fr;
    }

    .mixer-inputs {
        flex-direction: column;
    }

    .contrast-inputs {
        grid-template-columns: 1fr;
    }

    .preset-grid {
        grid-template-columns: 1fr;
    }

    .compliance-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .header-section h1 {
        font-size: 2rem;
    }

    .subtitle {
        font-size: 1rem;
    }

    .harmony-section h3 {
        font-size: 1.5rem;
    }

    .tabs-navigation {
        flex-direction: column;
    }

    .tab-button {
        width: 100%;
    }
}
</style>
