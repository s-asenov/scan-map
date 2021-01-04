function isAuth() {
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
