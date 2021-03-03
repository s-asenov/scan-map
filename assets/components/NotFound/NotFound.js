import React from "react";
import Container from "react-bootstrap/Container";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import "./NotFound.css";
import Button from "react-bootstrap/Button";
import { useHistory } from "react-router-dom";
import CountUp from "react-countup";

function NotFound() {
  const history = useHistory();

  return (
    <Container id="error-wrapper">
      <Row>
        <Col md="9" id="countUp" className="mx-auto">
          <div className="number" data-count="404">
            <CountUp end={404} duration={2} />
          </div>
          <div className="text">Page not found</div>
          <div className="text">This may not mean anything.</div>
          <div className="text">
            I'm probably working on something that has blown up.
          </div>
          <Button variant="danger" onClick={() => history.push("/")}>
            HOMEPAGE
          </Button>
        </Col>
      </Row>
    </Container>
  );
}

export default NotFound;
