import React from "react";
import MapButton from "../MapButton/MapButton";
import "./MapOptions.css";

function MapOptions({ rectangle, resizeRect, size }) {
  return (
    <div id="options">
      <p className="text-uppercase font-weight-bold mb-2">Размер на терена</p>
      <p className="w-75">
        <small className="text-muted font-italic mb-3 ">
          размерът се измерва в индекс, не в километри
        </small>
      </p>
      <div id="rectangle-size">
        <div
          className="value-button"
          id="decrease"
          title="Намаляване!"
          onClick={() => resizeRect(size - 1, rectangle)}
        >
          -
        </div>
        <input
          type="number"
          id="number"
          onChange={(e) => resizeRect(parseInt(e.target.value), rectangle)}
          value={size}
        />
        <div
          className="value-button"
          id="increase"
          title="Увеличаване!"
          onClick={() => resizeRect(size + 1, rectangle)}
        >
          +
        </div>
      </div>
      <MapButton rectangle={rectangle} />
    </div>
  );
}

export default MapOptions;
