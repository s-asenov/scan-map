import React from "react";
import ReactDOM from "react-dom";
// import "bootstrap/dist/css/bootstrap.min.css";
// import "./index.css";
import "./index.scss";
import App from "./app";

// start the Stimulus application

ReactDOM.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
  document.getElementById("root")
);
