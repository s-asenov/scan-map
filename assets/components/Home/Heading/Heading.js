import { Button } from "react-bootstrap";
import React, { useContext } from "react";
import "./Heading.css";
import { NavLink } from "react-router-dom";
import AuthContext from "../../Utils/context/AuthContext";

function Heading(props) {
  const context = useContext(AuthContext);
  return (
    <div
      id="heading"
      className="d-flex flex-column align-items-center justify-content-center"
    >
      <h1 id="hero-title">Terrain flora drawer</h1>
      <p id="heading-description" className="text-light">
        Проектът представлява интерактивен инструмент за разглеждане и
        запознаване с флората във всяка една точка по света. Целият процес е
        представен чрез генериран 3D терен на избран регион от потребителя.
        Приложението съдържа авторски 3D модели на част от необятната растителна
        Вселена.
      </p>
      {context.isAuth !== null && (
        <Button
          className="my-4"
          as={NavLink}
          to={!context.isAuth ? "/register" : "/map"}
          variant="danger"
        >
          {!context.isAuth ? "Започни сега!" : "Към картата!"}
        </Button>
      )}
    </div>
  );
}

// Heading.propTypes = {
//   isAuth: PropTypes.bool.isRequired,
// };

export default Heading;
