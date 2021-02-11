import React, { useContext, useState, useEffect } from "react";
import "./Login.css";
import { useFormik } from "formik";
import Form from "react-bootstrap/Form";
import axios from "axios";
import { NavLink, useHistory, useLocation } from "react-router-dom";
import FormInvalidFeedback from "app/assets/components/Utils/FormInvalidFeedback";
import httpService from "app/assets/helpers/api/apiInterceptor";
import AuthContext from "app/assets/components/Utils/context/AuthContext";
import { myValidate } from "app/assets/components/Utils/validation/messages";
import IndigoButton from "app/assets/components/Buttons/IndigoButton";

const initialValues = {
  email: "",
  password: "",
};

const validate = (values, props) => {
  Object.keys(values).map((k) => (values[k] = values[k].trim()));

  const errors = {};

  const validateEmail = myValidate(values.email);
  const validatePass = myValidate(values.password);

  const emailErr = validateEmail.REQUIRED() || validateEmail.EMAIL();
  const passErr =
    validatePass.REQUIRED() || validatePass.MIN(6) || validatePass.MAX(30);

  if (emailErr) {
    errors.email = emailErr;
  }

  if (passErr) {
    errors.password = passErr;
  }

  return errors;
};

function Login() {
  const history = useHistory();
  const location = useLocation();

  const context = useContext(AuthContext);
  const [unauthorized, setUnathorized] = useState(
    localStorage.getItem("unauth") !== null
  );

  useEffect(() => {
    if (location.unauthorized) {
      localStorage.setItem("unauth", ""); //add to localstorage to access it after the component refresh
      context.removeUser();
    }

    const timeout = setTimeout(() => {
      localStorage.removeItem("unauth");
      setUnathorized(false);
    }, 3000);

    return () => clearTimeout(timeout);
  }, [location.unauthorized]);

  const formik = useFormik({
    initialValues,
    validate,
    onSubmit: (values, props) => {
      axios
        .post("/api/login", values)
        .then((response) => {
          const user = response.data.user;
          let admin = user.roles.includes("ROLE_SUPER_ADMIN");

          context.setUser(true, admin);
          httpService.setupInterceptors(history);

          if (!user.isVerified) {
            history.push("/verify");
          } else {
            history.push("/");
          }
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
    <Form className="security-form" onSubmit={handleSubmit}>
      <h2 className="font-weight-bold text-center overflow-hidden">Вход</h2>
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
          Нямате профил? <NavLink to="/register">Регистрирайте се!</NavLink>
        </p>
      </Form.Group>
      {unauthorized && (
        <div
          style={{ color: "#dc3545", fontSize: "80%", marginBottom: "1rem" }}
        >
          Сесията е изтекла!
        </div>
      )}
      <IndigoButton variant="primary" type="submit" block>
        Вход
      </IndigoButton>
    </Form>
  );
}

export default Login;
