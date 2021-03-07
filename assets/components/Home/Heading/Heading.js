import React, { useContext } from "react";
import "./Heading.css";
import { NavLink } from "react-router-dom";
import AuthContext from "../../Utils/context/AuthContext";
import IndigoButton from "../../Buttons/IndigoButton";

function Heading() {
  const context = useContext(AuthContext);
  return (
    <div
      id="heading"
      className="d-flex flex-column align-items-center justify-content-center"
    >
      <h1 id="hero-title">Terrain Flora Drawer</h1>
      <p id="heading-description" className="text-light">
        Проектът представлява интерактивен инструмент за разглеждане и
        запознаване с флората във всяка една точка по света. Целият процес е
        представен чрез генериран 3D терен на избран регион от потребителя.
        Приложението съдържа авторски 3D модели на част от необятната растителна
        Вселена.
      </p>
      <div>
        <a href={process.env.BASE_URL + "TerrainFloraDrawer.rar"} download>
          <IndigoButton className="mr-3">Свали приложението!</IndigoButton>
        </a>
        {context.isAuth !== null && (
          <IndigoButton
            className="my-4"
            as={NavLink}
            to={!context.isAuth ? "/register" : "/map"}
            variant="success"
            style={{
              fontWeight: "500",
              borderWidth: "2px",
            }}
          >
            {!context.isAuth ? "Започни сега!" : "Към картата!"}
          </IndigoButton>
        )}
      </div>
    </div>
  );
}

export default Heading;
