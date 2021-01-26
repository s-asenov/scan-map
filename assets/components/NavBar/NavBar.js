import React, { useContext, useState } from "react";
import "./NavBar.css";
import Navbar from "react-bootstrap/Navbar";
import Nav from "react-bootstrap/Nav";
import { Button, Image } from "react-bootstrap";
import { NavLink, useHistory } from "react-router-dom";
import { removeAuth } from "../../helpers/auth";
import { HashLink } from "react-router-hash-link";
import AuthContext from "../Utils/context/AuthContext";

function NavBar(props) {
  const history = useHistory();
  const context = useContext(AuthContext);
  const { isAuth, isAdmin } = context;

  const logout = () => {
    removeAuth();

    context.removeUser();
    history.replace("/");
  };

  const AdminLinks = () => {
    if (isAdmin) {
      return (
        <Nav.Link as={NavLink} to="/admin">
          Admin Dashboard
        </Nav.Link>
      );
    } else {
      return null;
    }
  };

  const AuthLinks = () => {
    if (isAuth) {
      return (
        <React.Fragment>
          <Nav.Link as={NavLink} to="/map">
            Карта
          </Nav.Link>
          <Nav.Link as={NavLink} to="/terrains">
            Моите карти
          </Nav.Link>
        </React.Fragment>
      );
    } else {
      return null;
    }
  };

  const AuthButtons = () => {
    if (isAuth === false) {
      return (
        <React.Fragment>
          <Button as={NavLink} to="/login" variant="login">
            Вход
          </Button>
          <Button as={NavLink} to="/register" variant="register">
            Регистрация
          </Button>
        </React.Fragment>
      );
    } else if (isAuth) {
      return (
        <Button onClick={() => logout()} variant="outline-light">
          Изход
        </Button>
      );
    } else {
      return null;
    }
  };

  return (
    <Navbar variant="indigo" bg="indigo" expand="lg">
      <Navbar.Brand as={NavLink} to="/">
        {/* <Image src={logo} rounded height="54" width="54" /> */}
        Terrain Flora Drawer
      </Navbar.Brand>
      <Navbar.Toggle aria-controls="navbar" />
      <Navbar.Collapse id="navbar">
        <Nav>
          <Nav.Link as={NavLink} to="/">
            Начало
          </Nav.Link>
          <Nav.Link as={HashLink} to="/#about-us">
            За нас
          </Nav.Link>
          <Nav.Link as={NavLink} to="/demo">
            Демо
          </Nav.Link>
          <AuthLinks />
          <AdminLinks />
        </Nav>
        <div className="buttons">
          <AuthButtons />
        </div>
      </Navbar.Collapse>
    </Navbar>
  );
}

export default NavBar;
