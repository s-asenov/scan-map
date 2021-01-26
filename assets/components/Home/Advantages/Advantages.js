import React from "react";
import Container from "react-bootstrap/Container";
import Col from "react-bootstrap/Col";
import "./Advantages.css";
import Row from "react-bootstrap/Row";
import { GiSpellBook, GiClick, GiGears } from "react-icons/gi";

const info = [
  {
    heading: "Лесно",
    image: <GiClick size="2.5em" />,
    info:
      "Приложението е направено удобно за потребителят, като само с 1 клик на мишката се генерира уникален код за местността, която е посочил на Google Maps картата.",
  },
  {
    heading: "Разходка из познания",
    image: <GiSpellBook size="2.5em" />,
    info:
      "Освен разходка из региона, потребителят има възможността да се запознае и с неговата флора. Така се стремим да обогатим културата на всеки заинтригуван!",
  },
  {
    heading: "Напълно автоматично",
    image: <GiGears size="2.5em" />,
    info:
      "За разлика от други методи за генериране на терен от карта, не са нужни познания в различни технологии, като така нашият инструмент е лесно използваем за всички потребители.",
  },
];

function Advantage(props) {
  const { item } = props;

  return (
    <Col lg="4" className="d-flex">
      <div className="py-4 px-5 advantage">
        {item.image}
        <h3>{item.heading}</h3>
        <p className="text-muted">{item.info}</p>
      </div>
    </Col>
  );
}

function Advantages() {
  return (
    <React.Fragment>
      <div className="homepage-section advantage-wrapper">
        <Container className="heading">
          <Row className="mx-auto">
            {info.map((item, index) => (
              <Advantage key={index} item={item} />
            ))}
          </Row>
        </Container>
      </div>
      {/* <Image className="svg" src={svg} width="100%" height="60px" /> */}
    </React.Fragment>
  );
}

export default Advantages;
