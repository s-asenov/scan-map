import React, { useContext } from "react";
import "./LoadingModal.css";
import PropTypes from "prop-types";
import Modal from "react-bootstrap/Modal";
import Spinner from "react-bootstrap/Spinner";
import { useHistory } from "react-router-dom";
import MapContext from "../../Utils/context/MapContext";

function LoadingModal(props) {
  const { loading, loaded } = props;
  const context = useContext(MapContext);

  const history = useHistory();

  let content;

  if (loaded) {
    content = <p>Процесът е изпълнен!</p>;

    setTimeout(() => {
      context.setShowAlert(true);
      history.push({
        pathname: "/map",
        map: true,
      });
    }, 1000);
  } else if (loading) {
    content = <p>Моля изчакайте около 1-2 минути, за да завърши процеса!</p>;
  }

  return (
    <Modal
      contentClassName={"loading-modal w-50 mx-auto text-center"}
      centered
      show={loading || loaded}
      backdrop={"static"}
    >
      <Spinner animation="border" variant="indigo" />
      {content}
    </Modal>
  );
}

LoadingModal.proptypes = {
  loaded: PropTypes.bool.isRequired,
  loading: PropTypes.bool.isRequired,
};

export default LoadingModal;
