import React from "react";

const initialValue = {
  loaded: false,
  loading: false,
  setLoaded: () => {},
  setLoading: () => {},
  reset: () => {},
  showAlert: false,
  setShowAlert: () => {},
};

const MapContext = React.createContext(initialValue);

export default MapContext;
