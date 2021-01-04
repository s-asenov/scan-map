import React, { useEffect, useState } from "react";
import { Loader } from "@googlemaps/js-api-loader";
import "./Map.css";
import MapButton from "./MapButton";

function Map() {
  const [rect, setRect] = useState(null);

  useEffect(() => {
    const loader = new Loader({
      apiKey: process.env.GOOGLE_MAPS_API_KEY,
      libraries: ["drawing", "geometry", "places", "visualization"],
    });

    loader
      .load()
      .then(() => {
        const init = () => {
          const google = window.google;
          class Rectangle extends google.maps.Rectangle {
            constructor(options) {
              super(options);
            }

            getPos() {
              const sw = this.getBounds().getSouthWest();
              const ne = this.getBounds().getNorthEast();
              const scale = Math.pow(2, this.map.getZoom());
              const proj = this.map.getProjection();
              const bounds = this.map.getBounds();
              const nw = proj.fromLatLngToPoint(
                new google.maps.LatLng(
                  bounds.getNorthEast().lat(),
                  bounds.getSouthWest().lng()
                )
              );

              const point = proj.fromLatLngToPoint(sw);
              const point1 = proj.fromLatLngToPoint(ne);

              const nePoint = new google.maps.Point(
                Math.floor((point1.x - nw.x) * scale),
                Math.floor((point1.y - nw.y) * scale)
              );
              const swPoint = new google.maps.Point(
                Math.floor((point.x - nw.x) * scale),
                Math.floor((point.y - nw.y) * scale)
              );

              return {
                ne: nePoint,
                sw: swPoint,
              };
            }

            getLatLng() {
              const ne = this.getBounds().getNorthEast();
              const sw = this.getBounds().getSouthWest();

              const nw = new google.maps.LatLng(ne.lat(), sw.lng());
              const se = new google.maps.LatLng(sw.lat(), ne.lng());

              return {
                nw: nw.toJSON(),
                ne: ne.toJSON(),
                sw: sw.toJSON(),
                se: se.toJSON(),
              };
            }
          }

          class MyMap extends google.maps.Map {
            constructor(div, opt) {
              super(div, opt);
            }
          }

          return {
            initMap: (div, options) => new MyMap(div, options),
            initRect: (options) => new Rectangle(options),
          };
        };

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
        ];


        const { initMap, initRect } = init();

        const map = initMap(document.getElementById("map"), {
          center: { lat: 42.5, lng: 23 },
          zoom: 9,
          mapTypeId: "satellite",
          maxZoom: 9,
          minZoom: 3,
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
            north: 42.85,
            south: 42.7,
            east: 22.3,
            west: 22.1,
          },
        });
        rectangle.setMap(map);

        rectangle.addListener("dragend", () => {
          if (
            rectangle.getPos().ne.y < 0 ||
            rectangle.getPos().sw.y >
              document.getElementById("map").offsetHeight - 12
          ) {
            map.setCenter(rectangle.getBounds().getCenter());
          }
        });

        setRect(rectangle);
      })
      .catch((e) => {
        console.log(e);
      });
  }, []);
  return (
    <React.Fragment>
      <div id="map" style={{ height: "90vh" }} />
      <MapButton rectangle={rect} />
    </React.Fragment>
  );
}

export default Map;
