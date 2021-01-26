import apiInstance from "./apiInstance";

export default {
  setupInterceptors: (history) => {
    apiInstance.interceptors.response.use(
      (response) => {
        return response;
      },
      (error) => {
        if (error.response.status === 403) {
          if (error.response.data.email) {
            history.push("/verify");
          } else {
            history.push({
              pathname: "/",
              unauthorized: true,
            });
          }
        } else if (
          error.response.status === 401 &&
          window.location.pathname !== "/login" &&
          window.location.pathname !== "/register"
        ) {
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
