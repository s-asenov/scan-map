import React from "react";
import "./Demo.css";
import Unity, { UnityContent } from "react-unity-webgl";
import { AiOutlineFullscreen } from "react-icons/ai";
import { ImExit } from "react-icons/im";

/**
 * The component will contain a webgl demo of the project
 */
function Demo() {
  const unityContent = new UnityContent(
    "demo/Demo.json",
    "demo/UnityLoader.js"
  );

  const handleOnClickFullscreen = () => {
    unityContent.setFullscreen(true);
  };

  return (
    <React.Fragment>
      <ImExit
        size="2.75em"
        title="Обратно към сайта"
        color="var(--danger)"
        className="cursor-pointer font-weight-bold ml-auto mr-4 my-4"
        onClick={() => window.location.replace(process.env.BASE_URL)}
      />
      <div style={{ height: "80vh" }}>
        <Unity unityContent={unityContent} width="100%" height="100%" />
      </div>
      <AiOutlineFullscreen
        size="3em"
        title="Цял екран!"
        color="var(--primary)"
        className="cursor-pointer font-weight-bold ml-auto mr-4 my-4"
        onClick={() => handleOnClickFullscreen()}
      />
    </React.Fragment>
  );
}

export default Demo;
