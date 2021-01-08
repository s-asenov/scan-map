import React from "react";
import { Button } from "react-bootstrap";
import { NavLink } from "react-router-dom";
import "./Demo.css";

function Demo() {
  return (
    <div className="homepage-section home-demo text-center">
      <h2 className="font-weight-bold">Разгледайте демото</h2>
      <p>Разгледайте част от нашето приложение като натиснете бутона!</p>
      <Button as={NavLink} to="/demo">
        Демо!
      </Button>
    </div>
  );
}

export default Demo;
