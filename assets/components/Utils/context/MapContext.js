import React from "react";

const initialValue = {
  loaded: false,
  loading: false,
  setLoaded: () => {},
  setLoading: () => {},
};

const MapContext = React.createContext(initialValue);

export default MapContext;
