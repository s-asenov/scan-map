import apiInstance from "./apiInstance";

export default {
  setupInterceptors: (history) => {
    apiInstance.interceptors.response.use(
      (response) => {
        return response;
      },
      (error) => {
        const safePaths = ["/login", "/register", "/", "/demo"];

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
          !safePaths.includes(window.location.pathname)
        ) {
          history.push({
            pathname: "/login",
            unauthorized: true,
          });
        }

        return Promise.reject(error);
      }
    );
  },
};
