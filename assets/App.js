import React from "react";
import { BrowserRouter as Router, Switch, Route, Link } from "react-router-dom";
import Map from "./components/Map/Map";
import NavBar from "./components/NavBar/NavBar";
import "./App.css";
import Home from "./components/Home/Home";

function App() {
  return (
    <Router>
      <NavBar />
      <Switch>
        <Route path="/map">
          <Map />
        </Route>
        <Route path="/users">
          <h2>users</h2>
        </Route>
        <Route exact path="/">
          <Home />
        </Route>
        <Route path="*">
          <h2>404 no</h2>
        </Route>
      </Switch>
    </Router>
  );
}

export default App;
