import React, { useEffect, useReducer, useState } from "react";
import apiInstance from "../../helpers/api/instance";
import Pagination from "react-js-pagination";
import "./Terrains.css";
import TerrainReducer from "../Utils/reducers/List/Terrain/TerrainReducer";
import {
  SET_CURRENT,
  SET_LOADED,
  SET_MATCHING,
  SET_TERRAINS,
} from "../Utils/reducers/List/Terrain/TerrainActions";
import Row from "react-bootstrap/Row";
import Spinner from "react-bootstrap/Spinner";
import InputGroup from "react-bootstrap/InputGroup";
import FormControl from "react-bootstrap/FormControl";
import Container from "react-bootstrap/Container";
import Terrain from "./Terrain";

const initialState = {
  terrains: [],
  matchingTerrains: [],
  currentTerrains: [],
  page: 1,
  itemsPerPage: 12,
  loaded: false,
};

function Terrains() {
  let _isMounted = true;
  const [state, dispatch] = useReducer(TerrainReducer, initialState);
  const [input, setInput] = useState("");

  const {
    terrains,
    currentTerrains,
    page,
    itemsPerPage,
    matchingTerrains,
    loaded,
  } = state;

  useEffect(() => {
    var urlString = window.location.href; //window.location.href
    var url = new URL(urlString);
    var stringPage = url.searchParams.get("page");

    apiInstance
      .post("terrains")
      .then((response) => {
        const data = response.data.terrains;

        const existingPages = Math.ceil(data.length / itemsPerPage);
        const currentPage =
          parseInt(stringPage) > existingPages ? 1 : parseInt(stringPage);

        if (_isMounted) {
          dispatch({
            type: SET_TERRAINS,
            payload: {
              terrains: data,
              page: currentPage || page,
            },
          });
        }
      })
      .catch((err) => {
        dispatch({
          type: SET_LOADED,
        });
      });

    return () => (_isMounted = false);
  }, []);

  const handlePaginationClick = (page) => {
    var urlString = window.location.href; //window.location.href
    var url = new URL(urlString);
    url.searchParams.set("page", page);

    const newurl = url.toString();
    window.history.pushState({ path: newurl }, "", newurl);

    dispatch({
      type: SET_CURRENT,
      payload: {
        page: page,
      },
    });
  };

  const handleInputField = (e) => {
    setInput(e.target.value);

    dispatch({
      type: SET_MATCHING,
      payload: e.target.value,
    });
  };

  if (!loaded) {
    return (
      <Container className="flex-1 d-flex justify-content-center align-items-center">
        <Spinner
          animation="border"
          style={{
            height: "4rem",
            width: "4rem",
            borderWidth: "0.5em",
            color: "var(--indigo)",
          }}
        />
      </Container>
    );
  }

  return (
    <Container className="flex-1">
      <InputGroup className="my-4 align-items-center">
        <label className="mb-0" htmlFor="search">
          Търсачка
        </label>
        <FormControl
          className="ml-2"
          id="search"
          aria-label="Small"
          value={input}
          onChange={handleInputField}
          aria-describedby="inputGroup-sizing-sm"
        />
      </InputGroup>

      <Row className="mt-5">
        {currentTerrains.length ? (
          currentTerrains.map((terrain, index) => (
            <Terrain key={index} terrain={terrain} />
          ))
        ) : (
          <div className="text-center font-weight-bold">
            Няма намерени карти!
          </div>
        )}
      </Row>
      {matchingTerrains.length / itemsPerPage > 1 && (
        <Pagination
          innerClass="pagination justify-content-center"
          itemClass="page-item"
          linkClass="page-link"
          activePage={page}
          itemsCountPerPage={itemsPerPage}
          totalItemsCount={matchingTerrains.length}
          pageRangeDisplayed={5}
          onChange={handlePaginationClick}
        />
      )}
    </Container>
  );
}

export default Terrains;
