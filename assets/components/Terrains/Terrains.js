import React, { useEffect, useState } from "react";
import apiInstance from "../../helpers/api/instance";
import "./Terrains.css";

const Terrain = (props) => {
  return (
    <div>
      <p>{props.terrain.id}</p>
      <p>{props.terrain.zipName}</p>
      <p>{props.terrain.user}</p>
    </div>
  );
};

// 'id' => $object->getId(),
//             'zipName' => $object->getZipName(),
//             'user' => $object->getUser()->getId(),
//             'terrainKeys' => $keys

function Terrains() {
  let _isMounted = true;
  const [terrains, setTerrains] = useState();

  useEffect(() => {
    apiInstance.post("terrains").then((response) => {
      const data = response.data.terrains;

      if (_isMounted) {
        setTerrains(data);
      }
    });

    return () => (_isMounted = false);
  }, []);

  if (!terrains) {
    return <div style={{ flex: "1" }}></div>;
  }

  return (
    <div style={{ flex: "1" }}>
      {terrains.map((terrain, index) => (
        <Terrain key={index} terrain={terrain} />
      ))}
    </div>
  );
}

export default Terrains;
