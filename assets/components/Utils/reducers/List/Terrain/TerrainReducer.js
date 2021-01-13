import {
  SET_TERRAINS,
  SET_CURRENT,
  SET_MATCHING,
  SET_LOADED,
} from "./TerrainActions";

// function shownDataStartingIndex(page, itemsPerPage) {
//   return (page - 1) * itemsPerPage;
// }

function TerrainReducer(state, action) {
  const { type, payload } = action;

  switch (type) {
    case SET_TERRAINS: {
      const currentIndexPage = payload.page - 1;
      const newIndex = currentIndexPage * state.itemsPerPage;
      const current = payload.terrains.slice(
        newIndex,
        newIndex + state.itemsPerPage
      );

      return {
        ...state,
        terrains: payload.terrains,
        matchingTerrains: payload.terrains,
        currentTerrains: current,
        page: payload.page,
        loaded: true,
      };
    }

    case SET_CURRENT: {
      const currentIndexPage = payload.page - 1;
      const newIndex = currentIndexPage * state.itemsPerPage;
      const current = state.matchingTerrains.slice(
        newIndex,
        newIndex + state.itemsPerPage
      );

      return {
        ...state,
        currentTerrains: current,
        page: payload.page,
      };
    }

    case SET_MATCHING: {
      const matchingTerrains = [];

      for (const terrain in state.terrains) {
        if (
          state.terrains[terrain].name
            .toLowerCase()
            .includes(payload.toLowerCase())
        ) {
          matchingTerrains.push(state.terrains[terrain]);
        }
      }

      const current = matchingTerrains.slice(0, state.itemsPerPage);

      return {
        ...state,
        matchingTerrains: matchingTerrains,
        page: 1,
        currentTerrains: current,
      };
    }
    case SET_LOADED: {
      return {
        ...state,
        loaded: true,
      };
    }
    case "reset":
      return payload;
    default:
      throw new Error();
  }
}

export default TerrainReducer;
