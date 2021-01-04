import React from "react";
import "./Login.css";
import { useFormik } from "formik";
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";
import axios from "axios";
import { NavLink, useHistory } from "react-router-dom";
import { setAuth } from "../../helpers/auth";

const initialValues = {
  email: "",
  password: "",
};

const validate = (values, props) => {
  const errors = {};

  if (!values.email) {
    errors.email = "Required";
  } else if (!/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i.test(values.email)) {
    errors.email = "Invalid email address";
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

function Login() {
  const history = useHistory();

  const formik = useFormik({
    initialValues,
    validate,
    onSubmit: (values, props) => {
      axios
        .post("/api/login", values)
        .then((response) => {
          setAuth(response.data.user.apiToken);
          history.push("/");
        })
        .catch((error) => {
          const response = error.response;
          if (response.status === 400) {
            props.setErrors({
              password: response.data,
            });
          }
        });
    },
  });

  const { touched, errors, handleSubmit, values, handleChange } = formik;

  return (
    <Form onSubmit={handleSubmit}>
      <Form.Group controlId="email">
        <Form.Label>Email address</Form.Label>
        <Form.Control
          type="email"
          value={values.email}
          placeholder="Enter email"
          isInvalid={touched.email && errors.email}
          onChange={handleChange}
        />
        <Form.Text className="text-muted">
          We'll never share your email with anyone else.
        </Form.Text>
        <FormInvalidFeedback error={errors.email} />
      </Form.Group>
      <Form.Group controlId="password">
        <Form.Label>Password</Form.Label>
        <Form.Control
          type="password"
          value={values.password}
          onChange={handleChange}
          placeholder="Password"
          isInvalid={touched.password && errors.password}
        />
        <FormInvalidFeedback error={errors.password} />
      </Form.Group>
      <Form.Text>
        Don't have a profile? <NavLink>Sign up!</NavLink>
      </Form.Text>
      <Button variant="primary" type="submit">
        Submit
      </Button>
    </Form>
  );
}

export default Login;
