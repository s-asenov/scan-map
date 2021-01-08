import apiInstance from "./instance";

export default {
  setupInterceptors: (history) => {
    apiInstance.interceptors.response.use(
      (response) => {
        return response;
      },
      (error) => {
        if (error.response.status === 401) {
          localStorage.clear();
          history.push({
            pathname: "/",
            redirected: true,
          });
        }

        return Promise.reject(error);
      }
    );
  },
};
