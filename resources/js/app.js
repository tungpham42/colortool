import { createApp } from "vue";
import AdvancedColorTool from "./components/AdvancedColorTool.vue";

const app = createApp({});
app.component("advanced-color-tool", AdvancedColorTool);
app.mount("#app");
console.log("App.js đã chạy nè mẹ ơi!");
