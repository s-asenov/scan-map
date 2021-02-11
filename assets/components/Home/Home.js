import React from "react";
import AboutUs from "./AboutUs/AboutUs";
import Advantages from "./Advantages/Advantages";
import Contacts from "./Contacts/Contacts";
import Demo from "./Demo/Demo";
import Heading from "./Heading/Heading";
import "./Home.css";

function Home() {
  return (
    <React.Fragment>
      <Heading />
      <Advantages />
      <AboutUs />
      <Demo />
      <Contacts />
    </React.Fragment>
  );
}

export default Home;
