import React from "react";
import FormControl from "react-bootstrap/FormControl";
import PropTypes from "prop-types";

function FormInvalidFeedback({ error }) {
  if (error) {
    <FormControl.Feedback type="invalid">{error}</FormControl.Feedback>;
  } else {
    return null;
  }
}

FormInvalidFeedback.propTypes = {
  error: PropTypes.string.isRequired,
};

export default FormInvalidFeedback;
