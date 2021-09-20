import React, { useEffect, useReducer } from "react";
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
import Terrains from "./components/Terrains/Terrains";
import Demo from "./components/Demo/Demo";
import VerifyEmail from "./components/VerifyEmail/VerifyEmail";
import httpService from "./helpers/api/apiInterceptor";
import { getAuth } from "./helpers/auth";
import {
  REMOVE_USER,
  SET_AUTH,
  SET_USER,
} from "./components/Utils/reducers/Auth/AuthActions";
import AuthReducer from "./components/Utils/reducers/Auth/AuthReducer";
import AuthContext from "./components/Utils/context/AuthContext";
import AdminRoute from "./components/Routes/AdminRoute";
import Admin from "./components/Admin/Admin";
import Statistics from "./components/Statistics/Statistics";

const history = createBrowserHistory();
httpService.setupInterceptors(history); //initial setting up of the interceptor, required when reloading

const initialState = {
  isAuth: null,
  isAdmin: null,
  setAuth: () => {},
  setUser: () => {},
  removeUser: () => {},
};

function App() {
  const [state, dispatch] = useReducer(AuthReducer, initialState);

  const value = {
    isAuth: state.isAuth,
    isAdmin: state.isAdmin,
    setAuth: (value) => dispatch({ type: SET_AUTH, payload: value }),
    setUser: (auth, admin) =>
      dispatch({
        type: SET_USER,
        payload: {
          isAuth: auth,
          isAdmin: admin,
        },
      }),
    removeUser: () => dispatch({ type: REMOVE_USER }),
  };

  useEffect(() => {
    getAuth().then((result) => {
      value.setUser(result.auth, result.admin);
    });
  }, []);

  return (
    <AuthContext.Provider value={value}>
      {/*  <div className="z-2 position-absolute alert alert-danger"*/}
      {/*     style={{*/}
      {/*       zIndex: "10",*/}
      {/*       left: "50%",*/}
      {/*       /* position: absolute; */}
      {/*       transform: "translateX(-50%)",*/}
      {/*       bottom: "10px",*/}
      {/*       textAlign: "center"*/}
      {/*       }}>*/}
      {/*  За съжаление услугата, която се използва за вземането на информация на растенията е спряна (05.05.2021г.). Работи се по обработването на базата от данни на тази услуга, за да продължи работата на Terrain Flora Drawer.*/}
      {/*</div>*/}
      <Router history={history}>
        <Switch>
          <AuthRoute path="/map">
            <Map />
          </AuthRoute>
          <AuthRoute exact path="/terrains">
            <Terrains />
          </AuthRoute>
          <MyRoute exact path="/demo">
            <Demo />
          </MyRoute>
          <MyRoute exact path="/">
            <Home />
          </MyRoute>
          <AuthRoute exact path="/verify">
            <VerifyEmail />
          </AuthRoute>
          <AuthRoute exact path="/login">
            <Login />
          </AuthRoute>
          <AuthRoute exact path="/register">
            <Register />
          </AuthRoute>
          <AdminRoute path="/admin">
            <Admin />
          </AdminRoute>
          <MyRoute exact path="/stats">
            <Statistics />
          </MyRoute>
          <Route path="*">
            <NotFound />
          </Route>
        </Switch>
      </Router>
    </AuthContext.Provider>
  );
}

export default App;
