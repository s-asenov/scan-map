import axios from "axios";

const apiInstance = axios.create({
  baseURL: process.env.BASE_URL + "api/",
  headers: {
    Application: process.env.APP_SECRET,
  },
});

export default apiInstance;
