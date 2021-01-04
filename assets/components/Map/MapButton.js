import axios from "axios";
import React from "react";
import { Button } from "react-bootstrap";
import calculateDismensions from "./utils/canvas";
import { getFilename, getUniqImagesPos } from "./utils/helpers";

function MapButton({ rectangle }) {
  const handleRequest = async (rLatLng, unique, ctx) => {
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

    const response = await calculateDismensions(unique, ctx, corners);

    return response;
  };

  const handleClick = () => {
    if (
      rectangle.getPos().ne.y < 0 ||
      rectangle.getPos().sw.y > document.getElementById("map").offsetHeight - 12
    ) {
      rectangle.getMap().setCenter(rectangle.getBounds().getCenter());
    }

    const rLatLng = rectangle.getLatLng();
    const center = rectangle.getBounds().getCenter();

    let filenames = [];

    for (const property in rLatLng) {
      let latLng = rLatLng[property];

      filenames.push(getFilename(latLng));
    }

    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    const unique = getUniqImagesPos(filenames);

    handleRequest(rLatLng, unique, ctx).then((result) => {
      const { base64, unique } = result;

      if (unique.count === 1 && unique.images[0].includes("default.jpg")) {
        alert("Please refrain to mainland.");

        return;
      }

      axios
        .post(
          "/api/map",
          {
            lat: center.lat(),
            lng: center.lng(),
            jpg: base64,
          },
          {
            headers: {
              "AUTH-TOKEN": localStorage.getItem("remember"),
            },
          }
        )
        .then((response) => {
          console.log(response.data);
        });
    });
  };

  return <Button onClick={handleClick}>Render</Button>;
}

export default MapButton;
