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

const validate = (values) => {
  Object.keys(values).map((k) => (values[k] = values[k].trim()));

  const errors = {};

  if (!values.from) {
    errors.from = "Required";
  } else if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(values.from)) {
    errors.from = "Invalid email address";
  }

  if (!values.firstName) {
    errors.subject = "Required";
  }

  if (!values.text) {
    errors.text = "Required";
  }
  if (values.text.length < 2) {
    errors.text = "Too short";
  }

  return errors;
};

function ContactForm() {
  const history = useHistory();

  const formik = useFormik({
    initialValues,
    validate,
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
    <Form className="form" onSubmit={handleSubmit}>
      <Form.Group controlId="from">
        <Form.Label>Имейл адрес</Form.Label>
        <Form.Control
          type="email"
          value={values.from}
          placeholder="Имейл"
          isInvalid={touched.from && errors.from}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.from} />
      </Form.Group>
      <Form.Group controlId="subject">
        <Form.Label>Тема</Form.Label>
        <Form.Control
          type="text"
          value={values.subject}
          placeholder="Тема"
          isInvalid={touched.subject && errors.subject}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.subject} />
      </Form.Group>
      <Form.Group controlId="text">
        <Form.Label>Въведете текст</Form.Label>
        <Form.Control
          as="textarea"
          rows="7"
          value={values.text}
          placeholder="Текст"
          isInvalid={touched.text && errors.text}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.text} />
      </Form.Group>
      <Button variant="primary" type="submit">
        Изпращане
      </Button>
    </Form>
  );
}

export default ContactForm;
