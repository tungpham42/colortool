<template>
    <div class="color-tool">
        <!-- Color Picker -->
        <div class="section">
            <h3>Color Picker</h3>
            <input
                type="color"
                v-model="selectedColor"
                @change="getColorInfo"
            />
            <input type="text" v-model="selectedColor" placeholder="#FFFFFF" />
            <div class="color-info" v-if="colorInfo">
                <div
                    class="color-preview"
                    :style="{ backgroundColor: selectedColor }"
                ></div>
                <p>HEX: {{ colorInfo.hex }}</p>
                <p>
                    RGB: rgb({{ colorInfo.rgb.r }}, {{ colorInfo.rgb.g }},
                    {{ colorInfo.rgb.b }})
                </p>
                <p>
                    HSL: hsl({{ colorInfo.hsl.h }}, {{ colorInfo.hsl.s }}%,
                    {{ colorInfo.hsl.l }}%)
                </p>
            </div>
        </div>

        <!-- Color Mixer -->
        <div class="section">
            <h3>Color Mixer</h3>
            <div class="color-inputs">
                <input type="color" v-model="mixColor1" />
                <input type="color" v-model="mixColor2" />
                <input
                    type="range"
                    v-model="mixRatio"
                    min="0"
                    max="1"
                    step="0.1"
                />
            </div>
            <button @click="mixColors">Mix Colors</button>
            <div
                class="mixed-color"
                v-if="mixedColor"
                :style="{ backgroundColor: mixedColor.hex }"
            >
                {{ mixedColor.hex }}
            </div>
        </div>

        <!-- Image Color Extractor -->
        <div class="section">
            <h3>Extract Colors from Image</h3>
            <input type="file" @change="handleImageUpload" accept="image/*" />
            <div v-if="uploadedImage">
                <img :src="uploadedImage" class="preview-image" />
                <div class="extracted-colors">
                    <div
                        v-for="color in extractedColors"
                        :key="color.hex"
                        class="color-swatch"
                        :style="{ backgroundColor: color.hex }"
                        :title="color.hex"
                    >
                        <span>{{ color.percentage }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Color Lookup -->
        <div class="section">
            <h3>Color Lookup</h3>
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search colors..."
            />
            <div class="color-grid">
                <div
                    v-for="color in searchResults"
                    :key="color.id"
                    class="color-item"
                    :style="{ backgroundColor: color.hex }"
                    @click="selectColor(color)"
                >
                    <span>{{ color.name }}</span>
                    <span>{{ color.hex }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            selectedColor: "#3498db",
            colorInfo: null,
            mixColor1: "#ff0000",
            mixColor2: "#0000ff",
            mixRatio: 0.5,
            mixedColor: null,
            uploadedImage: null,
            extractedColors: [],
            searchQuery: "",
            searchResults: [],
        };
    },
    methods: {
        async getColorInfo() {
            const response = await axios.get("/api/colors/picker", {
                params: { color: this.selectedColor },
            });
            this.colorInfo = response.data;
        },
        async mixColors() {
            const response = await axios.post("/api/colors/mix", {
                color1: this.mixColor1,
                color2: this.mixColor2,
                ratio: this.mixRatio,
            });
            this.mixedColor = response.data;
        },
        async handleImageUpload(event) {
            const file = event.target.files[0];
            const formData = new FormData();
            formData.append("image", file);

            const response = await axios.post("/api/colors/extract", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            this.uploadedImage = response.data.image_url;
            this.extractedColors = response.data.colors;
        },
        async searchColors() {
            if (this.searchQuery.length < 2) return;

            const response = await axios.get("/api/colors/lookup", {
                params: { search: this.searchQuery },
            });
            this.searchResults = response.data.data;
        },
        selectColor(color) {
            this.selectedColor = color.hex;
            this.getColorInfo();
        },
    },
    mounted() {
        this.getColorInfo();
    },
    watch: {
        searchQuery() {
            this.searchColors();
        },
    },
};
</script>

<style>
.color-tool {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}
.section {
    margin: 30px 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
}
.color-preview {
    width: 100px;
    height: 100px;
    border-radius: 4px;
    margin: 10px 0;
}
.color-inputs {
    display: flex;
    gap: 10px;
    margin: 10px 0;
}
.extracted-colors {
    display: flex;
    gap: 5px;
    margin-top: 10px;
}
.color-swatch {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}
.color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}
.color-item {
    padding: 10px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    color: white;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
}
.preview-image {
    max-width: 300px;
    margin-top: 10px;
}
</style>
