import React from "react";
import { useFormik } from "formik";
import "./Register.css";
import axios from "axios";
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";
import { setAuth } from "../../helpers/auth";
import { useHistory } from "react-router-dom";
import FormInvalidFeedback from "../Utils/FormInvalidFeedback";

const initialValues = {
  email: "",
  firstName: "",
  lastName: "",
  password: "",
};

const validate = (values, props) => {
  const errors = {};

  if (!values.email) {
    errors.email = "Required";
  } else if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(values.email)) {
    errors.email = "Invalid email address";
  }

  if (!values.firstName) {
    errors.firstName = "Required";
  } else if (values.firstName.length < 2) {
    errors.firstName = "Too short";
  } else if (values.firstName.length > 30) {
    errors.firstName = "Too long";
  } else if (!/^[a-z \u0400-\u04FF ,.'-]+$/i.test(values.firstName)) {
    errors.firstName = "Special characters are forbidden!";
  }

  if (!values.lastName) {
    errors.lastName = "Required";
  } else if (values.lastName.length < 2) {
    errors.lastName = "Too short";
  } else if (values.lastName.length > 30) {
    errors.lastName = "Too long";
  } else if (!/^[a-z \u0400-\u04FF ,.'-]+$/i.test(values.lastName)) {
    errors.lastName = "Special characters are forbidden!";
  }

  if (!values.password) {
    errors.password = "Required";
  } else if (values.password.length < 6) {
    errors.password = "Too short";
  } else if (values.password.length > 30) {
    errors.password = "Too long";
  }

  return errors;
};

function Register() {
  const history = useHistory();

  const formik = useFormik({
    initialValues,
    validate,
    onSubmit: (values, props) => {
      axios
        .post("/api/register", values)
        .then((response) => {
          setAuth(response.data.user.apiToken);
          history.push("/");
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
    <Form className="security-form" onSubmit={handleSubmit}>
      <h2 className="font-weight-bold text-center">Регистрация</h2>
      <Form.Group controlId="email">
        <Form.Label>Имейл</Form.Label>
        <Form.Control
          type="email"
          value={values.email}
          placeholder="Въведете имейл"
          isInvalid={touched.email && errors.email}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.email} />
      </Form.Group>
      <Form.Group controlId="firstName">
        <Form.Label>Име</Form.Label>
        <Form.Control
          type="text"
          value={values.firstName}
          placeholder="Въведете име"
          isInvalid={touched.firstName && errors.firstName}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.firstName} />
      </Form.Group>
      <Form.Group controlId="lastName">
        <Form.Label>Фамилия</Form.Label>
        <Form.Control
          type="text"
          value={values.lastName}
          placeholder="Въведете фамилия"
          isInvalid={touched.lastName && errors.lastName}
          onChange={handleChange}
        />
        <FormInvalidFeedback error={errors.lastName} />
      </Form.Group>
      <Form.Group controlId="password">
        <Form.Label>Парола</Form.Label>
        <Form.Control
          type="password"
          value={values.password}
          onChange={handleChange}
          placeholder="Въведете парола"
          isInvalid={touched.password && errors.password}
        />
        <FormInvalidFeedback error={errors.password} />
      </Form.Group>
      <Button variant="primary" type="submit">
        Регистрация
      </Button>
    </Form>
  );
}

export default Register;
