import React, { useEffect } from "react";
import { Redirect, Route, Switch, useRouteMatch } from "react-router-dom";

/**
 * The Admin component will be responsible for all necessary admin api calls.
 * @todo
 */
function Admin() {
  let { url } = useRouteMatch();

  useEffect(() => {
    // apiInstance.post("/admin/test");
  }, []);

  return (
    // <div className="flex-1">
    <Switch>
      <Route exact path={`${url}/`}>
        <Redirect to={`${url}/dashboard`} />
      </Route>
      <Route exact path={`${url}/dashboard`}>
        <h3>dashboard</h3>
      </Route>
      <Route exact path={`${url}/model`}>
        <h3>to do - page for adding 3d models</h3>
      </Route>
    </Switch>
    // </div>
  );
}

export default Admin;
