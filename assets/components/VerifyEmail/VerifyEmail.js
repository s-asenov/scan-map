import axios from "axios";
import React, { useEffect, useState } from "react";
import { Spinner } from "react-bootstrap";
import { Redirect } from "react-router-dom";
import apiInstance from "../../helpers/api/apiInstance";

function VerifyEmail() {
  const [sent, setSent] = useState(false);
  const [verified, setVerified] = useState({
    loaded: false,
    verified: false,
  });

  useEffect(() => {
    axios
      .get("/api/user", {
        headers: {
          Application: process.env.APP_SECRET,
        },
      })
      .then((response) => {
        setVerified({
          loaded: true,
          verified: response.data.user.isVerified,
        });
      });
  }, []);

  const handleClick = () => {
    apiInstance.post("send/verify").then(() => setSent(true));
  };

  if (!verified.loaded) {
    return (
      <div className="flex-1 d-flex align-items-center justify-content-center">
        <Spinner
          animation="border"
          style={{
            height: "4rem",
            width: "4rem",
            borderWidth: "0.5em",
            color: "var(--indigo)",
          }}
        />
      </div>
    );
  }

  if (verified.loaded && verified.verified) {
    return <Redirect to="/" />;
  }

  return (
    <div className="flex-1">
      <p>Вашият имейл все още не е потвърден!</p>
      <p>
        Натиснете{" "}
        <a href="#" onClick={handleClick}>
          тук
        </a>
        , за да изпратим имейл за потвърждение.
      </p>
      {sent && (
        <p className="text-success font-weight-bold">
          Имейлът е изпратен успешно!
        </p>
      )}
    </div>
  );
}

export default VerifyEmail;
