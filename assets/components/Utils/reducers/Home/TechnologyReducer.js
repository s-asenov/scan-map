import { FIRST, SECOND } from "./TechnogolyActions";

function TechnologyReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case FIRST:
      return {
        ...state,
        first: payload,
        second: false,
      };
    case SECOND:
      return {
        ...state,
        first: false,
        second: payload,
      };
    default:
      throw new Error();
  }
}

export default TechnologyReducer;
