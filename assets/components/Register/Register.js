import React, { useContext } from "react";
import { useFormik } from "formik";
import "./Register.css";
import axios from "axios";
import Form from "react-bootstrap/Form";
import { NavLink, useHistory } from "react-router-dom";
import FormInvalidFeedback from "../Utils/FormInvalidFeedback";
import httpService from "../../helpers/api/apiInterceptor";
import AuthContext from "../Utils/context/AuthContext";
import { myValidate } from "../Utils/validation/messages";
import IndigoButton from "app/assets/components/Buttons/IndigoButton";

const initialValues = {
  email: "",
  firstName: "",
  lastName: "",
  password: "",
};

const validate = (values, props) => {
  Object.keys(values).map((k) => (values[k] = values[k].trim()));

  const errors = {};

  const validateEmail = myValidate(values.email);
  const validateFirstName = myValidate(values.firstName);
  const validateLastName = myValidate(values.lastName);
  const validatePass = myValidate(values.password);

  const emailErr = validateEmail.REQUIRED() || validateEmail.EMAIL();
  const firstNameErr =
    validateFirstName.REQUIRED() ||
    validateFirstName.MIN(2) ||
    validateFirstName.MAX(30) ||
    validateFirstName.SPECIAL_CHARACTERS();
  const lastNameErr =
    validateLastName.REQUIRED() ||
    validateLastName.MIN(2) ||
    validateLastName.MAX(30) ||
    validateLastName.SPECIAL_CHARACTERS();
  const passErr =
    validatePass.REQUIRED() ||
    validateLastName.MIN(6) ||
    validateLastName.MAX(30);

  if (emailErr) {
    errors.email = emailErr;
  }

  if (firstNameErr) {
    errors.firstName = firstNameErr;
  }

  if (lastNameErr) {
    errors.lastName = lastNameErr;
  }

  if (passErr) {
    errors.password = passErr;
  }

  return errors;
};

function Register() {
  const history = useHistory();
  const context = useContext(AuthContext);

  const formik = useFormik({
    initialValues,
    validate,
    onSubmit: (values, props) => {
      axios
        .post("/api/register", values)
        .then((response) => {
          const user = response.data.user;
          let admin = user.roles.includes("ROLE_SUPER_ADMIN");
          context.setUser(true, admin);

          httpService.setupInterceptors(history);
          history.push("/verify");
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
      <h2 className="font-weight-bold text-center overflow-hidden">
        Регистрация
      </h2>
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
        <p>
          Вече сте регистрирани? <NavLink to="/login">Влезте!</NavLink>
        </p>
      </Form.Group>
      <IndigoButton type="submit" block>
        Регистрация
      </IndigoButton>
    </Form>
  );
}

export default Register;
