import React, { useReducer } from "react";
import "./AboutUs.css";
import Container from "react-bootstrap/Container";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import Image from "react-bootstrap/Image";
import TechnologyItem from "./TechnologyItem";
import team1 from "../../../images/team1.jpg";
import team2 from "../../../images/team2.jpg";
import TechnologyReducer from "../../Utils/reducers/Home/technologyReducer";
import terrain from "../../../images/unity.jpg";
import technologies from "./technologies";
import IndigoButton from "../../Buttons/IndigoButton";

const initialValue = {
  first: false,
  second: false,
};

function AboutUs() {
  const [state, dispatch] = useReducer(TechnologyReducer, initialValue);

  const handleClick = (e) => {
    dispatch({ type: e.target.name, payload: !state[e.target.name] });
  };

  return (
    <div className="homepage-section">
      <Container>
        <Row>
          <Col md="6" id="about-us">
            <h3>За нас</h3>
            <p>
              Ние сме ученици от XII клас Професионална гимназия по икономика
              гр. Перник със специалност "Икономическа информатика".
            </p>
            <Row className="justify-content-around people">
              <div className="person">
                <Image src={team1} height="180" width="120" />
                <p className="font-weight-bold">Слави Крумов Асенов</p>
                <IndigoButton name="first" onClick={handleClick}>
                  Виж технологии!
                </IndigoButton>
              </div>
              <div className="person">
                <Image src={team2} height="180" width="120" />
                <p className="font-weight-bold">Алекс Руменов Янев</p>
                <IndigoButton name="second" onClick={handleClick}>
                  Виж технологии!
                </IndigoButton>
              </div>
            </Row>
          </Col>
          <Col md="6" className="d-flex align-items-center">
            {!state.first && !state.second && (
              <Image
                className="aboutus-image"
                src={terrain}
                style={{ width: "100%", minHeight: "300px" }}
              />
            )}

            {technologies.map((item, index) => (
              <TechnologyItem
                key={index}
                state={state}
                technologies={item}
                index={index}
              />
            ))}
          </Col>
        </Row>
      </Container>
    </div>
  );
}

export default AboutUs;
