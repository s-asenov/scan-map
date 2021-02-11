import { SET_DISABLED, SET_SHOW, SET_KEYS, SET_ADD } from "./TerrainKeyActions";

function TerrainKeyReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case SET_KEYS: {
      return {
        ...state,
        keys: payload.keys,
        loaded: payload.loaded || false,
      };
    }
    case SET_DISABLED: {
      return {
        ...state,
        disabled: payload,
      };
    }
    case SET_SHOW: {
      return {
        ...state,
        show: payload,
      };
    }
    case SET_ADD: {
      return {
        ...state,
        disabled: payload.disabled,
        show: payload.show,
        loaded: payload.loaded || false,
      };
    }
    default:
      throw new Error();
  }
}

export default TerrainKeyReducer;
