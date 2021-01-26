import React, { useEffect, useState } from "react";
import apiInstance from "../../helpers/api/apiInstance";
import gray from "../../images/gray.jpg";
import { IoKeySharp } from "react-icons/io5";
import { IoIosListBox } from "react-icons/io";
import { FiClipboard } from "react-icons/fi";
import Col from "react-bootstrap/Col";
import Image from "react-bootstrap/Image";
import OverlayTrigger from "react-bootstrap/OverlayTrigger";
import Popover from "react-bootstrap/Popover";

const TerrainList = ({ keys }) => {
  const handleClipboard = (id) => {
    var range = document.createRange();
    var selection = window.getSelection();
    range.selectNodeContents(document.querySelector(`.terrain-${id}`));

    selection.removeAllRanges();
    selection.addRange(range);

    document.execCommand("copy");
  };

  return (
    <Popover.Content className="key-list">
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

const Terrain = (props) => {
  const { id, name, terrainKeys, imageDirectory } = props.terrain;
  const [keys, setKeys] = useState([]);

  useEffect(() => {
    setKeys(terrainKeys);
  }, [props.terrain]);

  const handleAddKey = () => {
    apiInstance.post(`keys/${id}`).then((response) => {
      setKeys([...keys, response.data.key]);
    });
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
          color="#FFD700"
          size="1.75rem"
          onClick={handleAddKey}
          title="Вземи код!"
        />
        <OverlayTrigger
          trigger="click"
          placement="top"
          overlay={
            <Popover id="popover" {...props}>
              <Popover.Title as="h3">Код за избрания терен</Popover.Title>
              {keys.length ? (
                <TerrainList keys={keys} />
              ) : (
                <Popover.Content className="text-ceter">
                  Нямате валидни кодове! Генерирайте такъв чрез жълтия ключ!
                </Popover.Content>
              )}
            </Popover>
          }
        >
          <IoIosListBox className="ml-2 mr-0" color="#4b0082" size="1.75rem" />
        </OverlayTrigger>
      </div>
    </Col>
  );
};

export default Terrain;
