import React, { useEffect, useReducer, useState } from "react";
import { Loader } from "@googlemaps/js-api-loader";
import "./Map.css";
import MapButton from "./MapButton/MapButton";
import MapContext from "../Utils/context/MapContext";
import MapReducer from "../Utils/reducers/Map/MapReducer";
import { LOADED, LOADING } from "../Utils/reducers/Map/MapActions";
import LoadingModal from "./LoadingModal/LoadingModal";
import { useLocation } from "react-router-dom";
import LoadingAlert from "./LoadingAlert/LoadingAlert";
import init from "./utils/init";

const initialState = {
  loading: false,
  loaded: false,
  showAlert: false,
};

function Map() {
  const location = useLocation();

  const [rect, setRect] = useState(null);
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

    // let google = window.google;
    // if (!google) {
    //   try {
    //     await loader.load();
    //   } catch (err) {
    //     throw err;
    //   }

    //   google = window.google;
    // }

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
      // {
      //   featureType: "all",
      //   elementType: "labels",
      //   stylers: [{ visibility: "off" }],
      // },
      // {
      //   featureType: "road",
      //   elementType: "geometry",
      //   stylers: [{ visibility: "off" }],
      // },
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
      bounds: {
        north: 42.55,
        south: 42.4,
        east: 22.1,
        west: 21.9,
      },
    });

    rectangle.setMap(map);

    rectangle.addListener("dragend", () => {
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

  return (
    <MapContext.Provider value={value}>
      <div id="map-content">
        <div id="map" />
        <MapButton rectangle={rect} />
        {/* <p>Картата генерира нужните файлове</p> */}
        <LoadingModal loaded={state.loaded} loading={state.loading} />
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
