import React, { useEffect, useState } from "react";
import { useLocation } from "react-router-dom";
import AboutUs from "./AboutUs/AboutUs";
import Advantages from "./Advantages/Advantages";
import Contacts from "./Contacts/Contacts";
import Demo from "./Demo/Demo";
import Heading from "./Heading/Heading";
import "./Home.css";

function Home() {
  const location = useLocation();
  const [unauthorized, setUnauthorized] = useState(location.unauthorized);

  useEffect(() => {
    if (unauthorized) {
      setTimeout(() => {
        setUnauthorized(false);
      }, 2000);
    }
  }, []);

  return (
    <React.Fragment>
      {unauthorized && <h1>Unathorized</h1>}
      <Heading />
      <Advantages />
      <AboutUs />
      <Demo />
      <Contacts />
    </React.Fragment>
  );
}

export default Home;
