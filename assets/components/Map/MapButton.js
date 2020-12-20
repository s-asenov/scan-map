import React from "react";
import { Button } from "react-bootstrap";
import calculateDismensions from "./utils/canvas";
import { getFilename, getUniqImagesPos } from "./utils/helpers";

function MapButton({ rectangle }) {
  const handleClick = () => {
    if (
      rectangle.getPos().ne.y < 0 ||
      rectangle.getPos().sw.y > document.getElementById("map").offsetHeight - 12
    ) {
      rectangle.getMap().setCenter(rectangle.getBounds().getCenter());
    }

    const rLatLng = rectangle.getLatLng();

    let filenames = [];

    for (const property in rLatLng) {
      let latLng = rLatLng[property];

      filenames.push(getFilename(latLng));
    }

    const canvas = document.getElementById("canvas");
    const ctx = canvas.getContext("2d");

    const unique = getUniqImagesPos(filenames);

    const topLeft = rLatLng.nw;
    const topRight = rLatLng.ne;
    const botLeft = rLatLng.sw;
    const botRight = rLatLng.se;

    const corners = {
      topLeft,
      topRight,
      botLeft,
      botRight,
    };

    calculateDismensions(unique, ctx, corners);
  };

  return <Button onClick={handleClick}>Render</Button>;
}

export default MapButton;
