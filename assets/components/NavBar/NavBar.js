import React, { useState } from "react";
import "./NavBar.css";
import Navbar from "react-bootstrap/Navbar";
import Nav from "react-bootstrap/Nav";
import { Button, Image } from "react-bootstrap";
import logo from "../../images/search.png";
import { NavLink, useHistory } from "react-router-dom";
import { getAuth, removeAuth } from "../../helpers/auth";

function NavBar() {
  const history = useHistory();
  const isAuth = getAuth();

  const logout = () => {
    removeAuth();
    history.replace("/");
  };

  return (
    <Navbar variant="indigo" bg="indigo" expand="lg">
      <Navbar.Brand as={NavLink} to="/">
        <Image src={logo} rounded height="54" width="54" />
      </Navbar.Brand>
      <Navbar.Toggle aria-controls="basic-navbar-nav" />
      <Navbar.Collapse id="basic-navbar-nav">
        <Nav>
          <Nav.Link as={NavLink} to="/">
            Home
          </Nav.Link>
          <Nav.Link as={NavLink} to="/#about-us">
            About us
          </Nav.Link>
          {isAuth && (
            <Nav.Link as={NavLink} to="/map">
              Map
            </Nav.Link>
          )}
        </Nav>
        <div className="buttons">
          {!isAuth ? (
            <React.Fragment>
              <Button as={NavLink} to="/login" variant="login">
                Login
              </Button>
              <Button as={NavLink} to="/register" variant="register">
                Register
              </Button>{" "}
            </React.Fragment>
          ) : (
            <Button onClick={() => logout()} variant="outline-light">
              Logout
            </Button>
          )}
        </div>
      </Navbar.Collapse>
    </Navbar>
  );
}

export default NavBar;
