import apiInstance from "./apiInstance";

export default {
  setupInterceptors: (history) => {
    apiInstance.interceptors.response.use(
      (response) => {
        return response;
      },
      (error) => {
        const safePaths = ["/login", "/register", "/", "/demo", "/stats"];

        if (error.response) {
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
        }

        return Promise.reject(error);
      }
    );

    apiInstance.interceptors.request.use(
      (config) => {
        //todo
        // if (args.token) {
        //   config.headers.Authorization = args.token;
        // }

        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );
  },
};
