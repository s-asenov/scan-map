import React, { useContext } from "react";
import "./MapButton.css";
import { Button } from "react-bootstrap";
import apiInstance from "../../../helpers/api/apiInstance";
import MapContext from "../../Utils/context/MapContext";
import calculateDismensions from "../utils/canvas";
import { getFilename, getUniqImagesPos } from "../utils/helpers";
import html2canvas from "html2canvas";

function MapButton({ rectangle }) {
  const context = useContext(MapContext);

  const handleRequest = async (rLatLng, unique, ctx) => {
    context.setLoading(true);

    const { nw: topLeft, ne: topRight, sw: botLeft, se: botRight } = rLatLng;

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
    let terrainName;

    terrainName = prompt("Моля въведете име на местността:");

    if (terrainName === null || terrainName === "") {
      terrainName = prompt("Името не трябва да е празно!");
      return;
    }

    if (terrainName.length > 255) {
      terrainName = prompt("Името не трябва да е по-дълго от 255 символа!");
      return;
    }

    if (!/^[a-z \u0400-\u04FF 0-9 ,.'-]+$/i.test(terrainName)) {
      terrainName = prompt("Името не приема специални символи!");
      return;
    }

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

    const ne = rectangle.getPos().ne;
    const sw = rectangle.getPos().sw;

    html2canvas(document.getElementById("map"), {
      useCORS: true,
      x: sw.x - 50,
      y: ne.y + 40,
      width: ne.x - sw.x + 150,
      height: sw.y - ne.y + 100,
      scrollY: window.screenY,
    }).then((gmCanvas) => {
      const dataUrl = gmCanvas.toDataURL("image/jpeg");
      const gmImage = dataUrl.split("base64,")[1];

      handleRequest(rLatLng, unique, ctx).then((result) => {
        const { base64, unique } = result;

        if (unique.count === 1 && unique.images[0].includes("default.jpg")) {
          alert("Please refrain to mainland.");

          return;
        }

        apiInstance
          .post("map", {
            lat: center.lat(),
            lng: center.lng(),
            images: {
              elevation: base64,
              gmImage: gmImage,
            },
            name: terrainName,
          })
          .then((response) => {
            context.setLoaded(true);
            console.log(response.data);
          });
      });
    });
  };

  return (
    <Button onClick={handleClick} variant="primary map-btn">
      Генерирай
    </Button>
  );
}

export default MapButton;
