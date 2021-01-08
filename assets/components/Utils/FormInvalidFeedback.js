import React from "react";
import FormControl from "react-bootstrap/FormControl";
import PropTypes from "prop-types";

function FormInvalidFeedback({ error }) {
  if (error) {
    return <FormControl.Feedback type="invalid">{error}</FormControl.Feedback>;
  } else {
    return null;
  }
}

FormInvalidFeedback.propTypes = {
  error: PropTypes.string,
};

export default FormInvalidFeedback;
