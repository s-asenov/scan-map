import React, { useState } from "react";
import "./NavBar.css";
import Navbar from "react-bootstrap/Navbar";
import Nav from "react-bootstrap/Nav";
import { Button, Image } from "react-bootstrap";
import { NavLink, useHistory } from "react-router-dom";
import { getAuth, removeAuth } from "../../helpers/auth";
import { HashLink } from "react-router-hash-link";

function NavBar() {
  const history = useHistory();
  const isAuth = getAuth();

  const logout = () => {
    removeAuth();
    history.replace("/");
  };

  const NavLinks = () => {
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
          <NavLinks />
        </Nav>
        <div className="buttons">
          {!isAuth ? (
            <React.Fragment>
              <Button as={NavLink} to="/login" variant="login">
                Вход
              </Button>
              <Button as={NavLink} to="/register" variant="register">
                Регистрация
              </Button>
            </React.Fragment>
          ) : (
            <Button onClick={() => logout()} variant="outline-light">
              Изход
            </Button>
          )}
        </div>
      </Navbar.Collapse>
    </Navbar>
  );
}

export default NavBar;
