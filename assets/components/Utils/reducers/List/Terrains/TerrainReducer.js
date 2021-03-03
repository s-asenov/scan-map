import { changeShownList } from "../../../../../helpers/listHelper";
import {
  SET_TERRAINS,
  SET_CURRENT,
  SET_MATCHING,
  SET_LOADED,
  DELETE,
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
    case DELETE: {
      //todo refactor
      const dataIndex = state.terrains.findIndex((item) => item.id === payload);

      if (dataIndex === -1) return;
      state.terrains.splice(dataIndex, 1);

      if (state.terrains.length % state.itemsPerPage === 0 && state.page > 1) {
        state.page -= 1;
      }

      let shownFirstIndex = (state.page - 1) * state.itemsPerPage;

      state.currentTerrains = state.terrains.slice(
        shownFirstIndex,
        shownFirstIndex + state.itemsPerPage
      );
      state.matchingTerrains = state.terrains;

      return {
        ...state,
      };
    }
    case "reset":
      return payload;
    default:
      throw new Error();
  }
}

export default TerrainReducer;
