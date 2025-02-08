import axios from "axios";
import "./init.js";
import "https://kit.fontawesome.com/a416056d6c.js";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
