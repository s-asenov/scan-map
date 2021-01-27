import { createContext } from "react";

const initialValue = {
  isAuth: null,
  isAdmin: false,
  setAuth: () => {},
  setUser: () => {},
  removeUser: () => {},
};

const AuthContext = createContext(initialValue);

export default AuthContext;
