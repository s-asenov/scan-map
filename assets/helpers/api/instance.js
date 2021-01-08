import axios from "axios";
import { BASE_URL } from "../base";
import { getAuth } from "../auth";

const apiInstance = axios.create({
  baseURL: BASE_URL + "api/",
  headers: { "AUTH-TOKEN": getAuth() },
});

// apiInstance.interceptors.response.use(
//   function (response) {
//     return response;
//   },
//   function (error) {
//     if (error.response.status === 401) {
//       localStorage.clear();
//       window.location.replace("/");
//     }

//     return Promise.reject(error);
//   }
// );

export default apiInstance;
