import "./bootstrap";
import { createApp } from "vue";

import App from "./App.vue";
import { OhVueIcon, addIcons } from "oh-vue-icons";
import {
    BiXLg,
    CoPlus,
    MdArrowdropdownRound,
    MdArrowdropupRound,
    MdCheckboxoutlineblank,
    MdCheckboxRound,
} from "oh-vue-icons/icons";

addIcons(
    BiXLg,
    CoPlus,
    MdArrowdropdownRound,
    MdArrowdropupRound,
    MdCheckboxoutlineblank,
    MdCheckboxRound
);

createApp(App).component("v-icon", OhVueIcon).mount("#app");
