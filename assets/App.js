import React from "react";
import { BrowserRouter as Router, Switch, Route } from "react-router-dom";
import Map from "./components/Map/Map";
import "./App.css";
import Home from "./components/Home/Home";
import NotFound from "./components/NotFound/NotFound";
import MyRoute from "./components/Routes/MyRoute";

function App() {
  return (
    <Router>
      <Switch>
        <MyRoute path="/map">
          <Map />
        </MyRoute>
        <MyRoute path="/users">
          <h2>users</h2>
        </MyRoute>
        <MyRoute exact path="/">
          <Home />
        </MyRoute>
        <Route path="*">
          <NotFound />
        </Route>
      </Switch>
    </Router>
  );
}

export default App;
