import { SET_AUTH, REMOVE_AUTH, SET_USER, REMOVE_USER } from "./AuthActions";

function AuthReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case SET_AUTH:
      return {
        ...state,
        isAuth: payload,
      };
    case REMOVE_AUTH:
      return {
        ...state,
        isAuth: false,
        isAdmin: false,
      };
    case SET_USER:
      return {
        ...state,
        isAuth: payload.isAuth,
        isAdmin: payload.isAdmin,
      };
    case REMOVE_USER:
      return {
        ...state,
        isAuth: false,
        isAdmin: false,
      };
    default:
      throw new Error();
  }
}

export default AuthReducer;
