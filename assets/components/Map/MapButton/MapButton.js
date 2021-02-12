import React, { useContext } from "react";
import "./MapButton.css";
import apiInstance from "../../../helpers/api/apiInstance";
import MapContext from "../../Utils/context/MapContext";
import calculateDismensions from "../utils/canvas";
import { getFilename, getUniqImagesPos } from "../utils/helpers";
import html2canvas from "html2canvas";
import { myValidate } from "../../Utils/validation/messages";
import IndigoButton from "../../Buttons/IndigoButton";

function MapButton({ rectangle }) {
  const context = useContext(MapContext);

  const handleRequest = async (rLatLng, unique, ctx) => {
    //Change the loading property of the context state
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

    const validateName = myValidate(terrainName);

    const error = validateName.REQUIRED() || validateName.MAX(200);

    if (error) {
      terrainName = prompt(error);
      return;
    }

    //Center the rectangle on the map so we can screenshot the map
    rectangle.getMap().setCenter(rectangle.getBounds().getCenter());
    document.getElementById("map").scrollIntoView();

    const rLatLng = rectangle.getLatLng();
    const center = rectangle.getBounds().getCenter();

    /**
     * So we loop through all rectangle angles and return the corresponding filename.
     * @see getFilename()
     */
    let filenames = [];

    for (const property in rLatLng) {
      let latLng = rLatLng[property];

      filenames.push(getFilename(latLng));
    }

    /**
     * Create a canvas html element, which will be used for the displaying of the image(s).
     */
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");

    const unique = getUniqImagesPos(filenames);

    /**
     * Get the positions in pixels of the North-East(Top-Right) and South-West(Bottom-Left) corners of the google maps rectangle,
     * which will be used for creating the second image used in the terrains list.
     */
    const { ne, sw } = rectangle.getPos();

    html2canvas(document.getElementById("map"), {
      useCORS: true,
      x: sw.x - 25,
      y: ne.y,
      width: 350,
      height: 300,
      scrollY: document.getElementById("navbar").offsetHeight,
      logging: false,
    }).then((gmCanvas) => {
      const dataUrl = gmCanvas.toDataURL("image/jpeg");
      const gmImage = dataUrl.split("base64,")[1];

      handleRequest(rLatLng, unique, ctx).then((result) => {
        const { base64, unique } = result;

        if (unique.count === 1 && unique.images[0].includes("default.jpg")) {
          alert("Моля придържете се към континенталната част.");

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
            // console.log(response.data);
          });
      });
    });
  };

  return (
    <IndigoButton onClick={handleClick} block style={{ margin: "1rem 0" }}>
      Генерирай
    </IndigoButton>
  );
}

export default MapButton;
