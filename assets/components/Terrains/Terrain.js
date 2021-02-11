import React, { useEffect, useReducer, useRef } from "react";
import apiInstance from "../../helpers/api/apiInstance";
import gray from "../../images/gray.jpg";
import { IoKeySharp } from "react-icons/io5";
import { IoIosListBox } from "react-icons/io";
import { FiClipboard } from "react-icons/fi";
import Col from "react-bootstrap/Col";
import Image from "react-bootstrap/Image";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Popover from "react-bootstrap/Popover";
import TerrainKeyReducer from "../Utils/reducers/List/TerrainKey/TerrainKeyReducer";
import {
  SET_ADD,
  SET_KEYS,
  SET_SHOW,
} from "../Utils/reducers/List/TerrainKey/TerrainKeyActions";

const TerrainList = ({ keys, loaded }) => {
  const ref = useRef(null);

  useEffect(() => {
    if (loaded) {
      const objDiv = ref.current;

      objDiv.scrollTop = objDiv.scrollHeight;
    }
  }, [loaded]);

  const handleClipboard = (id) => {
    var range = document.createRange();
    var selection = window.getSelection();
    range.selectNodeContents(document.querySelector(`.terrain-${id}`));

    selection.removeAllRanges();
    selection.addRange(range);

    document.execCommand("copy");
  };

  return (
    <Popover.Content ref={ref} className="key-list">
      {keys.map((key, index) => (
        <div key={index} className="d-flex key">
          <p className={`terrain-${key.id} float-left`}>{key.id}</p>
          {document.queryCommandSupported("copy") && (
            <FiClipboard
              color={"#007bff"}
              style={{
                marginLeft: "auto",
                marginRight: "1rem",
                fontSize: "1.5rem",
                float: "right",
                cursor: "pointer",
              }}
              title="Копирай!"
              onClick={() => handleClipboard(key.id)}
            />
          )}
        </div>
      ))}
    </Popover.Content>
  );
};

const initialState = {
  show: false,
  disabled: false,
  keys: [],
  loaded: false,
};

const Terrain = (props) => {
  const [state, dispatch] = useReducer(TerrainKeyReducer, initialState);

  const { show, disabled, keys, loaded } = state;
  const { id, name, terrainKeys, imageDirectory } = props.terrain;

  useEffect(() => {
    dispatch({ type: SET_KEYS, payload: { keys: terrainKeys } });
    dispatch({ type: SET_SHOW, payload: false });
  }, [props.terrain]);

  const handleAddKey = (e) => {
    e.target.setAttribute("disabled", "disabled");

    if (disabled) {
      return;
    }

    dispatch({
      type: SET_ADD,
      payload: {
        disabled: true,
        show: true,
      },
    });

    apiInstance.post(`keys/${id}`).then((response) => {
      dispatch({
        type: SET_KEYS,
        payload: { keys: [...keys, response.data.key], loaded: true },
      });
    });

    setTimeout(() => {
      e.target.removeAttribute("disabled");
      dispatch({
        type: SET_ADD,
        payload: {
          disabled: false,
          show: true,
        },
      });
    }, 1500);
  };

  return (
    <Col md="3" className="terrain">
      <Image
        rounded
        src={`uploads/${imageDirectory}`}
        onError={(e) => (e.target.src = gray)}
      />
      <p className="terrain-name mt-2 float-left d-inline-block" title={name}>
        Име: {name}
      </p>
      <div className="float-right mt-2 terrain-btns">
        <IoKeySharp
          className="add-icon"
          color="#FFD700"
          size="1.75em"
          onClick={handleAddKey}
          title="Вземи код!"
        />
        <OverlayTrigger
          trigger="click"
          placement="top"
          show={show}
          onToggle={() => dispatch({ type: SET_SHOW, payload: !show })}
          overlay={
            <Popover id="popover" {...props}>
              <Popover.Title as="h3">Код за избрания терен</Popover.Title>
              {keys.length ? (
                <TerrainList keys={keys} loaded={loaded} />
              ) : (
                <Popover.Content className="text-ceter">
                  Нямате валидни кодове! Генерирайте такъв чрез жълтия ключ!
                </Popover.Content>
              )}
            </Popover>
          }
        >
          <IoIosListBox className="ml-2 mr-0" color="#4b0082" size="1.75em" />
        </OverlayTrigger>
      </div>
    </Col>
  );
};

export default Terrain;
