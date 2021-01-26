import React from "react";
import { Col, Container, Row } from "react-bootstrap";
import { NavLink } from "react-router-dom";
import { HashLink } from "react-router-hash-link";
import { FaGlobe } from "react-icons/fa";
import { MdContactMail } from "react-icons/md";
import "./Footer.css";

function Footer() {
  return (
    <div id="footer">
      <Container>
        <Row className="py-5">
          <Col md="4" className="links">
            <div className="mt-3">
              <FaGlobe size="2rem" color="#4b0082" />
              <p className="d-inline ml-2">гр. Перник, ул. Г. Мамарчев, 2</p>
            </div>
            <div className="mt-3">
              <MdContactMail size="2rem" color="#aaaaaa" />
              <a href="mailto:support@flora.noit.eu" className="ml-2">
                mailto:support@flora.noit.eu
              </a>
            </div>
          </Col>
          <Col md="4" className="links">
            <ul>
              <h4 className="footer-title">Източници</h4>
              <li>
                <a href="https://lpdaac.usgs.gov/" target="_blank">
                  LP DAAC
                </a>
              </li>
              <li>
                <a
                  href="https://bg.wikipedia.org/wiki/%D0%9D%D0%B0%D1%87%D0%B0%D0%BB%D0%BD%D0%B0_%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B8%D1%86%D0%B0"
                  target="_blank"
                >
                  Уикипедия
                </a>
              </li>
              <li>
                <a href="https://www.google.com/maps" target="_blank">
                  Google Maps
                </a>
              </li>
              <li>
                <a href="https://pgi-pernik.bg-schools.com/" target="_blank">
                  ПГИ гр. Перник
                </a>
              </li>
            </ul>
          </Col>
          <Col md="4" className="links">
            <ul>
              <h4 className="footer-title">Бързи връзки</h4>
              <li>
                <HashLink to="/#about-us">За нас</HashLink>
              </li>
              <li>
                <NavLink to="/demo">Демо</NavLink>
              </li>
              <li>
                <NavLink to="/login">Вход</NavLink>
              </li>
              <li>
                <NavLink to="/register">Регистрация</NavLink>
              </li>
              <li>
                <NavLink to="/map">Използвай карта</NavLink>
              </li>
            </ul>
          </Col>
        </Row>
      </Container>
      <hr style={{ backgroundColor: "#8c8c8c" }} />
      <div
        className="text-center"
        style={{
          color: "#aaaaaa",
          padding: "1rem 0",
        }}
      >
        <p>
          НОИТ 2021 Разпределени уеб приложения | Алекс Янев и Слави Асенов |
          ПГИ - гр. Перник
        </p>
      </div>
    </div>
  );
}

export default Footer;
