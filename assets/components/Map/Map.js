import React, { useEffect, useReducer, useState } from "react";
import { Loader } from "@googlemaps/js-api-loader";
import "./Map.css";
import MapContext from "../Utils/context/MapContext";
import MapReducer from "../Utils/reducers/Map/MapReducer";
import { LOADED, LOADING } from "../Utils/reducers/Map/MapActions";
import LoadingModal from "./LoadingModal/LoadingModal";
import { useLocation } from "react-router-dom";
import LoadingAlert from "./LoadingAlert/LoadingAlert";
import init from "./utils/init";
import MapOptions from "./MapOptions/MapOptions";

const initialState = {
  loading: false,
  loaded: false,
  showAlert: false,
};

function Map() {
  const MAX_RECT_SIZE = 30;
  const initialBounds = {
    north: 42.575,
    south: 42.425,
    east: 23.1,
    west: 22.9,
  };

  const location = useLocation();

  const [rect, setRect] = useState(null);
  const [size, setSize] = useState(15);

  const [state, dispatch] = useReducer(MapReducer, initialState);

  const value = {
    loaded: state.loaded,
    setLoaded: (value) => dispatch({ type: LOADED, payload: value }),
    loading: state.loading,
    setLoading: (value) => dispatch({ type: LOADING, payload: value }),
    reset: () => dispatch({ type: "reset", payload: initialState }),
    showAlert: state.showAlert,
    setShowAlert: (value) => dispatch({ type: "alert", payload: value }),
  };

  useEffect(async () => {
    let google;

    const loader = new Loader({
      apiKey: process.env.GOOGLE_MAPS_API_KEY,
      libraries: ["drawing", "geometry", "places", "visualization"],
    });

    if (window.google) {
      google = window.google;
    } else {
      try {
        await loader.load();
      } catch (err) {
        throw err;
      }

      google = window.google;
    }

    const styles = [
      {
        featureType: "road",
        stylers: [{ visibility: "off" }],
      },
    ];

    const { initMap, initRect } = init(window.google);

    const map = initMap(document.getElementById("map"), {
      center: { lat: 42.5, lng: 23 },
      zoom: 9,
      mapTypeId: "hybrid",
      maxZoom: 11,
      minZoom: 4,
      disableDefaultUI: true,
      styles,
    });

    const rectangle = initRect({
      strokeColor: "#FF0000",
      strokeOpacity: 1,
      strokeWeight: 2,
      fillColor: "#ffffff",
      fillOpacity: 0,
      draggable: true,
      bounds: initialBounds,
    });

    rectangle.setMap(map);

    rectangle.addListener("dragend", () => {
      const rectPos = rectangle.getPos();
      const rectBounds = rectangle.getBounds();
      const mapDivWidth = document.getElementById("map").offsetWidth;

      if (
        rectPos.ne.y < 150 ||
        rectPos.ne.x + 175 > mapDivWidth ||
        rectPos.sw.x < 175 ||
        rectPos.sw.y > document.getElementById("map").offsetHeight - 12 - 150
      ) {
        map.setCenter(rectBounds.getCenter());
      }

      setRect(rectangle);
    });

    map.addListener("dragend", () => {
      const rectPos = rectangle.getPos();

      if (
        rectPos.ne.y < 0 ||
        rectPos.ne.x < 0 ||
        rectPos.sw.x < 0 ||
        rectPos.sw.y > document.getElementById("map").offsetHeight - 12
      ) {
        map.setCenter(rectangle.getBounds().getCenter());
      }
    });

    setRect(rectangle);
  }, []);

  const resizeRect = (newSize, rectangle) => {
    if (typeof rectangle !== "undefined") {
      rectangle = rect;
    }

    if (newSize > MAX_RECT_SIZE) {
      newSize = 30;
    } else if (newSize < 0 || isNaN(newSize)) {
      newSize = 1;
    }

    const maxLatDif = 0.3;
    const maxLngDif = 0.4;

    const latTick = (newSize / MAX_RECT_SIZE) * maxLatDif;
    const lngTick = (newSize / MAX_RECT_SIZE) * maxLngDif;

    setSize(newSize);

    const bounds = rect.getBounds().toJSON();

    let west, east, south, north;

    west = bounds.west;
    south = bounds.south;
    east = bounds.west + lngTick;
    north = bounds.south + latTick;

    rectangle.setBounds({
      west,
      east,
      north,
      south,
    });

    setRect(rectangle);
  };

  return (
    <MapContext.Provider value={value}>
      <div className="generate-map">
        <div id="map-content">
          <div id="map" />
          <LoadingModal loaded={state.loaded} loading={state.loading} />
        </div>
        <MapOptions rectangle={rect} resizeRect={resizeRect} size={size} />
      </div>
      <LoadingAlert
        loaded={state.loaded}
        loading={state.loading}
        map={location.map}
      />
    </MapContext.Provider>
  );
}

export default Map;
