import axios from "axios";
import { BASE_URL } from "../base";

const apiInstance = axios.create({
  baseURL: BASE_URL + "api/",
  headers: {
    Application: process.env.APP_SECRET,
  },
});

export default apiInstance;
