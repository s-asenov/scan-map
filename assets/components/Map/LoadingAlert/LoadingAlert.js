import React, { useContext, useEffect, useState } from "react";
import { Alert, Fade } from "react-bootstrap";
import MapContext from "../../Utils/context/MapContext";
import PropTypes from "prop-types";
import "./LoadingAlert.css";

function LoadingAlert(props) {
  const { map, errorMessage, successMessage } = props;
  const context = useContext(MapContext);

  let alert;

  if (map === undefined) {
    return null;
  }

  if (context.showAlert && map) {
    alert = (
      <div id="loading-alert-wrapper">
        <Alert variant="success" className="px-2 py-3 loading-alert">
          {successMessage}
        </Alert>
      </div>
    );

    setTimeout(() => context.setShowAlert(false), 3000);
  } else if (context.showAlert && map === false) {
    alert = (
      <div id="loading-alert-wrapper">
        <Alert variant="danger" className="px-2 py-3 loading-alert">
          {errorMessage}
        </Alert>
      </div>
    );

    setTimeout(() => context.setShowAlert(false), 3000);
  } else {
    alert = null;
  }

  return (
    context.showAlert && (
      <Fade in={context.showAlert} appear={true}>
        {alert}
      </Fade>
    )
  );
}

LoadingAlert.propTypes = {
  map: PropTypes.bool,
};

LoadingAlert.defaultProps = {
  errorMessage: "Картата не бе успешно запазена!",
  successMessage: "Картата бе успешно запазена!",
};

export default LoadingAlert;
