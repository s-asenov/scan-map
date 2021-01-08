import React from "react";
import { Col, Container, Row } from "react-bootstrap";
import ContactForm from "./ContactForm";
import "./Contacts.css";

function Contacts() {
  return (
    <div className="homepage-section mt-4 contacts">
      <Container>
        <Row>
          <Col md="6">
            <ContactForm />
          </Col>
          <Col md="6">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2936.6111064523025!2d23.045091315705644!3d42.605993927527024!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14aacadf4ba43b73%3A0x28028560258f88be!2z0J_RgNC-0YTQtdGB0LjQvtC90LDQu9C90LAg0LPQuNC80L3QsNC30LjRjyDQv9C-INC40LrQvtC90L7QvNC40LrQsA!5e0!3m2!1sbg!2sbg!4v1610058290244!5m2!1sbg!2sbg"
              width="100%"
              height="450"
              frameBorder="0"
              style={{ border: 0 }}
              allowFullScreen=""
              aria-hidden="false"
              tabIndex="0"
            ></iframe>
          </Col>
        </Row>
      </Container>
    </div>
  );
}

export default Contacts;
