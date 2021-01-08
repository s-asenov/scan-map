import { LOADED, LOADING } from "./MapActions";

function MapReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case LOADING:
      return {
        ...state,
        loading: payload,
        loaded: !payload,
      };
    case LOADED:
      return {
        ...state,
        loading: !payload,
        loaded: payload,
      };
    default:
      throw new Error();
  }
}

export default MapReducer;
