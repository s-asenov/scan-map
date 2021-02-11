import React from "react";
import { NavLink } from "react-router-dom";
import IndigoButton from "../../Buttons/IndigoButton";
import "./Demo.css";

function Demo() {
  return (
    <div className="homepage-section home-demo text-center">
      <h2 className="font-weight-bold">Разгледайте демото</h2>
      <p>Разгледайте част от нашето приложение като натиснете бутона!</p>
      <IndigoButton as={NavLink} to="/demo">
        Демо!
      </IndigoButton>
    </div>
  );
}

export default Demo;
