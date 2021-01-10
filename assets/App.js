import React from "react";
import { Router, Switch, Route } from "react-router-dom";
import Map from "./components/Map/Map";
import "./App.css";
import Home from "./components/Home/Home";
import NotFound from "./components/NotFound/NotFound";
import MyRoute from "./components/Routes/MyRoute";
import Login from "./components/Login/Login";
import AuthRoute from "./components/Routes/AuthRoute";
import Register from "./components/Register/Register";
import { createBrowserHistory } from "history";
import httpService from "./helpers/api/apiInterceptor";
import Terrains from "./components/Terrains/Terrains";

const history = createBrowserHistory();
httpService.setupInterceptors(history);

function App() {
  return (
    <Router history={history}>
      <Switch>
        <AuthRoute path="/map">
          <Map />
        </AuthRoute>
        <AuthRoute exact path="/terrains">
          <Terrains />
        </AuthRoute>
        <MyRoute path="/users">
          <h2>users</h2>
        </MyRoute>
        <MyRoute exact path="/">
          <Home />
        </MyRoute>
        <AuthRoute exact path="/login">
          <Login />
        </AuthRoute>
        <AuthRoute exact path="/register">
          <Register />
        </AuthRoute>
        <Route path="*">
          <NotFound />
        </Route>
      </Switch>
    </Router>
  );
}

export default App;
