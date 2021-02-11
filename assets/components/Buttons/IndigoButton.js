import React from "react";
import Button from "react-bootstrap/Button";
import "./IndigoButton.css";

/**
 * The IndigoButton reusable component.
 */
function IndigoButton({ children, ...rest }) {
  return (
    <Button variant={"indigo"} {...rest}>
      {children}
    </Button>
  );
}

export default IndigoButton;
