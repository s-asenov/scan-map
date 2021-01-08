import apiInstance from "./api/instance";

function isAuth() {
  //async
  // const request = await apiInstance.post("/user");

  // if (request.status === 200) {
  //   return true;
  // } else {
  //   return false;
  // }
  return !!localStorage.getItem("x-token");
}

function getAuth() {
  return localStorage.getItem("x-token");
}

function setAuth(value) {
  localStorage.setItem("x-token", value);
}

function removeAuth() {
  localStorage.removeItem("x-token");
}

export { isAuth, getAuth, setAuth, removeAuth };
