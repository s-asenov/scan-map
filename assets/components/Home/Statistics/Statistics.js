import React, { useEffect, useRef, useState } from "react";
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";
import "./Statistics.css";
import axios from "axios";
import { FaMapMarkedAlt } from "react-icons/fa";
import { GiPlantRoots, GiPieChart } from "react-icons/gi";
import { MdTerrain } from "react-icons/md";
import CountUp, { startAnimation } from "react-countup";
import { Link } from "react-router-dom";
import VisibilitySensor from "react-visibility-sensor";

let initialData = {
  zones: 0,
  plants: 0,
  terrains: 0,
  loaded: false,
};

const StatCountUp = ({ end, ...rest }) => {
  const [isVisible, setVisible] = useState(false);

  const onVisibilityChange = (isVisible) => {
    if (isVisible) {
      setVisible(isVisible);
    }
  };

  return (
    <VisibilitySensor onChange={onVisibilityChange}>
      <CountUp {...rest} end={isVisible ? end : 0} />
    </VisibilitySensor>
  );
};

function Statistics() {
  let _isMounted = true;
  const [data, setData] = useState(initialData);

  const { zones, plants, terrains } = data;

  useEffect(async () => {
    const [
      generatedZones,
      generatedPlants,
      generatedTerrains,
    ] = await Promise.all([
      axios.get(process.env.BASE_URL + "statistics/zones/fetched"),
      axios.get(process.env.BASE_URL + "statistics/plants"),
      axios.get(process.env.BASE_URL + "statistics/terrains"),
    ]);

    if (_isMounted) {
      setData({
        zones: generatedZones.data.meta,
        plants: generatedPlants.data.meta,
        terrains: generatedTerrains.data.meta,
      });
    }

    return () => {
      _isMounted = false;
    };
  }, []);

  return (
    <div className="homepage-section" id="stats">
      <Container>
        <Row className="mx-auto">
          <Col>
            <FaMapMarkedAlt size="2.5em" />
            <h2 className="mt-2">
              <StatCountUp end={zones} duration={1} />
            </h2>
            <p>Разгледани региони!</p>
          </Col>
          <Col>
            <GiPlantRoots size="2.5em" />
            <h2 className="mt-2">
              <StatCountUp end={plants} duration={1} />
            </h2>
            <p>Растения, с които сте се запознали!</p>
          </Col>
          <Col>
            <MdTerrain size="2.5em" />
            <h2 className="mt-2">
              <StatCountUp end={terrains} duration={1} />
            </h2>
            <p>Генерирани терена!</p>
          </Col>
          <Col>
            <GiPieChart size="2.5em" />
            <h2 className="mt-2">Още</h2>
            <p>
              Може да разгледате нашите интерактивни диаграми като натиснете{" "}
              <Link className="text-decoration-none" to="/stats">
                тук!
              </Link>
            </p>
          </Col>
        </Row>
      </Container>
    </div>
  );
}

export default Statistics;
