import axios from "axios";
import { useFormik } from "formik";
import React from "react";
import { Form, Button } from "react-bootstrap";
import { useHistory } from "react-router-dom";
import FormInvalidFeedback from "../../Utils/FormInvalidFeedback";

const initialValues = {
  from: "",
  subject: "",
  text: "",
};

function ContactForm() {
  const history = useHistory();

  const formik = useFormik({
    initialValues,
    onSubmit: (values, props) => {
      axios
        .post("/email", values)
        .then((response) => {
          //   setAuth(response.data.user.apiToken);
          formik.resetForm(initialValues);
          history.push({
            pathname: "/",
            email: true,
          });
          //   window.location.reload();
        })
        .catch((error) => {
          const response = error.response;

          if (response.status === 400) {
            props.setErrors(response.data);
          }
        });
    },
  });

  const { touched, errors, handleSubmit, values, handleChange } = formik;

  return (
    <Form onSubmit={handleSubmit}>
      <Form.Group controlId="from">
        <Form.Label>Email address</Form.Label>
        <Form.Control
          type="email"
          value={values.from}
          placeholder="Enter email"
          isInvalid={touched.from && errors.from}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.from} />
      </Form.Group>
      <Form.Group controlId="subject">
        <Form.Label>First name</Form.Label>
        <Form.Control
          type="text"
          value={values.subject}
          placeholder="Enter subject"
          isInvalid={touched.subject && errors.subject}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.subject} />
      </Form.Group>
      <Form.Group controlId="text">
        <Form.Label>Description</Form.Label>
        <Form.Control
          as="textarea"
          rows="5"
          value={values.text}
          placeholder="Enter text"
          isInvalid={touched.text && errors.text}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.lastName} />
      </Form.Group>
      <Button variant="primary" type="submit">
        Submit
      </Button>
    </Form>
  );
}

export default ContactForm;
