import { deepCloneObject } from "../../../../../helpers/helper";
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
      let newState = deepCloneObject(state);

      const dataIndex = state.terrains.findIndex((item) => item.id === payload);

      if (dataIndex === -1) return;
      newState.terrains.splice(dataIndex, 1);

      if (
        newState.terrains.length % newState.itemsPerPage === 0 &&
        newState.page > 1
      ) {
        newState.page -= 1;

        var urlString = window.location.href;
        var url = new URL(urlString);
        url.searchParams.set("page", newState.page);

        const newurl = url.toString();
        window.history.pushState({ path: newurl }, "", newurl);
      }
      let shownFirstIndex = (newState.page - 1) * newState.itemsPerPage;

      newState.currentTerrains = newState.terrains.slice(
        shownFirstIndex,
        shownFirstIndex + newState.itemsPerPage
      );
      newState.matchingTerrains = newState.terrains;

      return {
        ...newState,
      };
    }
    case "reset":
      return payload;
    default:
      throw new Error();
  }
}

export default TerrainReducer;
