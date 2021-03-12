import { useFormik } from "formik";
import React, { useRef, useState } from "react";
import { Form } from "react-bootstrap";
import apiInstance from "../../../helpers/api/apiInstance";
import { toBase64 } from "../../../helpers/helper";
import IndigoButton from "../../Buttons/IndigoButton";
import FormInvalidFeedback from "../../Utils/FormInvalidFeedback";

const initialValues = {
  model: "",
  modelName: "",
  input: "",
  file: undefined,
};

function ModelAdder() {
  const [plant, setPlant] = useState();
  const [plants, setPlants] = useState([]);
  const ref = useRef(null);
  const timeout = useRef(null);

  const modelFormik = useFormik({
    initialValues,
    onSubmit: async (values, props) => {
      const file = ref.current.files[0];

      if (!file) {
        props.setErrors({
          model: "Can't be empty!",
        });

        return;
      }

      const base64 = await toBase64(file);

      const content = base64.split("base64,")[1];

      if (plant === undefined) {
        props.setErrors({
          input: "Find and choose a plant from the list!",
        });

        return;
      }
      console.log({ modelName: file.name, model: content });
      apiInstance
        .post(`admin/models/${plant}`, {
          modelName: file.name,
          model: content,
        })
        .then(() => {
          window.location.reload();
        })
        .catch((error) => {
          const response = error.response;
          console.log(response);
          if (response.status === 400) {
            props.setErrors({
              model: "Unexpected error",
            });
          }
        });
    },
  });

  const handleInput = (e) => {
    if (timeout.current) {
      clearTimeout(timeout.current);
    }

    const value = e.target.value;

    modelFormik.setFieldValue("input", value);

    timeout.current = setTimeout(() => {
      apiInstance
        .post("plants/find", { input: value })
        .then((response) => {
          setPlants(response.data.plants);
        })
        .catch((error) => {
          const response = error.response;

          if (response.status === 400) {
            props.setErrors({
              input: "Unexpected error",
            });
          }
        });
    }, 500);
  };

  return (
    <div className="flex-1">
      <Form className="mt-5 px-5" onSubmit={modelFormik.handleSubmit}>
        <Form.Group>
          <Form.Label>Търси растение по научно име</Form.Label>
          <Form.Control
            type="text"
            placeholder="Научно име"
            onChange={handleInput}
            value={modelFormik.values.input}
            isInvalid={modelFormik.errors.input}
          />
          <FormInvalidFeedback error={modelFormik.errors.input} />
        </Form.Group>
        <Form.Control
          as="select"
          className="mb-3"
          onChange={(e) => setPlant(e.currentTarget.value)}
          value={plant}
        >
          <option>Избери растение</option>
          {plants.map((item, id) => (
            <option key={id} value={item.id}>
              {item.scientificName}
            </option>
          ))}
        </Form.Control>
        <Form.File className="mb-3" id="model">
          <Form.File.Input
            ref={ref}
            value={modelFormik.values.file}
            isInvalid={!!modelFormik.errors.model}
          />
          <FormInvalidFeedback error={modelFormik.errors.model} />
          <Form.Text className="text-muted">
            {modelFormik.values.modelName}
          </Form.Text>
        </Form.File>
        <IndigoButton type="submit">Add</IndigoButton>
      </Form>
    </div>
  );
}

export default ModelAdder;
