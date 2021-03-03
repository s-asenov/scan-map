import React, { useEffect, useReducer, useRef } from "react";
import apiInstance from "../../helpers/api/apiInstance";
import gray from "../../images/gray.jpg";
import { IoKeySharp } from "react-icons/io5";
import { IoIosListBox } from "react-icons/io";
import { FiClipboard } from "react-icons/fi";
import { BsFillTrashFill } from "react-icons/bs";
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
import { DELETE } from "../Utils/reducers/List/Terrains/TerrainActions";

const TerrainList = ({ keys, added }) => {
  const ref = useRef(null);

  useEffect(() => {
    if (added) {
      const objDiv = ref.current;

      objDiv.scrollTop = objDiv.scrollHeight;
    }
  }, [added]);

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
        <div
          key={index}
          className={`d-flex key ${key.id === added ? "added" : ""}`}
        >
          <p className={`terrain-${key.id} float-left`}>{key.id}</p>
          {document.queryCommandSupported("copy") && (
            <FiClipboard
              // color={key.id === added ? "#ffffff" : "#007bff"}
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
  added: null,
};

const Terrain = (props) => {
  let timeout = useRef(null);

  const [state, dispatch] = useReducer(TerrainKeyReducer, initialState);

  const { show, disabled, keys, added } = state;
  const { id, name, terrainKeys, imageDirectory } = props.terrain;

  useEffect(() => {
    dispatch({ type: SET_KEYS, payload: { keys: terrainKeys } });
    dispatch({ type: SET_SHOW, payload: false });

    return () => {
      if (timeout.current) {
        clearTimeout(timeout.current);
      }
    };
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
        payload: {
          keys: [...keys, response.data.key],
          added: response.data.key.id,
        },
      });
    });

    timeout.current = setTimeout(() => {
      e.target.removeAttribute("disabled");
      dispatch({
        type: SET_ADD,
        payload: {
          disabled: false,
          show: true,
          added: null,
        },
      });
    }, 3000);
  };

  return (
    <Col md="3" className="terrain">
      <BsFillTrashFill
        id="delete-terrain-icon"
        size="1.75em"
        title="Изтрий генерирания терен!"
        onClick={() => props.deleteTerrain(id)}
      />
      <Image
        rounded
        src={`uploads/${imageDirectory}`}
        onError={(e) => (e.target.src = gray)}
      />
      <div className="terrain-info">
        <p className="terrain-name mt-2" title={name}>
          Име: {name}
        </p>
        <div className="mt-2 terrain-btns">
          <IoKeySharp
            id="add-key-icon"
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
                  <TerrainList keys={keys} added={added} />
                ) : (
                  <Popover.Content className="text-ceter">
                    Нямате валидни кодове! Генерирайте такъв чрез жълтия ключ!
                  </Popover.Content>
                )}
              </Popover>
            }
          >
            <IoIosListBox id="keys-list-icon" color="#4b0082" size="1.75em" />
          </OverlayTrigger>
        </div>
      </div>
    </Col>
  );
};

export default Terrain;
