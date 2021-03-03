import React from "react";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import Image from "react-bootstrap/Image";
import PropTypes from "prop-types";

const TechnologyItem = ({ state, technologies, index }) => {
  const cn = index === 0 ? "first" : "second";

  return (
    <Row className={"technologies " + cn + (state[cn] ? " active" : "")}>
      {technologies.map((item, index) => (
        <Col key={index} className="technology py-4 px-2">
          <Image
            className="technology-image"
            src={item.image}
            title={item.name}
          />
        </Col>
      ))}
    </Row>
  );
};

TechnologyItem.proptypes = {
  state: PropTypes.object.isRequired,
  technologies: PropTypes.array.isRequired,
  index: PropTypes.number.isRequired,
};

export default TechnologyItem;
